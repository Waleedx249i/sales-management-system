@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <i class="fa-solid fa-map-location-dot text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">تقرير مبيعات العهد</h1>
                <p class="text-sm text-slate-500 font-bold">تحليل أداء مبيعات النقاط الخارجية والمناديب</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="history.back()" class="bg-white border-2 border-slate-100 text-slate-600 px-5 py-2.5 rounded-xl font-black text-xs hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-chevron-right"></i>
                رجوع للمبيعات
            </button>
            
            <a href="{{ route('reports.store_sales') }}" class="bg-slate-100 text-slate-700 px-5 py-2.5 rounded-xl font-black text-xs hover:bg-slate-200 transition-all flex items-center gap-2">
                <i class="fa-solid fa-shop"></i>
                الانتقال للمحل
            </a>

            <a href="?export=true&date={{ request('date') }}&pos_name={{ request('pos_name') }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-file-csv"></i>
                تصدير التقرير
            </a>
        </div>
    </div>

    <form class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8 flex flex-wrap items-end gap-5">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">نقطة التوزيع</label>
            <div class="relative">
                <i class="fa-solid fa-store absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <select name="pos_name" class="w-full pr-10 pl-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-600 appearance-none">
                    <option value="">كل النقاط المتاحة</option>
                    @foreach($posList as $name)
                        <option value="{{ $name }}" {{ request('pos_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">تاريخ الكشف</label>
            <div class="relative">
                <i class="fa-solid fa-calendar-check absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full pr-10 pl-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-600">
            </div>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-black hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
            <i class="fa-solid fa-arrows-rotate"></i>
            تحديث البيانات
        </button>
    </form>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-indigo-50/50 text-indigo-400 text-[11px] font-black uppercase tracking-[0.15em] border-b border-indigo-50">
                    <th class="p-6">نقطة التوزيع</th>
                    <th class="p-6 text-center italic text-slate-400 font-normal">-- التفاصيل --</th>
                    <th class="p-6 text-center">الكمية المباعة</th>
                    <th class="p-6 text-center font-black">إجمالي المبيعات</th>
                    <th class="p-6 text-emerald-600 text-center">صافي الربح</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $p)
                <tr class="hover:bg-indigo-50/20 transition-all group">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black">
                                <i class="fa-solid fa-truck-ramp-box text-sm"></i>
                            </div>
                            <span class="font-black text-indigo-700 text-lg">{{ $p->pos_name }}</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-bold text-slate-600">{{ $p->product_name }}</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-slate-100 text-slate-500 px-4 py-1.5 rounded-full font-mono font-black text-sm">
                            {{ $p->quantity_sold }}
                        </span>
                    </td>
                    <td class="p-6 text-center font-mono font-black text-slate-800 text-lg tracking-tighter">
                        {{ number_format($p->total_amount) }}
                    </td>
                    <td class="p-6 text-center">
                        <div class="inline-flex items-center gap-2 text-emerald-600 font-black bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100/50">
                            <i class="fa-solid fa-money-bill-trend-up text-xs"></i>
                            <span class="font-mono">+{{ number_format($p->total_amount - $p->item_cost) }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                            <i class="fa-solid fa-map-pin text-4xl"></i>
                        </div>
                        <p class="text-slate-400 font-black tracking-tighter text-xl">لا توجد بيانات مبيعات للنقاط حالياً</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection