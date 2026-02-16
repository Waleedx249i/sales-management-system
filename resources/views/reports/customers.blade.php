@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#fffcf9] min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-orange-100 rounded-xl text-orange-400 hover:text-orange-600 hover:border-orange-200 transition shadow-sm group">
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-users-between-lines text-orange-600"></i>
                    تقرير أرصدة العملاء والديون
                </h1>
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em] mt-1 italic">Customer Balances & Receivables Ledger</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="printCustomerReport()" class="bg-rose-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-rose-100 hover:bg-rose-700 transition flex items-center gap-2 font-black text-sm">
                <i class="fa-solid fa-print"></i>
                تصدير PDF
            </button>

            <a href="{{ request()->fullUrlWithQuery(['export' => 'true']) }}" class="bg-orange-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-orange-100 hover:bg-orange-700 transition flex items-center gap-2 font-black text-sm">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                تصدير Excel
            </a>
        </div>
    </div>

    <div class="relative overflow-hidden bg-white p-8 rounded-[2.5rem] shadow-sm border border-orange-100 mb-10 group">
        <div class="absolute -left-10 -top-10 w-40 h-40 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center gap-5 mb-4 md:mb-0">
                <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                    <i class="fa-solid fa-hand-holding-dollar text-2xl"></i>
                </div>
                <div>
                    <span class="text-orange-800 font-black text-sm block uppercase tracking-tighter">إجمالي الديون المستحقة على العملاء</span>
                    <span class="text-[10px] text-orange-400 font-bold italic">Total Outstanding Receivables</span>
                </div>
            </div>
            <div class="text-center md:text-left">
                <div class="flex items-baseline gap-2 justify-center md:justify-end">
                    <span class="text-4xl font-black font-mono tracking-tighter text-rose-600 animate-pulse">
                        {{ number_format($data->sum('total_debt')) }}
                    </span>
                    <span class="text-sm font-black text-rose-400 font-mono italic">SDG</span>
                </div>
            </div>
        </div>
    </div>

    <div id="printable-area" class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-800 text-slate-300 uppercase text-[10px] font-black tracking-widest border-b border-slate-700">
                        <th class="p-6">اسم العميل</th>
                        <th class="p-6 text-center">النشاط</th>
                        <th class="p-6 text-center">إجمالي المشتريات</th>
                        <th class="p-6 text-center">المبلغ المسدد</th>
                        <th class="p-6 text-center bg-rose-50/30 text-rose-500">المتبقي (دين)</th>
                        <th class="p-6 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($data as $row)
                    <tr class="hover:bg-orange-50/20 transition-colors group">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-slate-700">{{ $row->customer_name }}</span>
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            <span class="text-xs font-bold text-slate-400">{{ $row->invoices_count }} فواتير</span>
                        </td>
                        <td class="p-6 text-center">
                            <span class="font-bold text-slate-600 font-mono">{{ number_format($row->total_purchases) }}</span>
                        </td>
                        <td class="p-6 text-center">
                            <span class="font-black text-emerald-600 font-mono">{{ number_format($row->total_paid) }}</span>
                        </td>
                        <td class="p-6 text-center bg-rose-50/30">
                            <span class="font-black text-rose-600 font-mono text-lg tracking-tighter">
                                {{ number_format($row->total_debt) }}
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            @if($row->total_debt > 0)
                                <span style="color: #e11d48; font-size: 10px; font-weight: 900;">مطلوب سداد</span>
                            @else
                                <span style="color: #059669; font-size: 10px; font-weight: 900;">خالص</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-24 text-center text-slate-400 font-black italic">سجل العملاء نظيف، لا توجد ديون معلقة!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function printCustomerReport() {
        const printContents = document.getElementById('printable-area').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <div dir="rtl" style="padding: 20px; font-family: sans-serif;">
                <h2 style="text-align: center; color: #ea580c;">تقرير أرصدة العملاء والمديونيات</h2>
                <div style="text-align: center; margin-bottom: 20px; font-weight: bold;">
                    إجمالي الديون المعلقة: {{ number_format($data->sum('total_debt')) }} SDG
                </div>
                <style>
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #eee; padding: 12px 8px; text-align: right; font-size: 11px; }
                    th { background: #1e293b; color: #fff; }
                    .text-rose-600 { color: #e11d48; font-weight: bold; }
                    .text-emerald-600 { color: #059669; font-weight: bold; }
                </style>
                ${printContents}
            </div>`;
        
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection