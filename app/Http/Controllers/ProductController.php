<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('products.index', compact('products'));
    }

    /**
     * واجهة إضافة منتج جديد
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * حفظ المنتج الجديد في القاعدة والمجلد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|unique:products,code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity'    => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'code.unique' => 'كود المنتج هذا مسجل مسبقاً.',
            'image.image' => 'الملف المرفق يجب أن يكون صورة.',
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                // إنشاء اسم فريد للصورة
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // التأكد من وجود المجلد، وإذا لم يوجد يتم إنشاؤه بصلاحيات كاملة
                $destinationPath = public_path('uploads/products');
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                // نقل الصورة للمجلد العام مباشرة
                $image->move($destinationPath, $imageName);
                
                // حفظ المسار في مصفوفة البيانات
                $validated['image'] = 'uploads/products/' . $imageName;
            }

            Product::create($validated);

            return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل المنتج (الواجهة التي صممناها)
     */
    public function show(Product $product)
    {
        // لضمان تحميل العلاقات (الملحقات)
        $product->load(['priceListItems.priceList', 'cost']);
        return view('products.show', compact('product'));
    }

    /**
     * واجهة تعديل المنتج
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * تحديث بيانات المنتج وصورته
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code'        => 'required|unique:products,code,' . $product->id,
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity'    => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {
                // 1. حذف الصورة القديمة من المجلد إذا كانت موجودة
                if ($product->image && File::exists(public_path($product->image))) {
                    File::delete(public_path($product->image));
                }

                // 2. رفع الصورة الجديدة بنفس الطريقة المضمونة
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $imageName);
                
                $validated['image'] = 'uploads/products/' . $imageName;
            }

            $product->update($validated);

            return redirect()->route('products.index')->with('success', 'تم تحديث بيانات المنتج بنجاح.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'فشل التحديث: ' . $e->getMessage());
        }
    }

    /**
     * حذف المنتج وصورته نهائياً
     */
    public function destroy(Product $product)
    {
        try {
            // حذف الصورة المرتبطة من المجلد أولاً
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $product->delete();

            return redirect()->route('products.index')->with('success', 'تم حذف المنتج وصورته بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'تعذر حذف المنتج: ' . $e->getMessage());
        }
    }
}