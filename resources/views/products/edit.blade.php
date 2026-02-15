@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="max-w-3xl mx-auto px-4 py-8 min-h-screen">
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden relative">
        
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-8 text-white relative">
            <div class="absolute right-0 top-0 opacity-10 translate-x-8 -translate-y-8">
                <i class="fa-solid fa-pen-ruler text-[10rem]"></i>
            </div>
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30">
                    <i class="fa-solid fa-box-open text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight">تعديل بيانات المنتج</h1>
                    <p class="text-amber-100 text-[10px] font-black uppercase tracking-[0.2em] mt-1 italic">Product Configuration Mode</p>
                </div>
            </div>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12 space-y-8">
            @csrf
            @method('PUT')

            <div class="flex flex-col items-center justify-center mb-10 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-amber-400 rounded-[2rem] blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    <img id="preview-image" src="{{ $product->image ? asset('storage/'.$product->image) : asset('default-product.png') }}" 
                         class="relative w-40 h-40 rounded-[2.5rem] object-cover border-4 border-white shadow-xl z-10 transition-transform group-hover:scale-105">
                    
                    <label for="image-upload" class="absolute -bottom-2 -right-2 bg-slate-900 text-white w-10 h-10 rounded-2xl flex items-center justify-center cursor-pointer hover:bg-amber-500 transition-colors z-20 shadow-lg border-2 border-white">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </label>
                    <input id="image-upload" type="file" name="image" class="hidden" onchange="previewImage(event)">
                </div>
                <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">اضغط على الأيقونة لتغيير الصورة</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-tighter mr-2">
                        <i class="fa-solid fa-signature text-amber-500"></i> اسم المنتج
                    </label>
                    <input type="text" name="name" value="{{ $product->name }}" required
                           class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-50 outline-none transition-all font-bold text-slate-700 placeholder:text-slate-300 shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-tighter mr-2">
                        <i class="fa-solid fa-barcode text-amber-500"></i> كود الصنف (SKU)
                    </label>
                    <input type="text" name="code" value="{{ $product->code }}" required
                           class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-50 outline-none transition-all font-mono font-bold text-indigo-600 shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-tighter mr-2">
                        <i class="fa-solid fa-cubes-stacked text-amber-500"></i> الكمية المتاحة
                    </label>
                    <div class="relative">
                        <input type="number" name="quantity" value="{{ $product->quantity }}" required
                               class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-50 outline-none transition-all font-black text-slate-700 shadow-sm">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 italic">UNIT</span>
                    </div>
                </div>

                <div class="space-y-2 md:col-span-1">
                     <label class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-tighter mr-2">
                        <i class="fa-solid fa-align-right text-amber-500"></i> ملاحظات المنتج
                    </label>
                    <textarea name="description" rows="1" 
                              class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-50 outline-none transition-all font-bold text-slate-600 shadow-sm">{{ $product->description }}</textarea>
                </div>
            </div>

            <div class="pt-10 flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-[2] bg-slate-900 hover:bg-amber-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-slate-200 transition-all flex items-center justify-center gap-3 active:scale-95 group">
                    <i class="fa-solid fa-cloud-arrow-up group-hover:animate-bounce"></i>
                    حفظ التغييرات الجديدة
                </button>
                
                <a href="{{ route('products.index') }}" class="flex-1 bg-slate-100 text-slate-500 hover:bg-slate-200 font-black py-5 rounded-[1.5rem] transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-left"></i>
                    إلغاء والعودة
                </a>
            </div>
        </form>
        
        <div class="bg-slate-50 p-6 text-center border-t border-slate-100">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.4em]">Asset Management Integrity Protocol</p>
        </div>
    </div>
</div>

<script>
    // دالة معاينة الصورة عند اختيار ملف جديد
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('preview-image');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700;900&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; background-color: #fbfcfd; }
    
    /* تحسين شكل الإدخال للأرقام ليكون نظيفاً */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; margin: 0; 
    }
</style>
@endsection