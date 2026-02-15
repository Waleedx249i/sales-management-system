@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 bg-[#fcfcfd] min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div class="flex items-center gap-5">
            <a href="{{ route('finance.index') }}" class="group p-3 bg-white border border-gray-100 rounded-2xl hover:bg-purple-600 hover:border-purple-600 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right text-gray-400 group-hover:text-white transition-colors"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase italic">
                    <i class="fa-solid fa-user-shield text-purple-600 ml-2"></i> مسحوبات الأرباح الشخصية
                </h1>
                <p class="text-gray-500 font-medium mt-1 text-sm">سجل المبالغ التي سحبتها من أرباح العمل لاستخدامك الخاص</p>
            </div>
        </div>

        <div class="bg-purple-600 text-white px-8 py-5 rounded-[2.5rem] shadow-xl shadow-purple-100 flex items-center gap-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10 transition-transform group-hover:scale-150"></div>
            <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-md">
                <i class="fa-solid fa-hand-holding-dollar text-2xl"></i>
            </div>
            <div class="relative">
                <p class="text-[10px] font-black text-purple-200 uppercase tracking-[0.2em] mb-1">إجمالي ما تم سحبه</p>
                <p class="text-3xl font-black font-mono tracking-tighter">
                    {{ number_format($total) }} <span class="text-sm font-normal opacity-70">SDG</span>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-2 h-6 bg-purple-500 rounded-full"></div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">سجل التحويلات الشخصية</h3>
            </div>
            <span class="text-[10px] bg-purple-100 text-purple-700 px-4 py-1.5 rounded-xl font-black uppercase tracking-tighter">أحدث العمليات</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-white">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">تاريخ العملية</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">وصف السحب (البيان)</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-left">المبلغ المسحوب</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-purple-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-clock text-gray-300 group-hover:text-purple-400"></i>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-700 text-sm font-mono">{{ $t->created_at->format('Y-m-d') }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $t->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-purple-400 scale-0 group-hover:scale-100 transition-transform"></div>
                                <span class="font-bold text-gray-600 text-lg">{{ $t->description }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-left">
                            <div class="flex flex-col items-end">
                                <span class="text-2xl font-black text-slate-900 font-mono tracking-tighter italic">
                                    <span class="text-purple-500 ml-1">-</span>{{ number_format($t->amount) }}
                                </span>
                                <span class="text-[9px] font-black text-gray-300 uppercase">Personal Draw</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-piggy-bank text-gray-200 text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-400 uppercase">لا توجد مسحوبات</h3>
                                <p class="text-sm text-gray-300 font-bold mt-1">المبالغ التي تسحبها لأغراضك الخاصة ستظهر هنا بالتفصيل.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->isNotEmpty())
        <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center border-t-4 border-purple-500">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-purple-400 text-lg"></i>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">إجمالي الحركات: {{ $transactions->count() }}</span>
            </div>
            <div class="text-left">
                <p class="text-[9px] font-black text-purple-400 uppercase mb-1">صافي المسحوبات</p>
                <p class="text-3xl font-black font-mono tracking-tighter italic">{{ number_format($total) }} <span class="text-sm opacity-50">SDG</span></p>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection