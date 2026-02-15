@extends('layouts.app', ['title' => 'تفاصيل فاتورة وارد #' . $invoice->invoice_number])

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12 px-4 sm:px-0">
    
    <div class="flex justify-between items-center no-print animate-in fade-in slide-in-from-top-4 duration-700">
        <a href="{{ route('import-invoices.index') }}" class="group flex items-center gap-3 text-slate-500 font-black hover:text-indigo-600 transition-all">
            <span class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all">
                <i class="fa-solid fa-arrow-right"></i>
            </span>
            العودة للأرشيف
        </a>
        <div class="flex gap-3">
            <a href="{{ route('import-invoices.edit', $invoice->id) }}" class="bg-white text-slate-700 border border-slate-200 px-6 py-3 rounded-2xl font-black shadow-sm hover:bg-slate-50 transition flex items-center gap-2 text-sm uppercase">
                <i class="fa-solid fa-pen-to-square"></i> تعديل
            </a>
            <button onclick="window.print()" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition flex items-center gap-3 text-sm uppercase">
                <i class="fa-solid fa-print"></i> طباعة الفاتورة
            </button>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 p-8 md:p-12 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 h-64 bg-indigo-500/5 rounded-full -ml-32 -mt-32"></div>
        
        <div class="relative flex flex-col md:flex-row justify-between items-start gap-8 border-b border-slate-100 pb-10">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] block">Official Invoice</span>
                        <h1 class="text-4xl font-black text-slate-800 tracking-tighter italic">#{{ $invoice->invoice_number }}</h1>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 pt-2">
                    <p class="text-slate-500 font-bold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-slate-300"></i> تاريخ الإدخال: 
                        <span class="text-slate-800">{{ $invoice->created_at->format('d/m/Y - h:i A') }}</span>
                    </p>
                    <p class="text-slate-500 font-bold text-sm flex items-center gap-2">
                        <i class="fa-solid fa-user-tie text-slate-300"></i> المورد: 
                        <span class="text-indigo-600 font-black">{{ $invoice->supplier ? $invoice->supplier->name : '---' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                <div class="bg-slate-50 p-5 rounded-[2rem] text-center min-w-[140px] border border-slate-100">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">سعر الصرف</span>
                    <span class="text-2xl font-black text-slate-800 font-mono italic">{{ number_format($invoice->exchange_rate, 2) }}</span>
                </div>
                <div class="bg-emerald-50 p-5 rounded-[2rem] text-center min-w-[140px] border border-emerald-100/50">
                    <span class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">نسبة التحميل</span>
                    <span class="text-2xl font-black text-emerald-600 font-mono">+{{ $invoice->cost_ratio_percent }}%</span>
                </div>
            </div>
        </div>

        <div class="py-10">
            <div class="flex items-center gap-3 mb-6">
                <i class="fa-solid fa-cubes text-slate-400 text-xl"></i>
                <h3 class="font-black text-slate-800 uppercase tracking-tighter">تفاصيل الأصناف الواردة</h3>
                <div class="h-px flex-1 bg-slate-100 mr-4"></div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-50 shadow-sm">
                <table class="w-full text-right">
                    <thead class="bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest">
                        <tr>
                            <th class="p-5">الصنف / المنتج</th>
                            <th class="p-5 text-center">الكمية</th>
                            <th class="p-5 text-center italic">السعر (EGP)</th>
                            <th class="p-5 text-center italic">التكلفة (SDG)</th>
                            <th class="p-5 text-center bg-indigo-500">التكلفة النهائية (SDG)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoice->items as $item)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="p-5">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <img src="{{$item->image ? asset('storage/' . $item->image) : asset('assets/no-image.png') }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">{{ $item->item_name }}</h4>
                                        <p class="text-[10px] font-mono text-slate-400 font-bold uppercase tracking-widest">{{ $item->item_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-lg font-black text-xs font-mono italic">x{{ $item->quantity }}</span>
                            </td>
                            <td class="p-5 text-center font-bold text-slate-500 font-mono tracking-tighter">{{ number_format($item->price_egp, 2) }}</td>
                            <td class="p-5 text-center font-bold text-slate-500 font-mono tracking-tighter">{{ number_format($item->unit_cost_sdg, 2) }}</td>
                            <td class="p-5 text-center">
                                <span class="font-black text-indigo-600 text-lg font-mono italic">{{ number_format($item->final_unit_cost) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-4">
            <div class="space-y-4">
                <div class="bg-slate-50 rounded-[2rem] p-6 border border-slate-100">
                    <h4 class="font-black text-slate-800 text-xs uppercase mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-indigo-500"></i> ملاحظات النظام
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-bold">
                        تم احتساب التكلفة النهائية بناءً على سعر صرف <span class="text-indigo-600 underline">{{ $invoice->exchange_rate }}</span> مع إضافة مصاريف لوجيستية بنسبة <span class="text-indigo-600 underline">{{ $invoice->cost_ratio_percent }}%</span> لكل وحدة. الكميات تم ترحيلها فعلياً لمخزن الوارد.
                    </p>
                </div>
                
                <div class="flex items-center gap-4 px-6 opacity-40 italic">
                    <i class="fa-solid fa-shield-check text-2xl text-emerald-500"></i>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">
                        Authorized Invoice Management<br>Cloud System Secure Check
                    </p>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-slate-200 relative overflow-hidden group">
                <div class="absolute bottom-0 right-0 opacity-10 -mr-10 -mb-10 group-hover:scale-110 transition-transform duration-1000">
                    <i class="fa-solid fa-money-bill-transfer text-9xl"></i>
                </div>
                
                <h3 class="font-black text-indigo-400 text-xs uppercase tracking-[0.3em] mb-8 border-b border-white/10 pb-4">Financial Summary</h3>
                
                <div class="space-y-5 relative">
                    <div class="flex justify-between items-center group/item">
                        <span class="text-slate-400 text-xs font-black uppercase">قيمة البضاعة (SDG):</span>
                        <span class="font-black text-lg font-mono tracking-tighter italic group-hover/item:text-indigo-300 transition-colors">{{ number_format($invoice->total_goods_sdg) }}</span>
                    </div>
                    <div class="flex justify-between items-center group/item">
                        <span class="text-slate-400 text-xs font-black uppercase">إجمالي اللوجستيات:</span>
                        <span class="font-black text-lg text-red-400 font-mono tracking-tighter italic group-hover/item:scale-110 transition-transform">+{{ number_format($invoice->total_logistic) }}</span>
                    </div>
                    
                    <div class="pt-8 mt-6 border-t border-white/20">
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-[10px] text-indigo-400 font-black block mb-1 uppercase tracking-widest">Grand Total Amount</span>
                                <div class="text-5xl font-black text-white tracking-tighter font-mono italic">
                                    {{ number_format($invoice->total_goods_sdg + $invoice->total_logistic) }}
                                </div>
                            </div>
                            <span class="text-2xl font-black text-indigo-500 mb-2">SDG</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @font-face {
        font-family: 'Digital';
        src: url('https://fonts.cdnfonts.com/s/14352/DigitalNumbers-Regular.woff') format('woff');
    }
    
    .font-mono { font-family: 'Courier New', Courier, monospace; }

    @media print {
        .no-print { display: none !important; }
        body { background: white !important; padding: 0 !important; }
        .max-w-6xl { max-width: 100% !important; margin: 0 !important; }
        .bg-white { border: none !important; shadow: none !important; }
        .bg-slate-900 { background: #0f172a !important; color: white !important; -webkit-print-color-adjust: exact; }
        .rounded-[3rem], .rounded-[2.5rem] { border-radius: 1rem !important; }
        .shadow-sm, .shadow-2xl { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>
@endsection