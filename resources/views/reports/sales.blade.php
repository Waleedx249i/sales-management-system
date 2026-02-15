@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#f9fafb] min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="w-11 h-11 flex items-center justify-center bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm text-gray-400 group">
                <i class="fa-solid fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie text-indigo-600"></i>
                    الملخص التنفيذي للمبيعات
                </h1>
                <p class="text-gray-500 font-medium italic">تحليل ذكي لأداء المحل ونقاط التوزيع الخارجية</p>
            </div>
        </div>
        
        <div class="bg-emerald-600 text-white px-8 py-4 rounded-2xl shadow-xl shadow-emerald-100 flex items-center gap-5 border border-emerald-500">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                <i class="fa-solid fa-sack-dollar text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">صافي الربح الكلي</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black font-mono tracking-tighter">{{ number_format($storeProfit + $posProfit) }}</span>
                    <span class="text-[10px] font-bold opacity-70 tracking-tighter">SDG</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
        
        <div class="relative group overflow-hidden bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-between transition-all hover:shadow-2xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 opacity-50"></div>
            
            <div class="relative z-10">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 uppercase tracking-wider border border-blue-100">
                    <i class="fa-solid fa-circle text-[6px] ml-2 animate-pulse"></i>
                    المبيعات المباشرة
                </span>
                <h2 class="text-2xl font-black mt-6 mb-8 text-gray-800 flex items-center gap-3">
                    <i class="fa-solid fa-shop text-blue-200"></i>
                    نشاط المحل الرئيسي
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-gray-50 rounded-2xl border border-transparent">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2 flex items-center gap-1">
                            <i class="fa-solid fa-wallet text-[8px]"></i> الإيرادات
                        </p>
                        <p class="text-2xl font-black text-gray-900 font-mono tracking-tighter">{{ number_format($storeRevenue) }}</p>
                    </div>
                    <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] font-black text-emerald-500 uppercase mb-2 flex items-center gap-1">
                            <i class="fa-solid fa-arrow-up-right-dots text-[8px]"></i> الأرباح
                        </p>
                        <p class="text-2xl font-black text-emerald-700 font-mono tracking-tighter">{{ number_format($storeProfit) }}</p>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('reports.store_sales') }}" class="mt-10 group-hover:scale-[1.02] transition-transform">
                <div class="flex items-center justify-center gap-3 bg-blue-600 text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-100">
                    <span>التقرير التفصيلي للمحل</span>
                    <i class="fa-solid fa-arrow-left-long"></i>
                </div>
            </a>
        </div>

        <div class="relative group overflow-hidden bg-gradient-to-br from-indigo-900 to-slate-900 p-8 rounded-[2.5rem] shadow-2xl flex flex-col justify-between transition-all hover:shadow-indigo-200">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>

            <div class="relative z-10">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black bg-white/10 text-indigo-200 uppercase tracking-wider border border-white/10 backdrop-blur-md">
                    <i class="fa-solid fa-truck-fast ml-2 text-[8px]"></i>
                    نظام العهد والتوزيع
                </span>
                <h2 class="text-2xl font-black mt-6 mb-8 text-white flex items-center gap-3">
                    <i class="fa-solid fa-map-location-dot text-indigo-400/50"></i>
                    نقاط التوزيع الخارجية
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/5">
                        <p class="text-[10px] font-black text-indigo-300 uppercase mb-2">إجمالي المبيعات</p>
                        <p class="text-2xl font-black text-white font-mono tracking-tighter">{{ number_format($posRevenue) }}</p>
                    </div>
                    <div class="p-5 bg-emerald-500/20 rounded-2xl border border-emerald-500/20">
                        <p class="text-[10px] font-black text-emerald-400 uppercase mb-2">صافي الأرباح</p>
                        <p class="text-2xl font-black text-emerald-400 font-mono tracking-tighter">{{ number_format($posProfit) }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('reports.pos_sales') }}" class="mt-10 group-hover:scale-[1.02] transition-transform">
                <div class="flex items-center justify-center gap-3 bg-white text-indigo-900 py-4 rounded-2xl font-black shadow-xl">
                    <span>إدارة مبيعات العهد</span>
                    <i class="fa-solid fa-users-gear"></i>
                </div>
            </a>
        </div>
    </div>

    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden relative">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-4">
            <h3 class="text-xl font-black text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                تحليل المبيعات الأسبوعي
            </h3>
            <div class="flex items-center gap-6 text-[10px] font-black text-gray-400 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                <div class="flex items-center gap-2 uppercase tracking-tighter">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span> إيراد المحل
                </div>
            </div>
        </div>

        <div class="h-80 flex items-end gap-3 md:gap-8 px-4">
            @php $maxTotal = max($chartData->pluck('total')->toArray() ?: [1]); @endphp
            @foreach($chartData as $data)
                @php $height = ($data->total / $maxTotal * 100); @endphp
                <div class="flex-1 flex flex-col items-center group relative">
                    <div class="absolute -top-14 bg-gray-900 text-white text-[10px] py-2 px-4 rounded-xl opacity-0 group-hover:opacity-100 transition-all pointer-events-none z-20 whitespace-nowrap shadow-2xl flex items-center gap-2">
                        <i class="fa-solid fa-coins text-amber-400 text-[8px]"></i>
                        {{ number_format($data->total) }} SDG
                    </div>
                    
                    <div class="w-full bg-blue-50 rounded-t-2xl group-hover:bg-blue-600 transition-all duration-700 ease-out relative overflow-hidden" style="height: {{ $height }}%">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent"></div>
                    </div>
                    
                    <div class="mt-6 text-[9px] font-black text-gray-400 group-hover:text-blue-600 transition-colors uppercase tracking-widest text-center leading-tight">
                        {{ \Carbon\Carbon::parse($data->date)->translatedFormat('D') }}<br>
                        <span class="text-[8px] opacity-60 font-bold">{{ \Carbon\Carbon::parse($data->date)->format('d/m') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection