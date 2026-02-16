@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#f8fafc] min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm group">
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 flex items-center gap-3">
                    <i class="fa-solid fa-truck-moving text-indigo-600"></i>
                    تقرير المشتريات والموردين
                </h1>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1 italic">Purchases & Suppliers Logistics Report</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="printPurchasesReport()" class="bg-rose-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-rose-100 hover:bg-rose-700 transition flex items-center gap-2 font-black text-sm">
                <i class="fa-solid fa-file-pdf"></i>
                تصدير PDF
            </button>

            <a href="{{ request()->fullUrlWithQuery(['export' => 'true']) }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition flex items-center gap-2 font-black text-sm">
                <i class="fa-solid fa-file-excel"></i>
                تصدير Excel
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] shadow-sm mb-8 border border-slate-100">
        <form action="{{ route('reports.purchases') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2 italic">
                    <i class="fa-solid fa-calendar-day ml-1"></i> تحديد يوم
                </label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border-slate-100 bg-slate-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-600">
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2 italic">
                    <i class="fa-solid fa-calendar-week ml-1"></i> تحديد شهر
                </label>
                <input type="month" name="month" value="{{ request('month') }}" class="w-full rounded-xl border-slate-100 bg-slate-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-600">
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2 italic">
                    <i class="fa-solid fa-calendar-check ml-1"></i> تحديد سنة
                </label>
                <select name="year" class="w-full rounded-xl border-slate-100 bg-slate-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-slate-600 appearance-none px-4 py-2">
                    <option value="">كل السنوات</option>
                    @foreach(range(date('Y'), date('Y')-5) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-slate-800 text-white px-6 py-3.5 rounded-xl shadow-lg shadow-slate-200 hover:bg-black transition font-black flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                تحديث التقرير
            </button>
        </form>
    </div>

    <div id="printable-area" class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-800 text-slate-300 uppercase text-[10px] font-black tracking-[0.15em]">
                        <th class="p-5 border-b border-slate-700"># الفاتورة</th>
                        <th class="p-5 border-b border-slate-700">المورد</th>
                        <th class="p-5 border-b border-slate-700 text-center">التاريخ</th>
                        <th class="p-5 border-b border-slate-700">قيمة البضاعة</th>
                        <th class="p-5 border-b border-slate-700">تكاليف لوجستية</th>
                        <th class="p-5 border-b border-slate-700 text-center">نسبة التكلفة %</th>
                        <th class="p-5 border-b border-slate-700 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="p-5">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-mono font-black text-xs">
                                {{ $row->invoice_number }}
                            </span>
                        </td>
                        <td class="p-5 font-black text-slate-700 italic flex items-center gap-2">
                            <i class="fa-solid fa-building-user text-slate-300 group-hover:text-indigo-500 transition-colors"></i>
                            {{ $row->supplier }}
                        </td>
                        <td class="p-5 text-center">
                            <span class="text-xs font-bold text-slate-400 font-mono italic">
                                {{ \Carbon\Carbon::parse($row->date_text)->format('Y-m-d') }}
                            </span>
                        </td>
                        <td class="p-5">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-800 font-mono text-lg">{{ number_format($row->total_goods_sdg) }}</span>
                                <span class="text-[9px] text-slate-400 font-bold tracking-widest uppercase">SDG</span>
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-2 text-rose-600 font-black font-mono">
                                {{ number_format($row->total_logistic) }}
                            </div>
                        </td>
                        <td class="p-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-600 font-black font-mono text-sm border border-blue-100">
                                {{ number_format($row->cost_ratio_percent, 1) }}%
                            </span>
                        </td>
                        <td class="p-5 text-center">
                            @if($row->status == 'completed')
                                <span class="px-3 py-1.5 text-[10px] font-black rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase italic">
                                    <i class="fa-solid fa-check-double ml-1"></i> مكتمل
                                </span>
                            @else
                                <span class="px-3 py-1.5 text-[10px] font-black rounded-xl bg-amber-50 text-amber-600 border border-amber-100 uppercase italic">
                                    <i class="fa-solid fa-spinner fa-spin ml-1"></i> قيد المعالجة
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-20 text-center">
                            <p class="text-slate-400 font-black italic">لا توجد مشتريات مسجلة في هذه الفترة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="bg-slate-50/80 font-black border-t-2 border-slate-100">
                    <tr class="text-slate-700">
                        <td colspan="3" class="p-6 text-center text-xs uppercase tracking-widest text-slate-400 italic">الإجمالي الكلي للتقرير</td>
                        <td class="p-6">
                            <span class="text-xl font-mono text-indigo-700 tracking-tighter">{{ number_format($data->sum('total_goods_sdg')) }}</span>
                        </td>
                        <td class="p-6">
                            <span class="text-xl font-mono text-rose-700 tracking-tighter">{{ number_format($data->sum('total_logistic')) }}</span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
    function printPurchasesReport() {
        const printContents = document.getElementById('printable-area').innerHTML;
        const originalContents = document.body.innerHTML;
        const dateStr = new Date().toLocaleDateString('ar-EG');

        document.body.innerHTML = `
            <div dir="rtl" style="padding: 30px; font-family: 'Arial', sans-serif;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1e293b; padding-bottom: 15px; margin-bottom: 30px;">
                    <div>
                        <h1 style="margin: 0; color: #1e293b; font-size: 22px;">تقرير المشتريات والخدمات اللوجستية</h1>
                        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 11px;">تاريخ التقرير: ${dateStr}</p>
                    </div>
                </div>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th { background-color: #1e293b !important; color: white !important; padding: 10px; font-size: 12px; text-align: right; }
                    td { border-bottom: 1px solid #e2e8f0; padding: 10px; font-size: 11px; }
                    .font-mono { font-family: monospace; font-weight: bold; }
                    .text-rose-600 { color: #e11d48; }
                    .text-indigo-700 { color: #4338ca; }
                    tfoot { background-color: #f8fafc; font-weight: bold; }
                </style>
                ${printContents}
            </div>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    body { font-family: 'Noto Kufi Arabic', sans-serif; }
</style>
@endsection