@extends('layouts.app')

@section('content')
<div class="max-w-[100rem] mx-auto p-4 lg:p-8 space-y-8 text-right" dir="rtl">
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-start gap-8">
        <div class="space-y-3">
            <span class="bg-amber-100 text-amber-600 px-4 py-1 rounded-full text-[10px] font-black uppercase">ملف مورد</span>
            <h1 class="text-5xl font-black text-slate-800 tracking-tighter">{{ $supplier->name }}</h1>
            <p class="text-slate-400 font-bold max-w-md">{{ $supplier->description ?? 'لا يوجد وصف مضاف لهذا المورد' }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 min-w-[160px]">
                <span class="block text-[10px] text-slate-400 font-black mb-1">المسؤول</span>
                <span class="text-lg font-black text-slate-700">{{ $supplier->contact_person ?? '---' }}</span>
            </div>
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 min-w-[160px]">
                <span class="block text-[10px] text-slate-400 font-black mb-1">الهاتف</span>
                <span class="text-lg font-black text-slate-700 font-mono">{{ $supplier->phone }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-800">سجل فواتير التوريد ({{ $supplier->importInvoices->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase border-b border-slate-50">
                        <th class="p-6">رقم الفاتورة</th>
                        <th class="p-6">تاريخ التوريد</th>
                        <th class="p-6 text-center">عدد الأصناف</th>
                        <th class="p-6 text-left">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($supplier->importInvoices as $invoice)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-6 font-black text-indigo-600">#{{ $invoice->invoice_number }}</td>
                        <td class="p-6 font-bold text-slate-500">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td class="p-6 text-center font-bold">{{ $invoice->items_count ?? 0 }}</td>
                        <td class="p-6 text-left font-black text-slate-700">{{ number_format($invoice->total_amount) }} SDG</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-slate-300 font-black uppercase tracking-widest text-xs">لا توجد فواتير توريد مسجلة حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection