@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto p-4 lg:p-8 space-y-6 text-right" dir="rtl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-slate-800">الموردين</h1>
            <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mt-1">إدارة جهات التوريد والشركات</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="bg-amber-500 text-white px-8 py-4 rounded-2xl font-black text-sm hover:bg-amber-600 transition-all shadow-xl shadow-amber-100 flex items-center gap-2">
            <span>+</span> إضافة مورد جديد
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                    <th class="p-6">اسم المورد</th>
                    <th class="p-6">المسؤول</th>
                    <th class="p-6 text-center">الهاتف</th>
                    <th class="p-6 text-center">الفواتير</th>
                    <th class="p-6 text-left">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($suppliers as $supplier)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="p-6 font-black text-slate-700">{{ $supplier->name }}</td>
                    <td class="p-6 font-bold text-slate-500">{{ $supplier->contact_person ?? '---' }}</td>
                    <td class="p-6 text-center font-mono text-xs text-slate-500">{{ $supplier->phone }}</td>
                    <td class="p-6 text-center">
                        <span class="bg-slate-100 px-3 py-1 rounded-lg text-[10px] font-black">{{ $supplier->import_invoices_count ?? 0 }} فاتورة</span>
                    </td>
                    <td class="p-6 flex justify-end gap-2">
                        <a href="{{ route('suppliers.show', $supplier->id) }}" class="p-2 text-slate-400 hover:text-indigo-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2 text-slate-400 hover:text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection