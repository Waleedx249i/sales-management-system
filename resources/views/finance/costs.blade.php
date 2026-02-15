@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div class="flex items-center gap-5">
            <a href="{{ route('finance.index') }}" class="group p-3 bg-white border border-gray-100 rounded-2xl hover:bg-orange-600 hover:border-orange-600 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right text-gray-400 group-hover:text-white transition-colors"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase italic">
                    <i class="fa-solid fa-file-invoice-dollar text-orange-500 ml-2"></i> سجل التكاليف والنثريات
                </h1>
                <p class="text-gray-500 font-medium mt-1 text-sm">متابعة كافة المصاريف التشغيلية للمحل والمنصرفات العامة</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border-2 border-orange-50 flex items-center gap-6 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-16 h-16 bg-orange-500/5 rounded-full -mr-8 -mt-8 transition-all group-hover:scale-150"></div>
            <div class="p-4 bg-orange-100 text-orange-600 rounded-2xl">
                <i class="fa-solid fa-vault text-2xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">إجمالي المنصرفات</p>
                <p class="text-3xl font-black text-slate-900 font-mono tracking-tighter">
                    {{ number_format($total) }} <span class="text-sm font-bold opacity-40">SDG</span>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-list-check text-slate-400"></i>
                <h3 class="font-black text-slate-700 uppercase text-sm tracking-wider">تفاصيل الحركات المالية</h3>
            </div>
            <button class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-2">
                <i class="fa-solid fa-download"></i> تصدير التقرير
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-white">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">تاريخ العملية</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">البيان / الوصف</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">طريقة الدفع</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 text-left">القيمة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-orange-50/20 transition group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-calendar text-gray-300 group-hover:text-orange-400 transition-colors"></i>
                                <span class="text-sm font-bold text-gray-500 font-mono">{{ $t->created_at->format('Y-m-d') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="font-bold text-slate-700 text-lg">{{ $t->description }}</span>
                        </td>
                        <td class="px-8 py-6 text-sm">
                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-lg font-bold text-[10px] uppercase">
                                <i class="fa-solid fa-money-bill-transfer ml-1"></i> سحب نقدي
                            </span>
                        </td>
                        <td class="px-8 py-6 text-left">
                            <div class="inline-flex flex-col items-end">
                                <span class="text-xl font-black text-slate-900 font-mono tracking-tighter">
                                    <span class="text-red-500 text-sm">-</span>{{ number_format($t->amount) }}
                                </span>
                                <span class="text-[9px] font-black text-gray-300 uppercase">Sudanese Pound</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-magnifying-glass-dollar text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-400">لا توجد تكاليف مسجلة</h3>
                                <p class="text-sm text-gray-300 font-bold">كل المصاريف التي يتم تحويلها من الحساب الرئيسي ستظهر هنا.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->isNotEmpty())
        <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-circle-info text-orange-500"></i>
                <span class="text-xs font-bold text-slate-400 tracking-wide uppercase">إجمالي الحركات المسجلة في هذا القسم: {{ $transactions->count() }} عمليات</span>
            </div>
            <div class="text-left">
                <p class="text-[10px] font-black text-orange-400 uppercase">المجموع النهائي</p>
                <p class="text-2xl font-black font-mono tracking-tighter">{{ number_format($total) }} SDG</p>
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