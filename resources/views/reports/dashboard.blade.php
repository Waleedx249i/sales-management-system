@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#f4f7fe] min-h-screen">
    
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <i class="fa-solid fa-gauge-high text-indigo-600"></i>
            لوحة التحكم 
        </h1>
        <p class="text-slate-500 font-bold mt-2 mr-10 italic uppercase text-xs tracking-widest">Business Intelligence Dashboard</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-blue-100/50 border-b-4 border-blue-500 hover:scale-[1.03] transition-transform duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-chart-line-up text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-blue-400 uppercase tracking-tighter">Daily Growth</span>
            </div>
            <h3 class="text-slate-400 text-xs font-black uppercase mb-1 mr-1">إجمالي المبيعات</h3>
            <p class="text-2xl font-black text-slate-800 font-mono tracking-tighter">
                {{ number_format($totalSales) }} <span class="text-[10px] text-slate-400">SDG</span>
            </p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-red-100/50 border-b-4 border-red-500 hover:scale-[1.03] transition-transform duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-cart-flatbed-boxes text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-red-400 uppercase tracking-tighter">Expenses</span>
            </div>
            <h3 class="text-slate-400 text-xs font-black uppercase mb-1 mr-1">إجمالي المشتريات</h3>
            <p class="text-2xl font-black text-slate-800 font-mono tracking-tighter">
                {{ number_format($totalPurchases) }} <span class="text-[10px] text-slate-400">SDG</span>
            </p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-emerald-100/50 border-b-4 border-emerald-500 hover:scale-[1.03] transition-transform duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-warehouse text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-tighter">Stock Assets</span>
            </div>
            <h3 class="text-slate-400 text-xs font-black uppercase mb-1 mr-1">قيمة المخزون</h3>
            <p class="text-2xl font-black text-slate-800 font-mono tracking-tighter">
                {{ number_format($stockValue) }} <span class="text-[10px] text-slate-400">SDG</span>
            </p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-amber-100/50 border-b-4 border-amber-500 hover:scale-[1.03] transition-transform duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-amber-400 uppercase tracking-tighter">Receivables</span>
            </div>
            <h3 class="text-slate-400 text-xs font-black uppercase mb-1 mr-1">ديون العملاء</h3>
            <p class="text-2xl font-black text-slate-800 font-mono tracking-tighter">
                {{ number_format($totalDebts) }} <span class="text-[10px] text-slate-400">SDG</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <a href="{{ route('reports.sales') }}" class="group relative overflow-hidden bg-indigo-600 p-6 rounded-2xl shadow-lg hover:shadow-indigo-200 transition-all active:scale-95">
            <div class="relative z-10 flex flex-col items-center gap-3 text-white font-black uppercase tracking-widest text-sm">
                <i class="fa-solid fa-money-bill-trend-up text-2xl mb-1"></i>
                تقرير المبيعات
            </div>
            <div class="absolute -right-4 -bottom-4 text-white opacity-10 text-6xl rotate-12 group-hover:scale-125 transition-transform">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
        </a>

        <a href="{{ route('reports.purchases') }}" class="group relative overflow-hidden bg-slate-800 p-6 rounded-2xl shadow-lg hover:shadow-slate-300 transition-all active:scale-95">
            <div class="relative z-10 flex flex-col items-center gap-3 text-white font-black uppercase tracking-widest text-sm">
                <i class="fa-solid fa-truck-ramp-box text-2xl mb-1"></i>
                تقرير المشتريات
            </div>
            <div class="absolute -right-4 -bottom-4 text-white opacity-10 text-6xl rotate-12 group-hover:scale-125 transition-transform">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </a>

        <a href="{{ route('reports.inventory') }}" class="group relative overflow-hidden bg-teal-600 p-6 rounded-2xl shadow-lg hover:shadow-teal-200 transition-all active:scale-95">
            <div class="relative z-10 flex flex-col items-center gap-3 text-white font-black uppercase tracking-widest text-sm">
                <i class="fa-solid fa-boxes-stacked text-2xl mb-1"></i>
                تقرير المخزون
            </div>
            <div class="absolute -right-4 -bottom-4 text-white opacity-10 text-6xl rotate-12 group-hover:scale-125 transition-transform">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </a>

        <a href="{{ route('reports.customers') }}" class="group relative overflow-hidden bg-orange-600 p-6 rounded-2xl shadow-lg hover:shadow-orange-200 transition-all active:scale-95">
            <div class="relative z-10 flex flex-col items-center gap-3 text-white font-black uppercase tracking-widest text-sm">
                <i class="fa-solid fa-users-viewfinder text-2xl mb-1"></i>
                تقرير العملاء
            </div>
            <div class="absolute -right-4 -bottom-4 text-white opacity-10 text-6xl rotate-12 group-hover:scale-125 transition-transform">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
        </a>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                <i class="fa-solid fa-medal text-amber-500"></i>
                الأصناف الأكثر طلباً
            </h3>
            <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-4 py-2 rounded-full border border-slate-100 italic uppercase">Top 5 Best Sellers</span>
        </div>
        
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-50">
                    <th class="p-4">الصنف</th>
                    <th class="p-4 text-center">الكمية المباعة</th>
                    <th class="p-4">كود الصنف</th>
                    <th class="p-4 text-center">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($topProducts as $product)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="p-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                            {{ $loop->iteration }}
                        </div>
                        <span class="font-bold text-slate-700">{{ $product->name }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="bg-emerald-50 text-emerald-600 px-4 py-1 rounded-full font-black font-mono text-sm">
                            {{ $product->qty }}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="font-mono text-slate-400 text-xs tracking-tighter">
                            <i class="fa-solid fa-barcode mr-1 opacity-50"></i> {{ $product->code }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <button class="text-slate-300 hover:text-indigo-600 transition-colors">
                            <i class="fa-solid fa-circle-info text-lg"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection