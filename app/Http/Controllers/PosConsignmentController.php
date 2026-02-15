<?php

namespace App\Http\Controllers;

use App\Models\PosConsignment;
use App\Models\PosConsignmentItem;
use App\Models\PosSale;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductCost; // الموديل الجديد للتكلفة
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosConsignmentController extends Controller
{
    /**
     * عرض قائمة العهد (نقاط البيع)
     */
    public function index()
    {
        $consignments = PosConsignment::withCount('items')->latest()->paginate(10);

        return view('pos.index', compact('consignments'));
    }

    /**
     * صفحة إنشاء عهدة جديدة
     */
    public function create()
    {
        $products = Product::where('quantity', '>', 0)->get();
        $priceLists = PriceList::with('items')->get();

        return view('pos.create', compact('products', 'priceLists'));
    }

    /**
     * حفظ العهدة الجديدة في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $request->validate([
            'pos_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // حساب إجمالي العهدة
                $calculatedTotal = collect($request->items)->sum(function ($item) {
                    return $item['qty'] * $item['price'];
                });

                // إنشاء رأس العهدة ورقمها
                $consignment = PosConsignment::create([
                    'consignment_number' => 'POS-'.now()->format('Ymd').'-'.str_pad(
                        PosConsignment::whereDate('created_at', now()->toDateString())->lockForUpdate()->count() + 1,
                        4, '0', STR_PAD_LEFT
                    ),
                    'pos_name' => $request->pos_name,
                    'notes' => $request->notes,
                    'total_amount' => $calculatedTotal,
                ]);

                foreach ($request->items as $itemData) {
                    $product = Product::lockForUpdate()->find($itemData['product_id']);

                    if ($product->quantity < $itemData['qty']) {
                        throw new \Exception("المخزن لا يكفي للصنف: {$product->name}");
                    }

                    // --- جلب التكلفة من جدول ProductCost ---
                    $costRecord = ProductCost::where('product_id', $product->id)->first();
                    $unitCost = $costRecord ? $costRecord->weighted_average_cost : 0;

                    // إنشاء تفاصيل العهدة وتخزين التكلفة
                    $consignment->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_code' => $product->code,
                        'product_image' => $product->image,
                        'delivered_qty' => $itemData['qty'],
                        'sold_qty' => 0,
                        'unit_price' => $itemData['price'],
                        'unit_cost' => $unitCost, // الحقل الجديد
                    ]);

                    // خصم من مخزن الإدارة الرئيسي
                    $product->decrement('quantity', $itemData['qty']);
                }

                return redirect()->route('pos.index')->with('success', 'تم شحن العهدة وتثبيت التكلفة بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * عرض تفاصيل عهدة معينة (صفحة البيع والإضافة)
     */
    public function show($id)
    {
        $consignment = PosConsignment::with(['items.sales'])->findOrFail($id);
        $priceLists = PriceList::with('items')->get();
        $allProducts = Product::where('quantity', '>', 0)->get();

        return view('pos.show', compact('consignment', 'allProducts', 'priceLists'));
    }

    /**
     * إضافة بضاعة إضافية لعهدة قائمة
     */
    public function addMoreStock(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $consignment = PosConsignment::findOrFail($id);
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            if ($product->quantity < $request->qty) {
                throw new \Exception('الكمية في المخزن غير كافية');
            }

            // جلب التكلفة الحالية قبل الشحن
            $costRecord = ProductCost::where('product_id', $product->id)->first();
            $currentCost = $costRecord ? $costRecord->weighted_average_cost : 0;

            $existingItem = $consignment->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $existingItem->increment('delivered_qty', $request->qty);
                // تحديث السعر والتكلفة بناءً على آخر شحنة واصلة
                $existingItem->update([
                    'unit_price' => $request->price,
                    'unit_cost' => $currentCost,
                ]);
            } else {
                $consignment->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'product_image' => $product->image,
                    'delivered_qty' => $request->qty,
                    'unit_price' => $request->price,
                    'unit_cost' => $currentCost,
                    'sold_qty' => 0,
                ]);
            }

            $product->decrement('quantity', $request->qty);
            DB::commit();

            return back()->with('success', 'تم تزويد العهدة بالبضاعة وتحديث التكلفة');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * تسجيل عملية بيع من داخل العهدة
     */
    public function storeSale(Request $request, $itemId)
    {
        $request->validate([
            'quantity_sold' => 'required|numeric|min:1',
            'sale_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $item = PosConsignmentItem::findOrFail($itemId);
            $qtyToSell = (int) $request->quantity_sold;

            // فحص الكمية المتاحة في العهدة
            $remaining = $item->delivered_qty - $item->sold_qty;
            if ($qtyToSell > $remaining) {
                throw new \Exception('الكمية المتاحة في العهدة فقط: '.$remaining);
            }

            // 1. إنشاء سجل البيع
            // ملاحظة: هنا نستخدم السعر المخزن في العهدة (unit_price)
            $sale = new PosSale;
            $sale->consignment_item_id = $item->id;
            $sale->quantity_sold = $qtyToSell;
            $sale->unit_price = $item->unit_price;
            $sale->total_amount = $qtyToSell * $item->unit_price;
            $sale->sale_date = $request->sale_date;
            $sale->save();

            // 2. تحديث الكمية المباعة في العهدة
            $item->increment('sold_qty', $qtyToSell);

            DB::commit();

            return back()->with('success', 'تم تسجيل البيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'فشل الحفظ: '.$e->getMessage()]);
        }
    }
}
