@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8 bg-[#fcfdfd] min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition shadow-sm">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <h1 class="text-2xl font-black text-slate-800 italic">تفاصيل المنتج</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.edit', $product->id) }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition flex items-center gap-2 font-bold text-sm">
                <i class="fa-solid fa-pen-to-square"></i> تعديل
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-4 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="aspect-square rounded-[2rem] overflow-hidden bg-slate-50 border border-slate-50 relative group">
                    @if($product->image)
                       <img 
    src="
        @if($product->image && file_exists(public_path($product->image)))
            {{ asset($product->image) }}
        @elseif($product->image && file_exists(public_path('storage/' . $product->image)))
            {{ asset('storage/' . $product->image) }}
        @else
            {{ 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&color=7F9CF5&background=EBF4FF&size=512&font-size=0.45' }}
        @endif
    " 
    class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
    alt="{{ $product->name }}"
    onerror="this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $product->name }}') + '&color=7F9CF5&background=EBF4FF&size=512';"
>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <i class="fa-solid fa-image text-6xl mb-2"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">No Image</span>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4">
                        <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg shadow-sm text-[10px] font-black text-indigo-600 border border-indigo-50">
                            {{ $product->code }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <h2 class="text-xl font-black text-slate-800">{{ $product->name }}</h2>
                    <p class="text-slate-400 text-sm mt-2 leading-relaxed px-4 italic">
                        {{ $product->description ?? 'لا يوجد وصف لهذا المنتج حالياً.' }}
                    </p>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2rem] p-6 text-white relative overflow-hidden shadow-xl">
                <i class="fa-solid fa-cubes absolute -right-4 -bottom-4 text-7xl opacity-10"></i>
                <div class="relative z-10">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">المخزون الحالي</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-4xl font-black font-mono text-emerald-400">{{ $product->quantity }}</span>
                        <span class="text-sm font-bold opacity-60 italic">قطعة</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-black text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-rose-500"></i>
                        هيكل التكلفة (Costing)
                    </h3>
                </div>
                <div class="p-8">
                    @if($product->cost)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-1">سعر الشراء</span>
                                <span class="text-lg font-black font-mono text-slate-700">{{ number_format($product->cost->purchase_price) }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase mb-1">لوجستيك</span>
                                <span class="text-lg font-black font-mono text-rose-600">+ {{ number_format($product->cost->logistic_cost) }}</span>
                            </div>
                            <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                                <span class="block text-[10px] font-black text-indigo-400 uppercase mb-1">صافي التكلفة</span>
                                <span class="text-xl font-black font-mono text-indigo-700">{{ number_format($product->cost->total_cost) }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center">
                                <span class="text-[10px] font-bold text-slate-400 italic">آخر تحديث: <br> {{ $product->cost->updated_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center border-2 border-dashed border-slate-100 rounded-[2rem]">
                            <i class="fa-solid fa-calculator text-slate-200 text-4xl mb-3"></i>
                            <p class="text-slate-400 font-bold italic">لم يتم تحديد تكاليف لهذا المنتج بعد</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-black text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-tags text-teal-500"></i>
                        قوائم أسعار البيع
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="p-6">القائمة / النوع</th>
                                <th class="p-6 text-center">سعر البيع</th>
                                <th class="p-6 text-center">هامش الربح</th>
                                <th class="p-6 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($product->priceListItems as $item)
                            <tr class="group hover:bg-slate-50/50 transition">
                                <td class="p-6">
                                    <span class="font-black text-slate-700 group-hover:text-teal-600 transition">{{ $item->priceList->name ?? 'قائمة افتراضية' }}</span>
                                </td>
                                <td class="p-6 text-center">
                                    <span class="font-black font-mono text-lg text-slate-800">{{ number_format($item->price) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">SDG</span>
                                </td>
                                <td class="p-6 text-center">
                                    @php
                                        $profit = $product->cost ? ($item->price - $product->cost->total_cost) : 0;
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 font-black font-mono text-sm border border-emerald-100">
                                        +{{ number_format($profit) }}
                                    </span>
                                </td>
                                <td class="p-6 text-center">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-slate-400 font-bold italic">لا توجد أسعار بيع مسجلة لهذا المنتج</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&family=Noto+Kufi+Arabic:wght@400;700;900&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>
@endsection