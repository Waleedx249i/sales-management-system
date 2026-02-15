<?php

namespace App\Http\Controllers;

use App\Models\ImportInvoice;
use App\Models\Product;
use App\Models\ProductCost; // تأكد من استدعاء الموديل الجديد
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ImportInvoice::query()->withCount('items');

        // 1. الفرز حسب رقم الفاتورة
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%'.$request->search.'%');
        }

        // 2. الفرز حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. الفرز الذكي حسب التاريخ
        if ($request->filled('filter_type')) {
            $today = now()->startOfDay();

            switch ($request->filter_type) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereBetween('created_at', [
                            $request->from_date.' 00:00:00',
                            $request->to_date.' 23:59:59',
                        ]);
                    }
                    break;
            }
        }

        $invoices = $query->latest()->paginate(15);

        return view('ImportInvoices.index', compact('invoices'));
    }

    // 2. عرض تفاصيل فاتورة واحدة
    public function show($id)
    {
        $invoice = ImportInvoice::with('items')->findOrFail($id);

        return view('Importinvoices.show', compact('invoice'));
    }

    public function create()
    {
        $products = Product::all(); // جلب كافة المنتجات للاستيراد
        $suppliers = \App\Models\Supplier::all(); // جلب الموردين لاختيارهم في الفاتورة

        return view('Importinvoices.create', compact('products', 'suppliers'));
    }

    // 3. حفظ الفاتورة ومعالجة التكاليف والمخزن
    public function store(Request $request)
    {
        $request->validate([
            'exchange_rate' => 'required|numeric',
            'status' => 'required|in:completed,pending',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء رأس الفاتورة
            $invoice = ImportInvoice::create([
                'exchange_rate' => $request->exchange_rate,
                'supplier_id' => $request->supplier_id, // تأكد من إضافة حقل المورد في النموذج
                'total_goods_sdg' => (float) str_replace(',', '', $request->total_goods_sdg),
                'total_logistic' => (float) str_replace(',', '', $request->total_logistic),
                'cost_ratio_percent' => str_replace(['+', '%'], '', $request->cost_ratio_display),
                'status' => $request->status,
            ]);

            foreach ($request->items as $itemData) {
                if (! isset($itemData['product_id'])) {
                    continue;
                }

                $newQty = (int) $itemData['qty'];
                $newCost = (float) str_replace(',', '', $itemData['unit_cost']);

                // حفظ الصنف في الفاتورة
                $invoice->items()->create([
                    'product_id' => $itemData['product_id'],
                    'item_name' => $itemData['name'],
                    'item_code' => $itemData['code'],
                    'price_egp' => $itemData['price_egp'],
                    'quantity' => $newQty,
                    'final_unit_cost' => $newCost,
                ]);

                // إذا كانت الفاتورة مكتملة: تحديث المخزن وحساب التكلفة المتوسطة
                if ($request->status === 'completed') {
                    $product = Product::findOrFail($itemData['product_id']);

                    // جلب سجل التكلفة الحالي أو إنشاء واحد جديد (علاقة One-to-One)
                    $costRecord = ProductCost::firstOrCreate(['product_id' => $product->id]);

                    $oldQty = (int) $product->quantity;
                    $oldCost = (float) $costRecord->weighted_average_cost;

                    // الحسابات داخل الكنترولر (المتوسط المرجح)
                    $totalQty = $oldQty + $newQty;
                    if ($totalQty > 0) {
                        $finalAverage = (($oldQty * $oldCost) + ($newQty * $newCost)) / $totalQty;
                    } else {
                        $finalAverage = $newCost;
                    }

                    // تحديث سجل التكلفة النهائي فقط
                    $costRecord->update([
                        'weighted_average_cost' => round($finalAverage, 2),
                    ]);

                    // زيادة كمية المنتج في المخزن
                    $product->increment('quantity', $newQty);
                }
            }

            DB::commit();

            return redirect()->route('import-invoices.index')->with('success', 'تم حفظ الفاتورة وحساب التكاليف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'حدث خطأ: '.$e->getMessage()]);
        }
    }

    // 4. تحديث حالة الفاتورة (من معلقة إلى مكتملة)
    public function updateStatus(Request $request, $id)
    {
        $invoice = ImportInvoice::with('items')->findOrFail($id);
        $newStatus = $request->status;

        if ($invoice->status === $newStatus) {
            return back()->with('info', 'الحالة هي نفسها بالفعل.');
        }

        try {
            DB::beginTransaction();

            if ($newStatus === 'completed') {
                foreach ($invoice->items as $item) {
                    $product = Product::findOrFail($item->product_id);
                    $costRecord = ProductCost::firstOrCreate(['product_id' => $product->id]);

                    $oldQty = (int) $product->quantity;
                    $oldCost = (float) $costRecord->weighted_average_cost;
                    $newQty = (int) $item->quantity;
                    $newCost = (float) $item->final_unit_cost;

                    // حساب المتوسط المرجح
                    $totalQty = $oldQty + $newQty;
                    $finalAverage = $totalQty > 0 ? (($oldQty * $oldCost) + ($newQty * $newCost)) / $totalQty : $newCost;

                    // تحديث التكلفة والمخزن
                    $costRecord->update(['weighted_average_cost' => round($finalAverage, 2)]);
                    $product->increment('quantity', $newQty);
                }
            }
            // ملاحظة: التحويل من مكتمل لمسودة يتطلب منطق عكسي دقيق (إنقاص مخزن)
            elseif ($invoice->status === 'completed' && $newStatus === 'pending') {
                foreach ($invoice->items as $item) {
                    $product = Product::findOrFail($item->product_id);
                    if ($product->quantity >= $item->quantity) {
                        $product->decrement('quantity', $item->quantity);
                    }
                }
            }

            $invoice->update(['status' => $newStatus]);

            DB::commit();

            return back()->with('success', 'تم تحديث الحالة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 5. حذف الفاتورة
    public function destroy($id)
    {
        $invoice = ImportInvoice::with('items')->findOrFail($id);

        try {
            DB::beginTransaction();

            // إذا كانت مكتملة، نعيد الكميات للمخزن قبل الحذف
            if ($invoice->status === 'completed') {
                foreach ($invoice->items as $item) {
                    Product::where('id', $item->product_id)->decrement('quantity', $item->quantity);
                }
            }

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            return redirect()->route('import-invoices.index')->with('success', 'تم حذف الفاتورة وتعديل المخزن.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'خطأ في الحذف: '.$e->getMessage()]);
        }
    }

    // دالة عرض الفاتورة للطباعة
    public function print($id)
    {
        // جلب الفاتورة مع أصنافها
        $invoice = \App\Models\ImportInvoice::with('items')->findOrFail($id);

        return view('ImportInvoices.print', compact('invoice'));
    }
}
