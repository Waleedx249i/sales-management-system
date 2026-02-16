@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-100">
                <i class="fa-solid fa-file-invoice-dollar text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">تقارير المبيعات التفصيلية</h1>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Sales & Profit Analysis System</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button onclick="printTable()" class="bg-rose-600 text-white px-6 py-3 rounded-xl font-black text-xs hover:bg-rose-700 transition-all flex items-center gap-2 shadow-lg shadow-rose-100">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                تصدير PDF
            </button>

            <a href="?export=true&{{ http_build_query(request()->all()) }}" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-black text-xs hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-file-excel text-sm"></i>
                تصدير Excel
            </a>
            
            <button onclick="history.back()" class="bg-white border-2 border-slate-100 text-slate-500 px-6 py-3 rounded-xl font-black text-xs hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </button>
        </div>
    </div>

    <form class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8 space-y-6">
        <div class="flex flex-wrap lg:flex-nowrap gap-8">
            
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4 border-l-2 border-slate-50 pl-8">
                <div class="col-span-3 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">تصفية سريعة</span>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">اليوم</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-xs text-slate-600">
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">الشهر</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-xs text-slate-600">
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">السنة</label>
                    <input type="number" name="year" placeholder="2024" value="{{ request('year') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-xs text-slate-600">
                </div>
            </div>

            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">فترة زمنية مخصصة</span>
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-bold text-xs text-slate-600">
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-bold text-xs text-slate-600">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
            <a href="{{ route('reports.store_sales') }}" class="text-[10px] font-black text-slate-300 hover:text-rose-500 transition-colors uppercase tracking-[0.2em]">
                <i class="fa-solid fa-arrows-rotate mr-1"></i> إعادة تعيين الفلاتر
            </a>
            
            <button type="submit" class="bg-slate-900 text-white px-12 py-4 rounded-2xl font-black text-xs hover:bg-blue-600 transition-all shadow-xl flex items-center gap-3">
                <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                تحديث النتائج المفلترة
            </button>
        </div>
    </form>

    <div id="printable-table" class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50">
                    <th class="p-6">رقم الفاتورة</th>
                    <th class="p-6">اسم العميل</th>
                    <th class="p-6 text-center">الإيراد الكلي</th>
                    <th class="p-6 text-center text-emerald-600">صافي الربح</th>
                    <th class="p-6 text-center">تاريخ العملية</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $s)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="p-6">
                        <span class="font-black text-blue-600 bg-blue-50 px-4 py-2 rounded-xl text-xs">
                            #{{ $s->invoice_number }}
                        </span>
                    </td>
                    <td class="p-6">
                        <span class="font-black text-slate-700 block">{{ $s->customer_name }}</span>
                        <span class="text-[9px] text-slate-300 font-bold italic uppercase">Retail Customer</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-mono font-black text-slate-700 text-lg">
                            {{ number_format($s->final_amount, 2) }}
                        </span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="font-mono font-black text-emerald-600 text-lg">
                            +{{ number_format($s->final_amount - $s->total_cost, 2) }}
                        </span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="text-xs text-slate-400 font-black font-mono">
                            {{ $s->date_text }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-32 text-center">
                        <i class="fa-solid fa-inbox text-6xl text-slate-100 mb-4 block"></i>
                        <p class="text-slate-400 font-black italic tracking-widest">NO DATA FOUND FOR THIS PERIOD</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function printTable() {
        const printContents = document.getElementById('printable-table').innerHTML;
        const originalContents = document.body.innerHTML;

        // إعداد نافذة الطباعة
        document.body.innerHTML = `
            <div dir="rtl" style="padding: 40px; font-family: 'Arial';">
                <h2 style="text-align: center; margin-bottom: 20px;">تقرير المبيعات</h2>
                <style>
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #eee; padding: 12px; text-align: right; }
                    th { background-color: #f9f9f9; }
                    .text-emerald-600 { color: #059669; }
                    .text-blue-600 { color: #2563eb; }
                </style>
                ${printContents}
            </div>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // لإعادة تفعيل وظائف الصفحة بعد الطباعة
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700;900&family=JetBrains+Mono:wght@700&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection