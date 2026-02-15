@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 lg:p-12 text-right" dir="rtl">
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="bg-amber-500 p-8 text-white">
            <h2 class="text-2xl font-black">تعديل بيانات: {{ $supplier->name }}</h2>
        </div>
        
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="p-10 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">اسم المورد</label>
                <input type="text" name="name" value="{{ $supplier->name }}" required class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">الشخص المسؤول</label>
                    <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ $supplier->phone }}" required class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all text-left">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase mr-2">الوصف</label>
                <textarea name="description" rows="4" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:border-amber-500 transition-all resize-none">{{ $supplier->description }}</textarea>
            </div>

            <button type="submit" class="w-full bg-amber-500 text-white py-5 rounded-2xl font-black shadow-xl hover:bg-amber-600 transition-all">تحديث البيانات</button>
        </form>
    </div>
</div>
@endsection