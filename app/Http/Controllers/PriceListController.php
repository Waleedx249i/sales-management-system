<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
    public function index()
    {
        $priceLists = PriceList::withCount('items')->latest()->get();

        return view('price_lists.index', compact('priceLists'));
    }

    public function show($id)
    {
        $priceList = PriceList::with('items.product')->findOrFail($id);

        return view('price_lists.show', compact('priceList'));
    }

    public function create()
    {
        $products = Product::all();

        return view('price_lists.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        DB::transaction(function () use ($request) {
            $priceList = PriceList::create(['name' => $request->name]);

            foreach ($request->prices as $productId => $data) {
                if (! empty($data['price'])) {
                    $priceList->items()->create([
                        'product_id' => $productId,
                        'price' => $data['price'],
                    ]);
                }
            }
        });

        return redirect()->route('price-lists.index')->with('success', 'تم إنشاء قائمة الأسعار بنجاح');
    }

    public function edit($id)
    {
        $priceList = PriceList::with('items')->findOrFail($id);
        $products = Product::all();
        // تحويل الأسعار الحالية لمصفوفة ليسهل عرضها في الـ view
        $currentPrices = $priceList->items->pluck('price', 'product_id')->toArray();

        return view('price_lists.edit', compact('priceList', 'products', 'currentPrices'));
    }

    public function update(Request $request, $id)
    {
        $priceList = PriceList::findOrFail($id);

        DB::transaction(function () use ($request, $priceList) {
            $priceList->update(['name' => $request->name]);

            // مسح الأسعار القديمة وإعادة إضافتها (أو استخدام updateOrCreate)
            $priceList->items()->delete();

            foreach ($request->prices as $productId => $data) {
                if (! empty($data['price'])) {
                    $priceList->items()->create([
                        'product_id' => $productId,
                        'price' => $data['price'],
                    ]);
                }
            }
        });

        return redirect()->route('price-lists.index')->with('success', 'تم تحديث القائمة بنجاح');
    }

    public function toggleStatus($id)
    {
        $priceList = PriceList::findOrFail($id);
        $priceList->is_active = ! $priceList->is_active;
        $priceList->save();

        return back()->with('success', 'تم تغيير حالة القائمة');
    }

    public function destroy($id)
    {
        PriceList::findOrFail($id)->delete();

        return back()->with('success', 'تم حذف القائمة');
    }
}
