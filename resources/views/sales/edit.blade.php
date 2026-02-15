@extends('layouts.app', ['title' => 'تعديل الفاتورة'])

@section('content')
<div class="max-w-[100rem] mx-auto p-4">
    <form action="{{ route('sales.update', $invoice->id) }}" method="POST" id="salesForm">
        @csrf
        @method('PUT')

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-amber-500 p-3 rounded-xl text-white shadow-lg shadow-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor font-black">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800">تعديل فاتورة رقم: {{ $invoice->invoice_number }}</h1>
                    <p class="text-xs text-slate-400 font-bold">تعديل الأصناف سيعيد ضبط المخزن تلقائياً</p>
                </div>
            </div>

            <div class="bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 text-right">
                <span class="block text-[10px] text-slate-400 font-bold uppercase">اسم العميل</span>
                <input type="text" name="customer_name" value="{{ $invoice->customer_name }}" required class="bg-transparent font-black text-slate-700 outline-none text-sm w-48">
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <div class="xl:col-span-3 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden min-h-[450px]">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-[11px] font-black uppercase tracking-wider border-b border-slate-100">
                            <th class="p-4">الصنف / الكود</th>
                            <th class="p-4 text-center w-32">السعر</th>
                            <th class="p-4 text-center w-28">الكمية</th>
                            <th class="p-4 text-center">الإجمالي</th>
                            <th class="p-4 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="sales_body">
                        @foreach($invoice->items as $index => $item)
                        <tr class="item-row border-b border-slate-50 transition-colors">
                            <td class="p-3">
                                <select name="items[{{$index}}][product_id]" class="product-select w-full p-2 bg-slate-50 rounded-lg text-sm font-bold outline-none border-none">
                                    <option value="{{ $item->product_id }}">{{ $item->product->name }}</option>
                                    </select>
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[{{$index}}][price]" value="{{ $item->unit_price }}" step="0.01" 
                                       class="item-price w-full text-center p-2 bg-emerald-50/30 rounded-lg font-black text-emerald-700 outline-none border-none">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[{{$index}}][qty]" value="{{ $item->quantity }}" 
                                       class="item-qty w-full text-center p-2 bg-slate-50 rounded-lg font-bold outline-none border-none">
                            </td>
                            <td class="p-3">
                                <input type="text" readonly class="row-total w-full text-center p-2 bg-transparent font-black text-slate-700 outline-none border-none" 
                                       value="{{ number_format($item->subtotal) }}">
                            </td>
                            <td class="p-3 text-center">
                                <button type="button" class="remove-row text-slate-300 hover:text-red-500 p-2">✕</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">
                    <button type="button" id="addRowBtn" class="text-emerald-600 font-black text-xs uppercase hover:tracking-widest transition-all">
                        + إضافة منتج جديد للفاتورة
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 sticky top-4">
                    <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2">ملخص التعديل</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold text-slate-500 px-2">
                            <span>الإجمالي:</span>
                            <span id="subtotal_display">{{ number_format($invoice->total_amount) }}</span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <label class="block text-[10px] text-slate-400 font-black uppercase mb-1">الخصم (SDG)</label>
                            <input type="number" name="discount" id="discount_input" value="{{ $invoice->discount }}" 
                                   class="w-full bg-transparent text-xl font-black text-red-500 outline-none border-none">
                        </div>
                        <div class="bg-slate-900 p-5 rounded-2xl text-center shadow-xl">
                            <span class="block text-[10px] text-slate-300 font-black mb-1 tracking-widest uppercase">الصافي الجديد</span>
                            <span id="final_total_display" class="text-3xl font-black text-white">{{ number_format($invoice->final_amount) }}</span>
                            
                            <input type="hidden" name="total_amount" id="hidden_subtotal" value="{{ $invoice->total_amount }}">
                            <input type="hidden" name="final_amount" id="hidden_final" value="{{ $invoice->final_amount }}">
                        </div>
                        <button type="submit" class="w-full bg-amber-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-amber-100 hover:bg-amber-700 transition-all uppercase tracking-widest text-xs">
                            تحديث الفاتورة وحفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const salesBody = document.getElementById('sales_body');
    const addRowBtn = document.getElementById('addRowBtn');
    let rowIndex = {{ $invoice->items->count() }};

    // 1. حساب الإجماليات
    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const rowTotal = price * qty;
            row.querySelector('.row-total').value = rowTotal.toLocaleString();
            subtotal += rowTotal;
        });

        const discount = parseFloat(document.getElementById('discount_input').value) || 0;
        const finalTotal = subtotal - discount;

        document.getElementById('subtotal_display').innerText = subtotal.toLocaleString();
        document.getElementById('final_total_display').innerText = finalTotal.toLocaleString();
        document.getElementById('hidden_subtotal').value = subtotal;
        document.getElementById('hidden_final').value = finalTotal;
    }

    // 2. إضافة صف جديد (بشكل وهمي للفهم)
    addRowBtn.addEventListener('click', function() {
        const newRow = `
        <tr class="item-row border-b border-slate-50 transition-colors animate-in slide-in-from-right duration-300">
            <td class="p-3">
                <select name="items[${rowIndex}][product_id]" required class="w-full p-2 bg-slate-50 rounded-lg text-sm font-bold outline-none border-none">
                    <option value="">اختر المنتج...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-3"><input type="number" name="items[${rowIndex}][price]" value="0" step="0.01" class="item-price w-full text-center p-2 bg-emerald-50/30 rounded-lg font-black text-emerald-700 outline-none border-none"></td>
            <td class="p-3"><input type="number" name="items[${rowIndex}][qty]" value="1" class="item-qty w-full text-center p-2 bg-slate-50 rounded-lg font-bold outline-none border-none"></td>
            <td class="p-3"><input type="text" readonly class="row-total w-full text-center p-2 bg-transparent font-black text-slate-700 outline-none border-none" value="0"></td>
            <td class="p-3 text-center"><button type="button" class="remove-row text-slate-300 hover:text-red-500 p-2">✕</button></td>
        </tr>`;
        salesBody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // 3. مستمع للأحداث (للإزالة والحساب)
    salesBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('item-price') || e.target.classList.contains('item-qty')) {
            calculateTotals();
        }
    });

    salesBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    });

    document.getElementById('discount_input').addEventListener('input', calculateTotals);
});
</script>
@endsection