@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8 min-h-screen bg-[#fcfcfd]" x-data="{ open: false, type: '', title: '', icon: '' }">
    
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-200">
                <i class="fa-solid fa-chart-pie-simple text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase italic">الإدارة المالية والتدفق النقدي</h1>
                <p class="text-gray-500 font-medium">تحليل رأس المال، تكلفة المبيعات، وصافي الأرباح المتاحة</p>
            </div>
        </div>
        <div class="flex gap-3">
             <a href="{{ route('reports.sales') }}" class="bg-white border-2 border-gray-100 px-6 py-3 rounded-2xl text-sm font-black text-gray-600 shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-chart-line"></i> تقارير المبيعات التفصيلية
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden group border-b-8 border-emerald-500">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full -mr-16 -mt-16"></div>
            
            <div class="flex justify-between items-start mb-6 relative">
                <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest">إجمالي رأس المال</span>
                <i class="fa-solid fa-boxes-stacked text-emerald-500/30 text-xl"></i>
            </div>
            
            <div class="space-y-6 relative">
                <div class="flex flex-col">
                    <span class="text-4xl font-black font-mono tracking-tighter">{{ number_format($workingCapital) }} <span class="text-xs opacity-40">SDG</span></span>
                    <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">القيمة الكلية للبضاعة التي تملكها (الموجودة في المخزن + التي تم بيعها للعملاء).</p>
                </div>

                <div class="pt-4 border-t border-white/5 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[9px] text-slate-500 uppercase font-bold">تكلفة المبيعات فقط:</span>
                        <span class="text-sm font-black text-emerald-400">{{ number_format($totalSoldCost) }}</span>
                    </div>
                    <i class="fa-solid fa-circle-info text-slate-700" title="قيمة البضاعة التي خرجت من مخزنك بسعر التكلفة"></i>
                </div>
            </div>
        </div>

        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative border-b-8 border-indigo-500">
            <div class="flex justify-between items-start mb-6 relative">
                <span class="bg-indigo-500/20 text-indigo-300 text-[10px] px-3 py-1 rounded-full font-black uppercase tracking-widest">صافي الربح الفعلي</span>
                <i class="fa-solid fa-sack-dollar text-indigo-500/30 text-xl"></i>
            </div>
            
            <div class="space-y-6 relative">
                <div class="flex flex-col">
                    <span class="text-4xl font-black font-mono tracking-tighter text-indigo-100">{{ number_format($remainingMainProfit) }} <span class="text-xs opacity-40">SDG</span></span>
                    <p class="text-[10px] text-indigo-300 mt-2 leading-relaxed">الأرباح المتوفرة حالياً "كاش" بعد استرداد تكلفة البضاعة وخصم كافة المصاريف والمسحوبات.</p>
                </div>

                <div class="pt-4 border-t border-white/5 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[9px] text-indigo-400 uppercase font-bold">قيمة المخزن حالياً:</span>
                        <span class="text-sm font-black text-white">{{ number_format($inventoryValue) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button @click="open = true; type = 'costs'; title = 'تحويل ميزانية تشغيل'; icon = 'fa-sack-dollar'" 
                        class="flex-1 bg-white/5 hover:bg-white/10 py-3 rounded-xl text-[9px] font-black uppercase transition-all border border-white/5">
                    تحويل تكاليف
                </button>
                <button @click="open = true; type = 'personal_profits'; title = 'سحب أرباح شخصية'; icon = 'fa-hand-holding-dollar'" 
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 py-3 rounded-xl text-[9px] font-black uppercase transition-all shadow-lg">
                    سحب أرباح
                </button>
            </div>
        </div>

        <a href="{{ route('finance.costs') }}" class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all border-t-8 border-t-orange-500 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">مصاريف التشغيل</p>
                    <p class="text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ number_format($costsBalance) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mb-6 italic leading-snug">إجمالي المبالغ التي صُرفت على (إيجارات، رواتب، كهرباء، صيانة، إلخ).</p>
            <div class="flex items-center justify-between text-[10px] font-black text-orange-500 uppercase">
                <span>عرض سجل المصاريف</span>
                <i class="fa-solid fa-arrow-left-long transition-transform group-hover:-translate-x-2"></i>
            </div>
        </a>

        <a href="{{ route('finance.personal') }}" class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all border-t-8 border-t-purple-600 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">المسحوبات الشخصية</p>
                    <p class="text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ number_format($personalBalance) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-tag text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mb-6 italic leading-snug">إجمالي المبالغ التي سحبتها من أرباحك الصافية لاستخدامك الشخصي.</p>
            <div class="flex items-center justify-between text-[10px] font-black text-purple-600 uppercase">
                <span>عرض السجل الخاص</span>
                <i class="fa-solid fa-arrow-left-long transition-transform group-hover:-translate-x-2"></i>
            </div>
        </a>
    </div>

    <div x-show="open" x-cloak
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl flex items-center justify-center z-[100] p-4">
        
        <div class="bg-white p-10 rounded-[3rem] w-full max-w-md shadow-2xl relative animate-in zoom-in-95 duration-200" @click.away="open = false">
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto bg-indigo-50 rounded-3xl flex items-center justify-center text-indigo-600 mb-4">
                    <i class="fa-solid" :class="icon || 'fa-exchange-alt'" style="font-size: 2rem;"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-800" x-text="title"></h2>
                <p class="text-sm text-gray-400 font-bold mt-1">سيتم خصم المبلغ من الأرباح المتاحة</p>
            </div>

            <form action="{{ route('finance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="account_type" :value="type">
                
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">المبلغ (SDG)</label>
                    <div class="relative group">
                        <input type="number" name="amount" required step="0.01"
                               class="w-full bg-gray-50 border-2 border-transparent focus:border-indigo-500 rounded-2xl p-6 text-3xl font-black text-center transition-all outline-none" 
                               placeholder="0.00">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">بيان/سبب العملية</label>
                    <textarea name="description" required rows="2"
                              class="w-full bg-gray-50 border-2 border-transparent focus:border-indigo-500 rounded-2xl p-4 font-bold transition-all outline-none" 
                              placeholder="اكتب هنا تفاصيل المصروف أو السحب..."></textarea>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black hover:bg-black transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-check-double"></i> تأكيد وحفظ العملية
                    </button>
                    <button type="button" @click="open = false" class="w-full bg-gray-100 text-gray-500 py-4 rounded-2xl font-black hover:bg-gray-200 transition-all text-sm uppercase">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    [x-cloak] { display: none !important; }
</style>
@endsection