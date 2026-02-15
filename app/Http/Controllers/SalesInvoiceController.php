<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductCost;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesInvoice::query();

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhere('customer_name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        $invoices = $query->latest()->paginate(15);

        return view('sales.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::all(); // عرض الكل للسماح بالمسودات
        $priceLists = PriceList::where('is_active', true)->with('items')->get();

        return view('sales.create', compact('products', 'priceLists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'status' => 'required|in:paid,partial,pending',
            'paid_amount' => 'nullable|numeric|min:0',
            'action' => 'required|in:draft,approve', // تحديد نوع الإجراء من الفرونت إند
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $isApproved = ($request->action === 'approve');

                // 1. توليد رقم الفاتورة اليومي
                $todayCount = SalesInvoice::whereDate('created_at', now()->toDateString())->count();
                $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

                // 2. حساب المبالغ
                $finalAmount = $request->final_amount;
                $paidAmount = 0;

                if ($isApproved) {
                    if ($request->status == 'paid') {
                        $paidAmount = $finalAmount;
                    } elseif ($request->status == 'partial') {
                        $paidAmount = $request->paid_amount ?? 0;
                    }
                }

                // 3. إنشاء الفاتورة
                $invoice = SalesInvoice::create([
                    'invoice_number' => $invoiceNumber,
                    'customer_name' => $request->customer_name ?? 'عميل نقدي',
                    'total_amount' => $request->total_amount,
                    'discount' => $request->discount ?? 0,
                    'final_amount' => $finalAmount,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $finalAmount - $paidAmount,
                    'status' => $request->status ? $request->status : 'pending', // حالة خاصة للمسودة
                    'is_approved' => $isApproved, // افترض وجود هذا الحقل في قاعدة البيانات
                ]);

                // 4. معالجة الأصناف
                foreach ($request->items as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);

                    // خصم المخزن فقط في حالة التصديق
                    if ($isApproved) {
                        $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                        if ($product->quantity < $itemData['qty']) {
                            throw new \Exception("الكمية غير كافية للمنتج: {$product->name}");
                        }
                        $product->decrement('quantity', $itemData['qty']);
                    }

                    $cost = ProductCost::where('product_id', $product->id)->first()?->weighted_average_cost ?? 0;

                    $invoice->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $itemData['qty'],
                        'unit_price' => $itemData['price'],
                        'unit_cost' => $cost,
                        'subtotal' => $itemData['qty'] * $itemData['price'],
                    ]);
                }

                // 5. تسجيل أول دفعة (فقط في حالة التصديق)
                if ($isApproved && $paidAmount > 0) {
                    $invoice->payments()->create([
                        'amount' => $paidAmount,
                        'payment_date' => now(),
                        'payment_method' => 'Cash',
                        'notes' => 'الدفعة الأولى عند إنشاء الفاتورة (مصدقة)',
                    ]);
                }

                $msg = $isApproved ? "تم تصديق الفاتورة {$invoiceNumber} وخصم المخزن" : "تم حفظ الفاتورة {$invoiceNumber} كمسودة";

                return redirect()->route('sales.index')->with('success', $msg);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // دالة جديدة لتصديق مسودة قائمة
    public function approveDraft($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $invoice = SalesInvoice::with('items')->lockForUpdate()->findOrFail($id);

                if ($invoice->is_approved) {
                    throw new \Exception('هذه الفاتورة مصدقة بالفعل.');
                }

                foreach ($invoice->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);
                    if ($product->quantity < $item->quantity) {
                        throw new \Exception("الكمية غير كافية حالياً للمنتج: {$product->name}");
                    }
                    $product->decrement('quantity', $item->quantity);
                }

                $invoice->update([
                    'is_approved' => true,
                    'status' => 'pending', // أو الحالة المختارة افتراضياً عند التصديق
                ]);

                return back()->with('success', 'تم تصديق المسودة وتحديث المخزن بنجاح.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with(['items.product', 'payments'])->findOrFail($id);

        return view('sales.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);

        // منع التعديل إذا كانت الفاتورة مصدقة (اختياري حسب رغبتك)
        // if ($invoice->is_approved) return back()->withErrors(['error' => 'لا يمكن تعديل فاتورة مصدقة']);

        $products = Product::all();
        $priceLists = PriceList::all();

        return view('sales.edit', compact('invoice', 'products', 'priceLists'));
    }

    public function update(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $invoice = SalesInvoice::with('items')->lockForUpdate()->findOrFail($id);
                $isApproved = ($request->action === 'approve' || $invoice->is_approved);

                // أ. إعادة الكميات للمخزن فقط إذا كانت الفاتورة الأصلية "مصدقة"
                if ($invoice->is_approved) {
                    foreach ($invoice->items as $oldItem) {
                        Product::where('id', $oldItem->product_id)->increment('quantity', $oldItem->quantity);
                    }
                }

                $invoice->items()->delete();

                // ب. تحديث البيانات وحساب الخصم المخزني الجديد إذا طلب التصديق
                foreach ($request->items as $itemData) {
                    if ($isApproved) {
                        $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                        if ($product->quantity < $itemData['qty']) {
                            throw new \Exception("المخزن لا يكفي: {$product->name}");
                        }
                        $product->decrement('quantity', $itemData['qty']);
                    }

                    $invoice->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['qty'],
                        'unit_price' => $itemData['price'],
                        'subtotal' => $itemData['qty'] * $itemData['price'],
                    ]);
                }

                $invoice->update([
                    'customer_name' => $request->customer_name ?? 'عميل نقدي',
                    'total_amount' => $request->total_amount,
                    'discount' => $request->discount ?? 0,
                    'final_amount' => $request->final_amount,
                    'is_approved' => $isApproved,
                    'status' => $isApproved ? ($request->status ?? $invoice->status) : 'draft',
                ]);

                return redirect()->route('sales.show', $id)->with('success', 'تم تحديث الفاتورة بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            return DB::transaction(function () use ($request, $id) {
                $invoice = SalesInvoice::lockForUpdate()->findOrFail($id);

                if (! $invoice->is_approved) {
                    throw new \Exception('لا يمكن إضافة دفعات لمسودة. يجب تصديق الفاتورة أولاً.');
                }

                if ($request->amount > $invoice->remaining_amount) {
                    throw new \Exception('المبلغ المدفوع أكبر من المتبقي!');
                }

                $invoice->payments()->create([
                    'amount' => $request->amount,
                    'payment_date' => now(),

                ]);

                $invoice->increment('paid_amount', $request->amount);
                $invoice->decrement('remaining_amount', $request->amount);

                $newStatus = ($invoice->remaining_amount <= 0) ? 'paid' : 'partial';
                $invoice->update(['status' => $newStatus]);

                return back()->with('success', 'تم تسجيل الدفعة بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 1. دالة التصديق (Approve)
    public function approve($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $invoice = SalesInvoice::with('items')->lockForUpdate()->findOrFail($id);

                if ($invoice->is_approved) {
                    return back()->with('info', 'هذه الفاتورة مصدقة بالفعل');
                }

                // تنفيذ خصم الكميات من المخزن الآن
                foreach ($invoice->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->quantity < $item->quantity) {
                        throw new \Exception("الكمية غير كافية في المخزن للمنتج: {$product->name}");
                    }

                    $product->decrement('quantity', $item->quantity);
                }

                // تحديث حالة الفاتورة لتصبح مصدقة
                $invoice->update([
                    'is_approved' => true,
                    'status' => ($invoice->remaining_amount <= 0) ? 'paid' : 'pending',
                ]);

                return back()->with('success', 'تم تصديق الفاتورة وخصم الكميات من المخزن بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 2. دالة الحذف (Destroy)
    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $invoice = SalesInvoice::with('items')->findOrFail($id);

                // إذا كانت الفاتورة مصدقة، يجب إعادة الكميات للمخزن قبل الحذف
                if ($invoice->is_approved) {
                    foreach ($invoice->items as $item) {
                        Product::where('id', $item->product_id)->increment('quantity', $item->quantity);
                    }
                }

                // حذف الأصناف والدفعات المرتبطة ثم الفاتورة
                $invoice->items()->delete();
                $invoice->payments()->delete();
                $invoice->delete();

                return back()->with('success', 'تم حذف الفاتورة بنجاح وإعادة الكميات للمخزن (إذا كانت مصدقة)');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'فشل الحذف: '.$e->getMessage()]);
        }
    }
}
