@extends('layouts.app', ['title' => 'أرشيف فواتير الوارد'])

@section('content')
<div class="max-w-[100rem] mx-auto p-6 space-y-8">

    {{-- Header Section --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16"></div>
        
        <div class="flex items-center gap-5 relative">
            <div class="bg-indigo-600 w-16 h-16 rounded-[2rem] flex items-center justify-center text-white shadow-xl shadow-indigo-100 rotate-3">
                <i class="fa-solid fa-box-archive text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">أرشيف فواتير الوارد</h2>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em] flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-400"></i> نظام إدارة وتتبع الشحنات
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 relative">
            <button onclick="window.print()" class="flex items-center gap-3 bg-slate-800 text-white px-6 py-4 rounded-[1.5rem] font-black text-xs hover:bg-black transition-all shadow-xl shadow-slate-200 uppercase tracking-widest">
                <i class="fa-solid fa-print text-lg"></i> طباعة التقرير العام
            </button>
            <a href="{{ route('import-invoices.create') }}" class="flex items-center gap-3 bg-indigo-600 text-white px-6 py-4 rounded-[1.5rem] font-black text-xs hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 uppercase tracking-widest">
                <i class="fa-solid fa-plus text-lg"></i> إضافة فاتورة جديدة
            </a>
        </div>
    </div>

    {{-- Filters Form --}}
    <form action="{{ route('import-invoices.index') }}" method="GET" class="bg-slate-50 p-8 rounded-[3rem] border border-slate-100 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="relative group">
                <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="رقم الفاتورة..." 
                       class="w-full pr-12 pl-4 py-4 bg-white border-2 border-transparent rounded-[1.5rem] text-sm font-bold outline-none focus:border-indigo-500 shadow-sm transition-all">
            </div>

            <div class="relative group">
                <i class="fa-solid fa-filter absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select name="status" class="w-full pr-12 pl-4 py-4 bg-white border-2 border-transparent rounded-[1.5rem] text-sm font-bold outline-none focus:border-indigo-500 shadow-sm appearance-none transition-all">
                    <option value="">كل الحالات</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة (مخزنياً)</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>مسودة (عرض سعر)</option>
                </select>
            </div>

            <div class="relative group">
                <i class="fa-solid fa-calendar-day absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="w-full pr-12 pl-4 py-4 bg-white border-2 border-transparent rounded-[1.5rem] text-sm font-bold outline-none focus:border-indigo-500 shadow-sm transition-all uppercase">
            </div>
            
            <div class="relative group">
                <i class="fa-solid fa-calendar-check absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="w-full pr-12 pl-4 py-4 bg-white border-2 border-transparent rounded-[1.5rem] text-sm font-bold outline-none focus:border-indigo-500 shadow-sm transition-all uppercase">
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-6 pt-4 border-t border-slate-200/50">
            <div class="flex items-center gap-3">
                <input type="hidden" name="filter_type" id="filter_type" value="{{ request('filter_type', 'custom') }}">
                
                <button type="submit" onclick="document.getElementById('filter_type').value='today'" 
                        class="flex items-center gap-2 px-5 py-3 rounded-xl text-[10px] font-black transition-all {{ request('filter_type') == 'today' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border border-slate-200' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> فواتير اليوم
                </button>
                
                <button type="submit" onclick="document.getElementById('filter_type').value='this_month'" 
                        class="flex items-center gap-2 px-5 py-3 rounded-xl text-[10px] font-black transition-all {{ request('filter_type') == 'this_month' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border border-slate-200' }}">
                    <i class="fa-solid fa-chart-line"></i> فواتير الشهر
                </button>
                
                <button type="submit" onclick="document.getElementById('filter_type').value='custom'" 
                        class="flex items-center gap-2 px-6 py-3 rounded-xl text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100 hover:bg-indigo-100 transition-all">
                    تطبيق البحث المتقدم <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>

            <a href="{{ route('import-invoices.index') }}" class="flex items-center gap-2 text-[10px] font-black text-red-400 hover:text-red-600 transition-colors uppercase tracking-widest">
                <i class="fa-solid fa-rotate-right"></i> إعادة ضبط الفلاتر
            </a>
        </div>
    </form>

    {{-- Table Section --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden printable relative">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="p-6">معلومات الفاتورة</th>
                        <th class="p-6 text-center">التاريخ</th>
                        <th class="p-6">المورد</th>
                        <th class="p-6">الإجمالي (SDG)</th>
                        <th class="p-6 text-center">نسبة التحميل</th>
                        <th class="p-6 text-center">الحالة</th>
                        <th class="p-6 text-center no-print">خيارات التحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($invoices as $invoice)
                    <tr class="group hover:bg-slate-50/80 transition-all">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-xs">
                                    #
                                </div>
                                <div>
                                    <span class="block font-black text-slate-700 font-mono tracking-tighter">INV-{{ $invoice->invoice_number }}</span>
                                    <span class="flex items-center gap-1 text-[9px] text-slate-400 font-bold italic">
                                        <i class="fa-solid fa-layer-group text-[8px]"></i> {{ $invoice->items_count }} أصناف مسجلة
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-center font-mono text-xs font-bold text-slate-500">
                            {{ $invoice->created_at->format('Y/m/d') }}
                        </td>
                        <td class="p-6">
                            <span class="flex items-center gap-2 font-black text-slate-800 text-xs">
                                <i class="fa-solid fa-user-tie text-slate-300"></i>
                                {{ $invoice->supplier ? $invoice->supplier->name : '— غير محدد —' }}
                            </span>
                        </td>
                        <td class="p-6">
                            <span class="font-black text-slate-900 text-sm font-mono italic bg-slate-100 px-3 py-1 rounded-lg">
                                {{ number_format($invoice->total_goods_sdg + $invoice->total_logistic) }}
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[10px] font-black px-3 py-1 rounded-full {{ $invoice->cost_ratio_display > 20 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-500' }}">
                                    +{{ number_format($invoice->cost_ratio_display, 1) }}%
                                </span>
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            <form action="{{ route('import-invoices.update-status', $invoice->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 py-2 px-4 rounded-xl text-[10px] font-black transition-all hover:scale-105 shadow-sm
                                    {{ $invoice->status == 'completed' ? 'bg-emerald-600 text-white' : 'bg-amber-400 text-amber-900' }}">
                                    <i class="fa-solid {{ $invoice->status == 'completed' ? 'fa-check-double' : 'fa-pen-ruler' }}"></i>
                                    {{ $invoice->status == 'completed' ? 'مكتملة' : 'مسودة' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-6 no-print">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('import-invoices.show', $invoice->id) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all hover:shadow-lg hover:shadow-indigo-100" title="عرض">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('import-invoices.edit', $invoice->id) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:bg-amber-500 hover:text-white transition-all hover:shadow-lg hover:shadow-amber-100" title="تعديل">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <a href="{{ route('import-invoices.print', $invoice->id) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all hover:shadow-lg hover:shadow-slate-200" title="طباعة">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                                <form action="{{ route('import-invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-100 text-red-300 rounded-xl hover:bg-red-600 hover:text-white transition-all hover:shadow-lg hover:shadow-red-100" title="حذف">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-20 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <i class="fa-solid fa-folder-open text-6xl mb-4"></i>
                                <p class="font-black uppercase tracking-widest text-xs">لا يوجد فواتير مطابقة للبحث</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Custom Pagination Section --}}
        @if($invoices->hasPages())
        <div class="p-8 bg-slate-50/50 border-t border-slate-50">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        عرض من <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $invoices->firstItem() }}</span> 
                        إلى <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $invoices->lastItem() }}</span> 
                        من أصل <span class="text-indigo-600 italic font-mono text-sm mx-1">{{ $invoices->total() }}</span> فاتورة
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex items-center gap-2" aria-label="Pagination">
                        {{-- Previous --}}
                        @if ($invoices->onFirstPage())
                            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-200 cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group">
                                <i class="fa-solid fa-chevron-right text-xs group-hover:-translate-x-1 transition-transform"></i>
                            </a>
                        @endif

                        {{-- Pages --}}
                        <div class="flex items-center gap-1">
                            @foreach ($invoices->getUrlRange(max(1, $invoices->currentPage() - 2), min($invoices->lastPage(), $invoices->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all 
                                   {{ $page == $invoices->currentPage() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Next --}}
                        @if ($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group">
                                <i class="fa-solid fa-chevron-left text-xs group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @else
                            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-200 cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .printable, .printable * { visibility: visible; }
        .printable { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .bg-white { border: none !important; box-shadow: none !important; }
    }
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('tbody tr:not(.empty-row)');
        
        rows.forEach(row => {
            let invNumber = row.querySelector('td:first-child').innerText.toLowerCase();
            let supplier = row.querySelector('td:nth-child(3)').innerText.toLowerCase();
            
            if(invNumber.includes(filter) || supplier.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection