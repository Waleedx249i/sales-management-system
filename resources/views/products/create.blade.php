@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 min-h-screen">
    
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-indigo-100/50 border border-slate-100 overflow-hidden relative">
        
        <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-violet-800 p-10 text-white relative">
            <div class="absolute left-0 top-0 opacity-10 -translate-x-10 -translate-y-10">
                <i class="fa-solid fa-cart-plus text-[12rem]"></i>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-xl rounded-[1.5rem] flex items-center justify-center border border-white/20 shadow-inner">
                        <i class="fa-solid fa-plus text-2xl text-indigo-100"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black tracking-tight">إضافة منتج جديد</h1>
                        <p class="text-indigo-200/80 text-xs font-bold uppercase tracking-[0.2em] mt-1 flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            توسيع مخزونك الرقمي الآن
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <div class="lg:col-span-1 flex flex-col items-center">
                    <div class="relative group cursor-pointer w-full max-w-[240px]">
                        <div id="drop-zone" class="aspect-square rounded-[2.5rem] border-4 border-dashed border-slate-100 bg-slate-50/50 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-indigo-300 hover:bg-indigo-50/30">
                            
                            <div id="placeholder-elements" class="flex flex-col items-center p-6 text-center">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                </div>
                                <p class="text-xs font-black text-slate-500 uppercase leading-relaxed">اسحب الصورة هنا أو اضغط للاختيار</p>
                            </div>

                            <img id="image-preview" src="#" class="hidden w-full h-full object-cover rounded-[2.2rem]">
                        </div>
                        
                        <label for="image-input" class="absolute -bottom-3 -right-3 w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg hover:bg-indigo-700 transition-all cursor-pointer border-4 border-white">
                            <i class="fa-solid fa-camera"></i>
                        </label>
                        <input type="file" name="image" id="image-input" accept="image/*" class="hidden">
                    </div>
                    <p class="mt-6 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">مدعوم: JPG, PNG, WEBP</p>
                </div>

                <div class="lg:col-span-2 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-tighter mr-2 flex items-center gap-2">
                                <i class="fa-solid fa-tag text-indigo-500"></i> اسم الصنف
                            </label>
                            <input type="text" name="name" required placeholder="مثال: آيفون 15 برو"
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition-all font-bold text-slate-700 shadow-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-tighter mr-2 flex items-center gap-2">
                                <i class="fa-solid fa-fingerprint text-indigo-500"></i> الكود الفريد (SKU)
                            </label>
                            <input type="text" name="code" required placeholder="PRO-2024-001"
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition-all font-mono font-black text-indigo-600 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-tighter mr-2 flex items-center gap-2">
                                <i class="fa-solid fa-boxes-stacked text-indigo-500"></i> الرصيد الافتتاحي
                            </label>
                            <input type="number" name="quantity" value="0" required
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition-all font-black text-slate-700 shadow-sm">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-tighter mr-2 flex items-center gap-2">
                                <i class="fa-solid fa-comment-dots text-indigo-500"></i> وصف الصنف
                            </label>
                            <textarea name="description" rows="1" placeholder="أضف وصفاً مختصراً..."
                                      class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 outline-none transition-all font-bold text-slate-600 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="pt-8 flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-[2] bg-indigo-900 hover:bg-indigo-950 text-white font-black py-5 rounded-[1.8rem] shadow-xl shadow-indigo-200 transition-all flex items-center justify-center gap-3 active:scale-95 group">
                            <i class="fa-solid fa-check-double text-emerald-400 group-hover:scale-125 transition-transform"></i>
                            تأكيد وحفظ الصنف الجديد
                        </button>
                        
                        <a href="{{ route('products.index') }}" class="flex-1 bg-slate-100 text-slate-500 hover:bg-slate-200 font-black py-5 rounded-[1.8rem] transition-all text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-xmark"></i>
                            تجاهل
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="bg-indigo-50/50 p-6 text-center border-t border-slate-100">
            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.5em]">Inventory System Secure Input v2.0</p>
        </div>
    </div>
</div>

<script>
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const placeholder = document.getElementById('placeholder-elements');
    const dropZone = document.getElementById('drop-zone');

    imageInput.onchange = evt => {
        const [file] = imageInput.files;
        if (file) {
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            dropZone.style.borderStyle = 'solid';
        }
    }

    // إضافة تأثير السحب والإفلات (Visual Only)
    dropZone.onclick = () => imageInput.click();
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700;900&display=swap');
    body { font-family: 'Noto Kufi Arabic', sans-serif; background-color: #f8fafc; }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endsection