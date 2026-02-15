<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * عرض قائمة الموردين (Index)
     */
    public function index()
    {
        // جلب الموردين مع حساب عدد الفواتير لكل مورد لسرعة العرض
        $suppliers = Supplier::withCount('importInvoices')->latest()->get();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * صفحة إضافة مورد جديد (Create)
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * حفظ المورد الجديد (Store)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|unique:suppliers,phone',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'يرجى إدخال اسم المورد أو الشركة',
            'phone.required' => 'رقم الهاتف مطلوب للضرورة',
            'phone.unique' => 'رقم الهاتف هذا مسجل لمورد آخر بالفعل',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'تم تسجيل المورد الجديد بنجاح في النظام');
    }

    /**
     * عرض تفاصيل المورد وكشف حساب فواتيره (Show)
     */
    public function show(Supplier $supplier)
    {
        // جلب الفواتير المرتبطة بهذا المورد مرتبة من الأحدث
        $supplier->load(['importInvoices' => function ($query) {
            $query->latest();
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * صفحة تعديل بيانات المورد (Edit)
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * تحديث بيانات المورد (Update)
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|unique:suppliers,phone,'.$supplier->id,
            'description' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    /**
     * حذف مورد (Destroy)
     */
    public function destroy(Supplier $supplier)
    {
        // حماية: منع الحذف إذا كان هناك فواتير مرتبطة
        if ($supplier->importInvoices()->count() > 0) {
            return back()->with('error', 'عفواً، لا يمكن حذف هذا المورد لوجود فواتير توريد مسجلة باسمه.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'تم حذف المورد من النظام');
    }
}
