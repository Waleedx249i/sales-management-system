@extends('layouts.app', ['title' => 'كشف عهد المناديب'])

@section('content')
<div class="max-w-7xl mx-auto p-6 min-h-screen bg-[#fcfcfd]">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-emerald-100">
                <i class="fa-solid fa-truck-ramp-box text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">إدارة عهد نقاط البيع</h1>
                <p class="text-sm text-slate-500 font-bold">متابعة البضاعة والعهدة الفاتحة في السوق</p>
            </div>
        </div>
        
        <a href="{{ route('pos.create') }}" class="group bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-1 transition-all flex items-center gap-3">
            <i class="fa-solid fa-plus-circle text-lg"></i>
            <span>إضافة شحنة جديدة</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-emerald-600"></i>
            <span class="text-xs font-black text-slate-500 uppercase tracking-widest">كشوفات العهد النشطة</span>
        </div>

        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="text-slate-400 text-[11px] font-black uppercase tracking-[0.15em] border-b border-slate-50">
                    <th class="p-6">رقم العهدة</th>
                    <th class="p-6">اسم المندوب / النقطة</th>
                    <th class="p-6 text-center">حالة الحمولة</th>
                    <th class="p-6 text-center">تاريخ الشحن</th>
                    <th class="p-6"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($consignments as $con)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-xs">
                                #
                            </span>
                            <span class="font-black text-emerald-600">{{ $con->consignment_number }}</span>
                        </div>
                    </td>
                    <td class="p-6">
                        <div class="flex flex-col">
                            <span class="font-black text-slate-700 text-lg">{{ $con->pos_name }}</span>
                            <span class="text-[10px] text-slate-400 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-[8px]"></i> نقطة توزيع نشطة
                            </span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-xs font-black inline-flex items-center gap-2">
                            <i class="fa-solid fa-box-open text-slate-400"></i>
                            {{ $con->items_count }} صنف
                        </span>
                    </td>
                    <td class="p-6 text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="text-sm text-slate-600 font-black font-mono tracking-tighter">
                                <i class="fa-regular fa-calendar-check ml-1 text-slate-400"></i>
                                {{ $con->created_at->format('Y/m/d') }}
                            </span>
                        </div>
                    </td>
                    <td class="p-6 text-left">
                        <a href="{{ route('pos.show', $con->id) }}" class="inline-flex items-center gap-2 bg-white border-2 border-slate-100 px-5 py-2.5 rounded-xl text-emerald-600 font-black text-xs hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-sm">
                            <span>عرض التفاصيل</span>
                            <i class="fa-solid fa-arrow-left-long"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($consignments->isEmpty())
        <div class="p-20 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-folder-open text-3xl text-slate-200"></i>
            </div>
            <p class="text-slate-400 font-bold">لا توجد عهد مسجلة حالياً</p>
        </div>
        @endif
    </div>
</div>

<style>
    /* تحسين خط الأرقام ليكون متناسقاً */
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection