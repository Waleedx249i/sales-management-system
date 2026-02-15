@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-100">
                <i class="fa-solid fa-chart-mixed text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">تقرير مبيعات المحل</h1>
                <p class="text-sm text-slate-500 font-bold text-right">مراقبة الفواتير الصادرة وتحليل الأرباح اليومية</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="history.back()" class="bg-white border-2 border-slate-100 text-slate-600 px-5 py-2.5 rounded-xl font-black text-xs hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع للمبيعات
            </button>
            
            <a href="{{ route('reports.pos_sales') }}" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-black transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
                <i class="fa-solid fa-location-dot"></i>
                الانتقال للعهد
            </a>

            <a href="?export=true&date={{ request('date') }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-file-export"></i>
                تصدير البيانات
            </a>
        </div>
    </div>

    <form class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8 flex flex-wrap md:flex-nowrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">فلتر باليوم</label>
            <div class="relative">
                <i class="fa-solid fa-calendar-day absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full pr-10 pl-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-slate-600">
            </div>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">فلتر بالشهر</label>
            <div class="relative">
                <i class="fa-solid fa-calendar-range absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="month" name="month" value="{{ request('month') }}" 
                       class="w-full pr-10 pl-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-slate-600">
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
            <i class="fa-solid fa-magnifying-glass"></i>
            تحديث النتائج
        </button>
    </form>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden font-sans">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-[0.15em] border-b border-slate-50">
                    <th class="p-6">رقم الفاتورة</th>
                    <th class="p-6">العميل</th>
                    <th class="p-6">الإيراد (الكلي)</th>
                    <th class="p-6 text-emerald-600">صافي الربح</th>
                    <th class="p-6 text-center italic">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $s)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="p-6">
                        <span class="font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg text-sm">
                            <i class="fa-solid fa-hashtag text-[10px] opacity-50"></i>{{ $s->invoice_number }}
                        </span>
                    </td>
                    <td class="p-6">
                        <div class="flex flex-col">
                            <span class="font-black text-slate-700">{{ $s->customer_name }}</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase italic">عميل نقدي</span>
                        </div>
                    </td>
                    <td class="p-6">
                        <span class="font-mono font-black text-slate-700 text-lg tracking-tighter">
                            {{ number_format($s->final_amount) }}
                        </span>
                    </td>
                    <td class="p-6">
                        <div class="inline-flex items-center gap-2 text-emerald-600 font-black">
                            <i class="fa-solid fa-circle-arrow-up text-xs opacity-40"></i>
                            <span class="font-mono text-lg">+{{ number_format($s->final_amount - $s->total_cost) }}</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm text-slate-400 font-bold font-mono tracking-tighter">
                                <i class="fa-regular fa-clock ml-1 text-[10px]"></i>
                                {{ $s->date_text }}
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
                            <i class="fa-solid fa-inbox text-4xl"></i>
                        </div>
                        <p class="text-slate-400 font-black">لا توجد مبيعات مسجلة لهذه الفترة</p>
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