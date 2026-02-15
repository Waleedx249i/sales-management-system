@extends('layouts.app', ['title' => 'تفاصيل الفاتورة'])

@section('content')
<style>
    @media print {
        /* إخفاء العناصر غير الضرورية عند الطباعة */
        nav, side-bar, footer, .no-print, button, a, #paymentModal {
            display: none !important;
        }

        /* ضبط الصفحة لتملأ الورقة */
        .max-w-5xl {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .bg-white {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .rounded-[2.5rem], .rounded-3xl, .rounded-2xl {
            border-radius: 0 !important;
        }

        .text-slate-800, .text-slate-900 {
            color: #000 !important;
        }
        
        table {
            border: 1px solid #e2e8f0 !important;
        }
    }
</style>

<div class="max-w-5xl mx-auto p-4 space-y-6">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 flex justify-between items-start">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="bg-slate-900 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                    {{ $invoice->invoice_number }}
                </span>
                @php
                    $statusColors = [
                        'paid' => 'bg-emerald-100 text-emerald-600',
                        'partial' => 'bg-amber-100 text-amber-600',
                        'pending' => 'bg-red-100 text-red-600'
                    ];
                    $statusLabels = ['paid' => 'خالصة', 'partial' => 'دفع جزئي', 'pending' => 'آجلة (دين)'];
                @endphp
                <span class="px-4 py-1 rounded-full text-[10px] font-black {{ $statusColors[$invoice->status] ?? 'bg-slate-100' }}">
                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                </span>
            </div>
            <h1 class="text-3xl font-black text-slate-800">{{ $invoice->customer_name }}</h1>
            <p class="text-slate-400 font-bold mt-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $invoice->created_at->format('Y-m-d H:i') }}
            </p>
        </div>
        
        <div class="text-left space-y-2">
            <div class="no-print flex gap-2 justify-end">
                <button onclick="window.print()" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-black text-xs hover:bg-emerald-700 transition-all flex items-center gap-2">
                    طباعة 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
                <a href="{{ route('sales.edit', $invoice->id) }}" class="bg-slate-100 text-slate-600 px-6 py-2 rounded-xl font-black text-xs hover:bg-indigo-600 hover:text-white transition-all">تعديل ✎</a>
            </div>
            <div class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($invoice->final_amount) }} <span class="text-xs font-bold">SDG</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase">
                    <tr>
                        <th class="p-6 border-b">المنتج</th>
                        <th class="p-6 text-center border-b">الكمية</th>
                        <th class="p-6 text-center border-b">السعر</th>
                        <th class="p-6 text-left border-b">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-bold text-sm">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="p-6 text-slate-700">{{ $item->product->name }}</td>
                        <td class="p-6 text-center text-slate-500">{{ $item->quantity }}</td>
                        <td class="p-6 text-center text-slate-500">{{ number_format($item->unit_price) }}</td>
                        <td class="p-6 text-left font-black">{{ number_format($item->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-6 bg-slate-50/50 flex justify-between items-center">
                <span class="text-slate-400 font-bold text-xs uppercase">إجمالي الأصناف</span>
                <span class="font-black text-slate-700">{{ number_format($invoice->total_amount) }} SDG</span>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h3 class="font-black text-slate-800 mb-6 flex justify-between items-center">
                    سجل التحصيلات
                    <span class="text-[10px] bg-slate-100 px-2 py-1 rounded-lg">دفعات: {{ $invoice->payments->count() }}</span>
                </h3>
                
                <div class="space-y-4 mb-6">
                    @forelse($invoice->payments as $payment)
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase">{{ $payment->payment_date }}</span>
                            <span class="text-sm font-black text-slate-700">{{ number_format($payment->amount) }} SDG</span>
                        </div>
                        <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">{{ $payment->payment_method }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-xs font-bold">لا توجد دفعات مسجلة بعد</div>
                    @endforelse
                </div>

                <div class="border-t border-dashed border-slate-200 pt-6 space-y-3">
                    <div class="flex justify-between text-sm font-bold">
                        <span class="text-slate-400">إجمالي المدفوع:</span>
                        <span class="text-emerald-600">{{ number_format($invoice->paid_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-black">
                        <span class="text-slate-800">المتبقي (دين):</span>
                        <span class="text-red-500">{{ number_format($invoice->remaining_amount) }}</span>
                    </div>
                    
                    @if($invoice->remaining_amount > 0)
                    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black mt-4 hover:bg-slate-800 transition-all shadow-xl no-print">
                        + إضافة دفعة جديدة
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="paymentModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl relative">
        <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="absolute top-6 left-6 text-slate-400 hover:text-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="mb-8 text-right">
            <h3 class="text-2xl font-black text-slate-800">إضافة دفعة مالية</h3>
            <p class="text-slate-400 font-bold text-sm">الفاتورة رقم: {{ $invoice->invoice_number }}</p>
        </div>

       {{-- التعديل هنا: استخدام اسم الراوت الجديد وتمرير الـ id --}}
<form action="{{ route('sales.add_payment', $invoice->id) }}" method="POST" class="space-y-6">
            @csrf
            <div class="text-right">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">المبلغ المستلم</label>
                <input type="number" name="amount" max="{{ $invoice->remaining_amount }}" step="0.01" required 
                       class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-lg font-black text-slate-800 outline-none focus:border-emerald-500 transition-all text-center"
                       placeholder="0.00">
                <p class="text-[10px] text-amber-600 mt-2 mr-2 font-bold italic text-right">أقصى مبلغ مسموح: {{ number_format($invoice->remaining_amount) }} SDG</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-right">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">تاريخ الدفع</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold text-slate-800 outline-none">
                </div>
                
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                تأكيد واستلام المبلغ
            </button>
        </form>
    </div>
</div>

@endsection