@extends('layouts.app', ['title' => 'تعديل جدول أسعار: ' . $priceList->name])

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-6 min-h-screen bg-[#fcfcfd]">
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
        <div class="flex items-center gap-4">
            <div class="bg-amber-500 w-14 h-14 rounded-2xl text-white shadow-xl shadow-amber-100 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tighter">تعديل قائمة: {{ $priceList->name }}</h2>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Update Price List & Margins</p>
            </div>
        </div>
        <a href="{{ route('price-lists.index') }}" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all">
            <i class="fa-solid fa-xmark text-lg"></i>
        </a>
    </div>

    <form action="{{ route('price-lists.update', $priceList->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 relative overflow-visible">
            
            <div class="mb-10 relative z-10">
                <label class="flex items-center gap-2 text-sm font-black text-slate-700 mb-4 mr-2">
                    <i class="fa-solid fa-signature text-amber-500"></i>
                    اسم جدول الأسعار
                </label>
                <input type="text" name="name" value="{{ old('name', $priceList->name) }}" required 
                       class="w-full p-5 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl outline-none transition-all font-black text-slate-700 shadow-inner text-lg">
            </div>

            <div class="space-y-6 relative z-10">
                <div class="flex items-center justify-between px-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <i class="fa-solid fa-list-ol"></i>
                        تحديث تسعير المنتجات
                    </h3>
                    <span class="text-[10px] bg-indigo-50 px-3 py-1 rounded-full text-indigo-600 font-black border border-indigo-100 uppercase">
                        {{ $products->count() }} Total Items
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($products as $product)
                    @php 
                        // جلب التكلفة
                        $cost = $product->cost->weighted_average_cost ?? 0;

                        // تجهيز الصورة
                        $productImg = 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=f1f5f9&color=f59e0b&size=128';
                        if($product->image && file_exists(public_path('storage/' . $product->image))) {
                            $productImg = asset('storage/' . $product->image);
                        }
                    @endphp
                    
                    <div class="group flex flex-col md:flex-row items-start md:items-center justify-between p-5 bg-slate-50 rounded-[2.5rem] border border-slate-100 hover:border-amber-200 hover:bg-white hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300 gap-4">
                        
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <div class="w-16 h-16 bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 group-hover:border-amber-200 transition-colors">
                                    <img src="{{ $productImg }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-amber-500 rounded-lg flex items-center justify-center text-[10px] text-white shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <span class="font-black text-slate-700 group-hover:text-amber-600 transition-colors tracking-tight text-lg">{{ $product->name }}</span>
                                <div class="flex flex-wrap gap-3 mt-1">
                                    <span class="text-[10px] text-slate-400 font-black uppercase flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info text-[8px]"></i> الأساسي: {{ number_format($product->price) }} <small>SDG</small>
                                    </span>
                                    <span class="text-[10px] text-amber-600 font-black uppercase flex items-center gap-1 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100/50">
                                        <i class="fa-solid fa-hand-holding-dollar text-[8px]"></i> التكلفة: {{ number_format($cost) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative w-full md:w-auto">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-300 group-hover:text-amber-500 transition-colors">
                                <i class="fa-solid fa-money-bill-wave text-xs"></i>
                            </div>
                            <input type="number" step="0.01" name="prices[{{ $product->id }}][price]" 
                                   value="{{ old('prices.'.$product->id.'.price', $currentPrices[$product->id] ?? '') }}"
                                   class="w-full md:w-44 p-4 pr-10 bg-white border-2 border-slate-200 rounded-2xl text-center font-black text-amber-600 outline-none focus:ring-8 focus:ring-amber-500/5 focus:border-amber-500 shadow-sm transition-all text-xl" 
                                   placeholder="0.00" onfocus="this.select()">
                            <span class="absolute top-1/2 -translate-y-1/2 left-4 text-[9px] font-black text-slate-400 uppercase tracking-tighter">SDG</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mt-12 pt-8 border-t border-slate-50">
                <button type="submit" class="flex-[2] bg-slate-900 text-white py-5 rounded-[1.8rem] font-black shadow-2xl shadow-slate-200 hover:bg-black active:scale-95 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                    تحديث وإعتماد التعديلات
                </button>
                <a href="{{ route('price-lists.index') }}" class="flex-1 bg-slate-100 text-slate-500 py-5 rounded-[1.8rem] font-black hover:bg-slate-200 active:scale-95 transition-all text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-rotate-left text-xs text-slate-400"></i>
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>

<style>
    /* منع الأسهم في حقول الأرقام */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* تحسين شكل السكرول بار للصفحة */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f8fafc; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection