@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#fcfdfd] min-h-screen">

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-teal-100 rounded-xl text-teal-400 hover:text-teal-600 hover:border-teal-200 transition shadow-sm group">
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-boxes-stacked text-teal-600"></i>
                    تقرير تقييم المخزون الحالي
                </h1>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1 italic">Current Stock Valuation Report</p>
            </div>
        </div>
        
        <a href="{{ request()->fullUrlWithQuery(['export' => 'true']) }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition flex items-center gap-2 font-black text-sm">
            <i class="fa-solid fa-file-excel text-lg"></i>
            تصدير Excel
        </a>
    </div>

    <div class="bg-gradient-to-r from-teal-600 to-teal-800 p-8 rounded-[2rem] shadow-xl shadow-teal-100 mb-10 flex flex-col md:flex-row justify-between items-center text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 translate-x-10 -translate-y-10">
            <i class="fa-solid fa-warehouse text-[12rem]"></i>
        </div>
        
        <div class="relative z-10 mb-6 md:mb-0">
            <div class="flex items-center gap-3 mb-2 opacity-80 uppercase text-[10px] font-black tracking-[0.2em]">
                <i class="fa-solid fa-list-check"></i> إجمالي عدد الأصناف
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black font-mono tracking-tighter">{{ count($data) }}</span>
                <span class="text-sm font-bold opacity-70 italic">صنف مسجل</span>
            </div>
        </div>

        <div class="relative z-10 text-center md:text-left border-t md:border-t-0 md:border-r border-white/20 pt-6 md:pt-0 md:pr-10">
            <div class="flex items-center gap-3 mb-2 opacity-80 uppercase text-[10px] font-black tracking-[0.2em] justify-center md:justify-end">
                القيمة الإجمالية للمخزون <i class="fa-solid fa-vault"></i>
            </div>
            <div class="flex items-baseline gap-2 justify-center md:justify-end">
                <span class="text-sm font-bold opacity-70 italic">SDG</span>
                <span class="text-4xl font-black font-mono tracking-tighter text-emerald-300">
                    {{ number_format($data->sum('total_value')) }}
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100">
                        <th class="p-6">كود الصنف</th>
                        <th class="p-6">اسم الصنف</th>
                        <th class="p-6 text-center">الكمية الحالية</th>
                        <th class="p-6 text-center">متوسط التكلفة</th>
                        <th class="p-6 text-center">إجمالي القيمة</th>
                        <th class="p-6 text-center">الحالة التشغيلية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($data as $row)
                    <tr class="hover:bg-teal-50/30 transition-colors group">
                        <td class="p-6 font-mono text-xs text-slate-400">
                            <i class="fa-solid fa-barcode mr-1 opacity-40"></i> {{ $row->code }}
                        </td>
                        <td class="p-6">
                            <span class="font-black text-slate-700 group-hover:text-teal-700 transition-colors">{{ $row->name }}</span>
                        </td>
                        <td class="p-6 text-center">
                            @if($row->quantity <= 5)
                                <span class="bg-rose-50 text-rose-600 px-4 py-2 rounded-xl font-black font-mono text-sm border border-rose-100 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                                    {{ $row->quantity }}
                                </span>
                            @else
                                <span class="bg-slate-50 text-slate-600 px-4 py-2 rounded-xl font-black font-mono text-sm border border-slate-100">
                                    {{ $row->quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="p-6 text-center font-mono font-bold text-slate-500">
                            {{ number_format($row->cost, 2) }}
                        </td>
                        <td class="p-6 text-center">
                            <span class="font-black text-teal-700 font-mono text-lg">
                                {{ number_format($row->total_value) }}
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            @if($row->quantity <= 5)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-black uppercase tracking-tighter">
                                    <i class="fa-solid fa-battery-empty"></i> مخزون منخفض
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-tighter">
                                    <i class="fa-solid fa-battery-full"></i> متوفر بالمخزن
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center">
                            <div class="flex flex-col items-center gap-4 text-slate-300">
                                <i class="fa-solid fa-box-open text-6xl"></i>
                                <p class="text-lg font-black italic tracking-tight">المخزن فارغ حالياً!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection