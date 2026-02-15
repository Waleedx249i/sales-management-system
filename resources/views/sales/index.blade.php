@extends('layouts.app', ['title' => 'أرشيف المبيعات والتحصيل'])

@section('content')
<div class="max-w-[100rem] mx-auto p-4 space-y-6">
    
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800">أرشيف المبيعات</h1>
            <p class="text-slate-400 font-bold text-sm">إدارة الفواتير، التحصيلات، والتقارير</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-white text-slate-600 border border-slate-200 px-6 py-3 rounded-2xl font-black flex items-center gap-2 hover:bg-slate-50 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير
            </button>
            <a href="{{ route('sales.create') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-indigo-100 hover:scale-105 transition-transform flex items-center gap-2">
                <span>+</span> فاتورة جديدة
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-400 uppercase mr-2">بحث سريع</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفاتورة أو اسم العميل..." 
                       class="w-full bg-slate-50 border-none rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-400 uppercase mr-2">حالة الدفع</label>
                <select name="status" class="w-full bg-slate-50 border-none rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                    <option value="">كل الحالات</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>خالصة</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئية</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>آجلة</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-400 uppercase mr-2">من تاريخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="w-full bg-slate-50 border-none rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-2xl font-black text-sm hover:bg-slate-800 transition-all">تطبيق الفلتر</button>
                <a href="{{ route('sales.index') }}" class="p-3 bg-slate-100 text-slate-500 rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all" title="إعادة تعيين">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden printable-area">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase border-b">
                    <th class="p-6">رقم الفاتورة</th>
                    <th class="p-6">التاريخ</th>
                    <th class="p-6">العميل</th>
                    <th class="p-6 text-center">الإجمالي</th>
                    <th class="p-6 text-center">المدفوع</th>
                    <th class="p-6 text-center text-red-500">المتبقي</th>
                    <th class="p-6 text-center">الحالة</th>
                    <th class="p-6 no-print">إجراءات</th>
                </tr>
            </thead>
           <tbody class="divide-y divide-slate-50 font-bold">
    @forelse($invoices as $invoice)
    <tr class="hover:bg-slate-50/50 transition-colors {{ !$invoice->is_approved ? 'bg-slate-50/30' : '' }}">
        <td class="p-6 font-black">
            <span class="text-indigo-600">{{ $invoice->invoice_number }}</span>
            @if(!$invoice->is_approved)
                <span class="block text-[9px] text-amber-500 font-black uppercase tracking-tighter mt-1">📝 مسودة - لم تُعتمد</span>
            @endif
        </td>
        <td class="p-6 text-slate-500 text-xs">{{ $invoice->created_at->format('Y-m-d') }}</td>
        <td class="p-6 text-slate-700">{{ $invoice->customer_name }}</td>
        <td class="p-6 text-center font-black">{{ number_format($invoice->final_amount) }}</td>
        <td class="p-6 text-center text-emerald-600">{{ number_format($invoice->paid_amount) }}</td>
        <td class="p-6 text-center text-red-500">{{ number_format($invoice->remaining_amount) }}</td>
        <td class="p-6 text-center">
            @php
                $colors = [
                    'paid' => 'bg-emerald-100 text-emerald-600', 
                    'partial' => 'bg-amber-100 text-amber-600', 
                    'pending' => 'bg-red-100 text-red-600',
                    'draft' => 'bg-slate-200 text-slate-500' // لون للمسودة
                ];
                $labels = [
                    'paid' => 'خالصة', 
                    'partial' => 'جزئية', 
                    'pending' => 'آجلة',
                    'draft' => 'مسودة'
                ];
            @endphp
            <span class="px-3 py-1 rounded-xl text-[10px] font-black {{ $colors[!$invoice->is_approved ? 'draft' : $invoice->status] ?? $colors['pending'] }}">
                {{ $labels[!$invoice->is_approved ? 'draft' : $invoice->status] ?? 'غير محدد' }}
            </span>
        </td>
        <td class="p-6 no-print">
            <div class="flex items-center gap-2">
                @if(!$invoice->is_approved)
                <form action="{{ route('sales.approve', $invoice->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من تصديق الفاتورة؟ سيتم خصم الكميات من المخزن الآن.')">
                    @csrf
                    <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="تصديق واعتماد">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
                @endif

                <a href="{{ route('sales.show', $invoice->id) }}" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:bg-indigo-50 hover:text-indigo-600" title="عرض">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>

                <form action="{{ route('sales.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('تحذير: هل أنت متأكد من حذف هذه الفاتورة نهائياً؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-600" title="حذف">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    @endforelse
</tbody>
        </table>
        
        <div class="p-6 bg-slate-50/50">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .printable-area, .printable-area * { visibility: visible; }
        .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .rounded-[2.5rem] { border-radius: 0 !important; }
        .shadow-sm { box-shadow: none !important; }
    }
</style>

<script>
    function printSingleInvoice(url) {
        // نفتح صفحة التفاصيل في نافذة مخفية ونطلب طباعتها
        const printWindow = window.open(url, '_blank');
        printWindow.onload = function() {
            printWindow.print();
            // printWindow.close(); // اختياري لإغلاق النافذة بعد الطباعة
        };
    }
</script>
@endsection