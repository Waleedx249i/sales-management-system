@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <i class="fa-solid fa-map-location-dot text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">تقرير مبيعات العهد</h1>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1 italic">Consignment Sales Intelligence</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button onclick="printTable()" class="bg-rose-600 text-white px-6 py-3 rounded-xl font-black text-xs hover:bg-rose-700 transition-all flex items-center gap-2 shadow-lg shadow-rose-100">
                <i class="fa-solid fa-file-pdf"></i> تصدير PDF
            </button>

            <a href="?export=true&{{ http_build_query(request()->all()) }}" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-black text-xs hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fa-solid fa-file-excel"></i> تصدير Excel
            </a>
            
            <a href="{{ route('reports.store_sales') }}" class="bg-white border-2 border-slate-100 text-slate-500 px-6 py-3 rounded-xl font-black text-xs hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-shop"></i> تقارير المحل
            </a>
        </div>
    </div>

    <form class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-4 gap-4 border-l-2 border-slate-50 pl-8">
                <div class="col-span-4 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">خيارات الفلترة السريعة</span>
                </div>
                
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">نقطة التوزيع</label>
                    <select name="pos_name" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-xs text-slate-600 appearance-none">
                        <option value="">كل النقاط</option>
                        @foreach($posList as $name)
                            <option value="{{ $name }}" {{ request('pos_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">بالشهر</label>
                    <input type="month" name="month" value="{{ request('month') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-xs text-slate-600">
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 mr-2">بالسنة</label>
                    <input type="number" name="year" placeholder="2024" value="{{ request('year') }}" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-xs text-slate-600">
                </div>
            </div>

            <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">تحديد فترة مخصصة</span>
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
            <a href="{{ route('reports.pos_sales') }}" class="text-[10px] font-black text-slate-300 hover:text-rose-500 transition-colors uppercase tracking-[0.2em]">
                <i class="fa-solid fa-arrows-rotate mr-1"></i> إعادة ضبط
            </a>
            
            <button type="submit" class="bg-indigo-900 text-white px-12 py-4 rounded-2xl font-black text-xs hover:bg-indigo-600 transition-all shadow-xl flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-filter"></i>
                تطبيق الفلترة المتقدمة
            </button>
        </div>
    </form>

    <div id="printable-table" class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-indigo-50/30 text-indigo-400 text-[10px] font-black uppercase tracking-widest border-b border-indigo-50">
                    <th class="p-6">نقطة التوزيع</th>
                    <th class="p-6">اسم المنتج</th>
                    <th class="p-6 text-center">الكمية المباعة</th>
                    <th class="p-6 text-center italic">الإجمالي</th>
                    <th class="p-6 text-center text-emerald-600">صافي الربح</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $p)
                <tr class="hover:bg-indigo-50/10 transition-all group">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-black text-[10px]">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <span class="font-black text-slate-700">{{ $p->pos_name }}</span>
                        </div>
                    </td>
                    <td class="p-6">
                        <span class="font-bold text-slate-600 block text-sm">{{ $p->product_name }}</span>
                        <span class="text-[9px] text-slate-400 font-mono tracking-tighter">{{ $p->product_code }}</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-lg font-mono font-black text-xs">
                            {{ $p->quantity_sold }}
                        </span>
                    </td>
                    <td class="p-6 text-center font-mono font-black text-slate-700">
                        {{ number_format($p->total_amount) }}
                    </td>
                    <td class="p-6 text-center">
                        <div class="inline-flex items-center gap-1 text-emerald-600 font-black bg-emerald-50 px-3 py-1.5 rounded-lg text-sm">
                            <span class="font-mono">+{{ number_format($p->total_amount - $p->item_cost) }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-32 text-center">
                        <i class="fa-solid fa-map-pin text-6xl text-slate-100 mb-4 block"></i>
                        <p class="text-slate-400 font-black italic tracking-widest uppercase">No Consignment Records Found</p>
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

        document.body.innerHTML = `
            <div dir="rtl" style="padding: 40px; font-family: 'Arial';">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: #4f46e5;">تقرير مبيعات العهد</h2>
                    <span style="font-size: 12px; font-weight: bold;">تاريخ الطباعة: ${new Date().toLocaleDateString('ar-EG')}</span>
                </div>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #e2e8f0; padding: 12px; text-align: right; font-size: 13px; }
                    th { background-color: #f8fafc; color: #64748b; font-weight: 900; }
                    .text-emerald-600 { color: #059669; }
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
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700;900&family=JetBrains+Mono:wght@700&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection