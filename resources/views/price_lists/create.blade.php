@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 min-h-screen bg-[#fcfcfd]">
    <div class="flex items-center gap-4 mb-8 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
        <div class="bg-indigo-600 w-14 h-14 rounded-2xl text-white shadow-xl shadow-indigo-100 flex items-center justify-center">
            <i class="fa-solid fa-file-circle-plus text-2xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tighter">إنشاء قائمة أسعار جديدة</h2>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Setup New Price Strategy</p>
        </div>
    </div>

    <form action="{{ route('price-lists.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 relative overflow-visible">
            
            <div class="mb-10 relative z-10">
                <label class="flex items-center gap-2 text-sm font-black text-slate-700 mb-4 mr-2">
                    <i class="fa-solid fa-pen-fancy text-indigo-500"></i>
                    اسم القائمة (مثلاً: أسعار تجار الجملة)
                </label>
                <input type="text" name="name" required 
                       class="w-full p-5 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl outline-none transition-all font-black text-slate-700 shadow-inner text-lg placeholder:text-slate-300" 
                       placeholder="أدخل اسماً يميز هذا الجدول...">
            </div>

            <div class="space-y-4 relative z-10">
                <div class="flex items-center justify-between px-2 mb-6">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-layer-group"></i>
                        حدد الأسعار للأصناف بناءً على التكلفة:
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($products as $product)
                    @php 
                        // جلب التكلفة
                        $cost = $product->cost->weighted_average_cost ?? 0;
                        
                        // تجهيز الصورة
                        $productImg = 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=f1f5f9&color=6366f1&size=128';
                        if($product->image && file_exists(public_path('storage/' . $product->image))) {
                            $productImg = asset('storage/' . $product->image);
                        }
                    @endphp
                    
                    <div class="group flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:border-indigo-200 hover:bg-white hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 gap-4">
                        
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="w-16 h-16 bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 group-hover:border-indigo-200 transition-colors">
                                    <img src="{{ $productImg }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-indigo-600 rounded-lg flex items-center justify-center text-[10px] text-white shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 group-hover:text-indigo-600 transition-colors tracking-tight text-lg">{{ $product->name }}</span>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-[10px] text-amber-600 font-black uppercase flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-md border border-amber-100/50 w-fit">
                                        <i class="fa-solid fa-hand-holding-dollar text-[8px]"></i> 
                                        التكلفة: {{ number_format($cost) }} <small class="text-[8px] mr-0.5">SDG</small>
                                    </span>
                                    @if($product->sku)
                                    <span class="text-[10px] text-slate-400 font-bold bg-white px-2 py-1 rounded-md border border-slate-100 w-fit">
                                        #{{ $product->sku }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="relative w-full md:w-auto">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-300 group-hover:text-indigo-400 transition-colors">
                                <i class="fa-solid fa-tag text-xs"></i>
                            </div>
                            <input type="number" step="0.01" name="prices[{{ $product->id }}][price]" 
                                   class="w-full md:w-44 p-4 pr-10 bg-white border-2 border-slate-200 rounded-2xl text-center font-black text-indigo-600 outline-none focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 shadow-sm transition-all text-xl" 
                                   placeholder="0.00" onfocus="this.select()">
                            <span class="absolute top-1/2 -translate-y-1/2 left-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter">SDG</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-12 flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-[2] bg-indigo-600 text-white py-5 rounded-[1.8rem] font-black shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-check-circle text-lg"></i>
                    حفظ واعتماد القائمة
                </button>
                <a href="{{ route('price-lists.index') }}" class="flex-1 bg-slate-100 text-slate-500 py-5 rounded-[1.8rem] font-black hover:bg-slate-200 active:scale-95 transition-all text-center flex items-center justify-center">
                    إلغاء العملية
                </a>
            </div>
        </div>
    </form>
</div>

<style>
    /* تحسينات إضافية CSS */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    
    /* تنعيم حركة التمرير */
    html {
        scroll-behavior: smooth;
    }
    
    /* تحسين استجابة الحقول في الجوال */
    @media (max-width: 768px) {
        .group {
            border-radius: 2rem;
        }
    }
</style>
@endsection