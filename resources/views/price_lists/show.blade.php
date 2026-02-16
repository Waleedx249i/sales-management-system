@extends('layouts.app', ['title' => 'تحليل جدول: ' . $priceList->name])

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="max-w-5xl mx-auto p-6 space-y-6 min-h-screen bg-[#fcfcfd]">
    
    <div class="bg-indigo-600 p-8 md:p-10 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-100 relative overflow-hidden no-print">
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-2 bg-white/10 w-fit px-4 py-1 rounded-full backdrop-blur-md mb-3">
                        <i class="fa-solid fa-chart-line text-[10px]"></i>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em]">Financial Analysis Mode</span>
                    </div>
                    <h1 class="text-4xl font-black tracking-tighter">{{ $priceList->name }}</h1>
                    <p class="text-indigo-200 text-xs mt-2 font-bold italic">تم تحديث هذا الجدول بتاريخ: {{ $priceList->updated_at->format('Y-m-d') }}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="group flex items-center gap-2 px-6 py-3 bg-indigo-500 hover:bg-indigo-400 text-white rounded-2xl transition shadow-lg border border-indigo-400 font-black text-sm">
                        <i class="fa-solid fa-print group-hover:animate-bounce"></i>
                        طباعة للزبائن
                    </button>
                    <a href="{{ route('price-lists.edit', $priceList->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 rounded-2xl hover:scale-105 transition shadow-xl font-black text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                        تعديل الأسعار
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -bottom-16 opacity-10 transform rotate-12">
            <i class="fa-solid fa-file-invoice-dollar text-[15rem]"></i>
        </div>
    </div>

    <div class="only-print hidden mb-10 text-center">
        <div class="flex justify-between items-center border-b-4 border-slate-900 pb-6 mb-6">
            <div class="text-right">
                <h2 class="text-3xl font-black text-slate-900">قائمة الأسعار المعتمدة</h2>
                <p class="text-slate-500 font-bold">اسم القائمة: {{ $priceList->name }}</p>
            </div>
            <div class="text-left font-mono text-sm text-slate-400">
                تاريخ الاستخراج: {{ date('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden print:border-none print:shadow-none">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 print:bg-slate-100 print:text-slate-900">
                    <th class="p-6">الصنف</th>
                    <th class="p-6 text-center italic no-print">التكلفة (AVG)</th>
                    <th class="p-6 text-center">سعر البيع الحالي</th>
                    <th class="p-6 text-center no-print">هامش الربح (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 print:divide-slate-200">
                @foreach($priceList->items as $item)
                @php 
                    $cost = $item->product->cost->weighted_average_cost ?? 0;
                    $margin = $item->price - $cost;
                    $marginPercentage = $cost > 0 ? ($margin / $cost) * 100 : 0;
                    
                    // تحديد لون الهامش بناءً على القيمة
                    $marginColor = 'text-emerald-600';
                    if($marginPercentage < 5) $marginColor = 'text-rose-600';
                    elseif($marginPercentage < 15) $marginColor = 'text-amber-600';
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all group page-break border-b border-slate-50 print:border-slate-100">
                    <td class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="relative w-14 h-14 shrink-0">
                                <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://ui-avatars.com/api/?name='.urlencode($item->product->name).'&background=f1f5f9&color=6366f1' }}" 
                                     class="w-full h-full rounded-xl object-cover border-2 border-slate-100 shadow-sm print:border-slate-300">
                            </div>
                            <div>
                                <span class="font-black text-slate-700 block leading-tight group-hover:text-indigo-600 transition-colors">{{ $item->product->name }}</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter print:text-slate-500">Code: {{ $item->product->code ?? '#'.$item->product_id }}</span>
                            </div>
                        </div>
                    </td>
                    
                    <td class="p-6 text-center no-print">
                        <span class="font-mono font-black text-slate-500 bg-slate-50 px-3 py-1 rounded-lg text-sm border border-slate-100">
                            {{ number_format($cost) }}
                        </span>
                    </td>

                    <td class="p-6 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-xl font-black text-indigo-700 font-mono tracking-tighter print:text-slate-900 print:text-2xl">
                                {{ number_format($item->price) }}
                            </span>
                            <span class="text-[8px] font-black text-slate-300 uppercase leading-none print:text-slate-500">SDG / Unit</span>
                        </div>
                    </td>

                    <td class="p-6 text-center no-print">
                        @if($margin > 0)
                            <div class="flex flex-col items-center">
                                <span class="{{ $marginColor }} font-black text-sm flex items-center justify-center gap-1">
                                    <i class="fa-solid {{ $marginPercentage < 5 ? 'fa-triangle-exclamation' : 'fa-caret-up' }}"></i> 
                                    {{ number_format($margin) }} <small class="text-[10px]">SDG</small>
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">{{ number_format($marginPercentage, 1) }}% الربح</span>
                            </div>
                        @else
                            <div class="bg-rose-50 text-rose-600 px-3 py-1 rounded-full text-[10px] font-black uppercase italic border border-rose-100 inline-block">
                                <i class="fa-solid fa-circle-down mr-1"></i> خسارة / Low Margin
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center no-print text-slate-400 font-bold text-xs mt-4">
        <a href="{{ route('price-lists.index') }}" class="hover:text-indigo-600 transition-all flex items-center gap-2 group">
            <i class="fa-solid fa-circle-arrow-right group-hover:-translate-x-1 transition-transform"></i>
            العودة للقوائم الرئيسية
        </a>
        <div class="flex items-center gap-4">
            <span class="bg-slate-100 px-3 py-1 rounded-lg">إجمالي الأصناف: {{ $priceList->items->count() }}</span>
            <span class="italic uppercase">Internal Financial Analysis</span>
        </div>
    </div>
</div>

<style>
    /* الخطوط والتنسيق العام */
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700;900&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; }
    
    /* منع ظهور أزرار المتصفح الافتراضية في الطباعة */
    @media print {
        .no-print { display: none !important; }
        .only-print { display: block !important; }
        
        body { background: white !important; }
        .max-w-5xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
        
        /* تحسين الوضوح في الورق */
        .print\:text-slate-900 { color: #000 !important; }
        .print\:text-2xl { font-size: 1.5rem !important; }
        
        /* التحكم في الصفحات */
        .page-break { page-break-inside: avoid; border-bottom: 1px solid #e2e8f0 !important; }
        
        @page {
            margin: 1.5cm;
            size: A4;
        }
    }

    /* تأثيرات جمالية إضافية */
    .group:hover img {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }
</style>
@endsection