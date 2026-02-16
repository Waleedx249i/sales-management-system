@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 min-h-screen bg-[#f8fafc]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <i class="fa-solid fa-boxes-packing text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">مخزن المنتجات</h1>
                <p class="text-slate-500 font-bold text-xs uppercase tracking-widest mt-1 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                    إدارة وتتبع الأصناف الحية
                </p>
            </div>
        </div>
        
        <a href="{{ route('products.create') }}" class="group bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black transition-all shadow-xl shadow-indigo-100 flex items-center gap-3 active:scale-95">
            <i class="fa-solid fa-plus text-lg group-hover:rotate-90 transition-transform"></i>
            <span>إضافة منتج جديد</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-slate-300 text-[10px] font-black uppercase tracking-[0.2em]">
                        <th class="p-6 border-b border-slate-700">المعاينة</th>
                        <th class="p-6 border-b border-slate-700 text-center">كود الصنف</th>
                        <th class="p-6 border-b border-slate-700">اسم المنتج</th>
                        <th class="p-6 border-b border-slate-700 text-center">الكمية المتوفرة</th>
                        <th class="p-6 border-b border-slate-700 text-center">آخر تحديث</th>
                        <th class="p-6 border-b border-slate-700 text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($products as $product)
                    <tr class="hover:bg-indigo-50/30 transition-all group">
                        <td class="p-6">
                            <div class="relative w-14 h-14">
                                <img src="
    @if($product->image && file_exists(public_path($product->image)))
        {{ asset($product->image) }}
    @elseif($product->image && file_exists(public_path('storage/' . $product->image)))
        {{ asset('storage/' . $product->image) }}
    @else
        {{ 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=f1f5f9&color=64748b&size=512&font-size=0.45' }}
    @endif
    " 
    onerror="this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $product->name }}') + '&background=f1f5f9&color=64748b&size=512';"
    class="w-full h-full rounded-xl object-cover border-2 border-white shadow-md group-hover:scale-110 transition-transform"
    alt="{{ $product->name }}">
                                <div class="absolute inset-0 rounded-xl bg-black/5 group-hover:bg-transparent transition-colors"></div>
                            </div>
                        </td>

                        <td class="p-6 text-center">
                            <span class="font-mono font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg text-xs border border-indigo-100/50">
                                <i class="fa-solid fa-hashtag opacity-30 text-[9px]"></i> {{ $product->code }}
                            </span>
                        </td>

                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 group-hover:text-indigo-700 transition-colors">{{ $product->name }}</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">Inventory Item Assets</span>
                            </div>
                        </td>

                        <td class="p-6 text-center">
                            @if($product->quantity > 5)
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-black border border-emerald-100">
                                    <i class="fa-solid fa-check-circle text-[10px]"></i>
                                    {{ $product->quantity }} قطعة
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-black border border-rose-100 animate-pulse">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    {{ $product->quantity }} قطعة
                                </span>
                            @endif
                        </td>

                        <td class="p-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-500 font-mono italic">
                                    <i class="fa-regular fa-calendar-check ml-1 text-slate-300"></i>
                                    {{ $product->updated_at->format('Y-m-d') }}
                                </span>
                            </div>
                        </td>

                        <td class="p-6">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('products.show', $product->id) }}" 
                                   class="w-10 h-10 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" 
                                   title="عرض التفاصيل">
                                    <i class="fa-solid fa-circle-info text-lg"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}" 
                                   class="w-10 h-10 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" 
                                   title="تعديل">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" 
                                      onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذا المنتج نهائياً؟')">
                                    @csrf @method('DELETE')
                                    <button class="w-10 h-10 flex items-center justify-center text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm" 
                                            title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-slate-50 p-6 text-center border-t border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                Total of {{ $products->count() }} items found in warehouse
            </p>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    
    /* تحسين شكل السكرول بار */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection