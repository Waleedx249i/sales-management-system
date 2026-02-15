<div id="saleModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800">تسجيل مبيعات</h2>
            <p id="modalProductName" class="text-emerald-600 font-bold mt-2"></p>
        </div>
        
        <form id="saleForm" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 text-right">الكمية التي تم بيعها</label>
                <input type="number" name="quantity_sold" id="maxQtyInput" required min="1" 
                    class="w-full p-5 bg-slate-50 border border-slate-100 rounded-3xl text-center text-3xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 text-right">تاريخ البيع (للسجلات)</label>
                <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" required 
                    class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-black outline-none">
            </div>
            
            <div class="flex flex-col gap-3 pt-4">
                <button type="submit" class="w-full bg-emerald-600 text-white py-5 rounded-3xl font-black shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all active:scale-95">حفظ العملية</button>
                <button type="button" onclick="closeModal()" class="w-full py-2 text-slate-400 font-black text-xs uppercase tracking-widest">إغلاق التقرير</button>
            </div>
        </form>
    </div>
</div>