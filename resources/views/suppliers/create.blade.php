@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 lg:p-12 text-right" dir="rtl">
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="bg-slate-900 p-8 text-white">
            <h2 class="text-2xl font-black">إضافة مورد جديد</h2>
            <p class="text-slate-400 text-xs mt-1 font-bold italic">أدخل بيانات الشركة أو المورد الأساسية</p>
        </div>
        
        <form action="{{ route('suppliers.store') }}" method="POST" class="p-10 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">اسم المورد أو الشركة</label>
                <input type="text" name="name" required onfocus="this.select()" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:ring-4 focus:ring-amber-500/5 focus:border-amber-500 transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">الشخص المسؤول</label>
                    <input type="text" name="contact_person" onfocus="this.select()" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">رقم الهاتف</label>
                    <input type="text" name="phone" required onfocus="this.select()" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-mono font-bold outline-none focus:border-amber-500 transition-all text-left">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">وصف/ملاحظات المورد</label>
                <textarea name="description" rows="4" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all resize-none"></textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-5 rounded-2xl font-black shadow-xl hover:bg-black transition-all">حفظ المورد</button>
                <a href="{{ route('suppliers.index') }}" class="px-8 bg-slate-100 text-slate-400 py-5 rounded-2xl font-black hover:bg-slate-200 transition-all">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection