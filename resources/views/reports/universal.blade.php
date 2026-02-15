@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100 no-print">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            📊 استخراج التقارير الإدارية
        </h2>
        
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">نوع التقرير</label>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2">
                    <optgroup label="المبيعات">
                        <option value="sales_report" {{ request('type') == 'sales_report' ? 'selected' : '' }}>فواتير المبيعات</option>
                        <option value="top_products" {{ request('type') == 'top_products' ? 'selected' : '' }}>الأكثر مبيعاً</option>
                    </optgroup>
                    <optgroup label="المشتريات">
                        <option value="purchases_report" {{ request('type') == 'purchases_report' ? 'selected' : '' }}>فواتير الشراء</option>
                    </optgroup>
                    <optgroup label="المخزون">
                        <option value="inventory_status" {{ request('type') == 'inventory_status' ? 'selected' : '' }}>رصيد المخزن</option>
                        <option value="dead_stock" {{ request('type') == 'dead_stock' ? 'selected' : '' }}>الأصناف الراكدة</option>
                    </optgroup>
                    <optgroup label="المحاسبة">
                        <option value="profit_loss" {{ request('type') == 'profit_loss' ? 'selected' : '' }}>الأرباح والخسائر</option>
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">من تاريخ</label>
                <input type="date" name="from" value="{{ $from }}" class="w-full rounded-md border-gray-300 shadow-sm py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">إلى تاريخ</label>
                <input type="date" name="to" value="{{ $to }}" class="w-full rounded-md border-gray-300 shadow-sm py-2">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 font-bold transition">
                    تحديث
                </button>
                <button type="button" onclick="window.print()" class="bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition">
                    🖨️ طباعة
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden print:shadow-none print:border-none">
        <div class="p-5 bg-gray-50 border-b border-gray-200 flex justify-between items-center print:bg-white">
            <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
            <span class="text-sm text-gray-500">الفترة من {{ $from }} إلى {{ $to }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white print:bg-gray-100 print:text-black">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            @foreach((array)$row as $cell)
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium border-b border-gray-50">
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-400 italic">
                                لا توجد بيانات مسجلة لهذه الفترة...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        nav, footer, .sidebar { display: none !important; }
        body { background: white; }
        .container { max-width: 100% !important; width: 100%; margin: 0; padding: 0; }
        table { width: 100%; border: 1px solid #ddd; }
        th { background: #eee !important; color: black !important; }
    }
</style>
@endsection