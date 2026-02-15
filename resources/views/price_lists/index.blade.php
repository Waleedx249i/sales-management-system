@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 min-h-screen bg-[#fcfcfd]">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <i class="fa-solid fa-tags text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">إدارة جداول الأسعار</h1>
                <p class="text-sm text-slate-500 font-bold">تحديد قوائم الأسعار المختلفة وتفعيلها للعملاء</p>
            </div>
        </div>
        
        <a href="{{ route('price-lists.create') }}" class="group bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all flex items-center gap-3">
            <i class="fa-solid fa-plus-circle text-lg"></i>
            <span>إنشاء جدول جديد</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-2 text-slate-500">
            <i class="fa-solid fa-table-list"></i>
            <span class="text-xs font-black uppercase tracking-widest">القوائم المسجلة حالياً</span>
        </div>

        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="text-slate-400 text-[11px] font-black uppercase tracking-[0.15em] border-b border-slate-50">
                    <th class="p-6">اسم جدول الأسعار</th>
                    <th class="p-6 text-center">التسعير</th>
                    <th class="p-6 text-center">تاريخ الإنشاء</th>
                    <th class="p-6 text-center">الحالة</th>
                    <th class="p-6 text-center italic">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($priceLists as $list)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <span class="font-black text-slate-700 text-lg">{{ $list->name }}</span>
                        </div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-black inline-flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-[10px]"></i>
                            {{ $list->items_count }} صنف
                        </span>
                    </td>
                    <td class="p-6 text-center font-mono text-sm text-slate-400 font-bold">
                        <i class="fa-regular fa-calendar-alt ml-1"></i>
                        {{ $list->created_at->format('Y-m-d') }}
                    </td>
                    <td class="p-6">
                        <div class="flex justify-center">
                            <form action="{{ route('price-lists.toggle', $list->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 {{ $list->is_active ? 'bg-emerald-500' : 'bg-slate-200' }}">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-300 {{ $list->is_active ? '-translate-x-6' : '-translate-x-1' }}"></span>
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="p-6">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('price-lists.show', $list->id) }}" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="عرض التفاصيل">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>

                            <a href="{{ route('price-lists.edit', $list->id) }}" class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="تعديل">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>

                            <form action="{{ route('price-lists.destroy', $list->id) }}" method="POST" onsubmit="return confirm('حذف الجدول نهائياً؟')">
                                @csrf @method('DELETE')
                                <button class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="حذف">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($priceLists->isEmpty())
        <div class="p-20 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-tags text-4xl text-slate-200"></i>
            </div>
            <p class="text-slate-400 font-black tracking-tighter">لا توجد قوائم أسعار مسجلة بعد</p>
            <a href="{{ route('price-lists.create') }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline italic">أنشئ أول جدول الآن</a>
        </div>
        @endif
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection