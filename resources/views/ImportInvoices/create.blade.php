@extends('layouts.app', ['title' => 'فاتورة وارد تفصيلية'])

@section('content')
<style>
    .product-dropdown { z-index: 9999 !important; }
    .table-container { min-height: 500px; } 
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
</style>

<div class="max-w-[100rem] mx-auto p-6" x-data="{ exchangeRate: 76 }">
    @if($errors->any())
    <div class="mb-8 p-5 bg-red-50 border-r-8 border-red-500 rounded-2xl shadow-sm animate-bounce">
        <div class="flex items-center mb-3">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl ml-3"></i>
            <span class="text-red-800 font-black italic uppercase">تنبيه: راجع البيانات التالية</span>
        </div>
        <ul class="grid grid-cols-2 gap-2 text-xs text-red-700 font-bold">
            @foreach($errors->all() as $error)
                <li class="flex items-center gap-2"><i class="fa-solid fa-caret-left"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('import-invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <input type="hidden" name="total_goods_sdg" id="hidden_total_goods">
        <input type="hidden" name="total_logistic" id="hidden_total_logistic">
        <input type="hidden" name="cost_ratio_display" id="hidden_cost_ratio">

        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-wrap justify-between items-center gap-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16"></div>
            
            <div class="flex items-center gap-5 relative">
                <div class="bg-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100 rotate-3 group hover:rotate-0 transition-transform">
                    <i class="fa-solid fa-file-import text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic uppercase">فاتورة وارد جديدة</h1>
                    <p class="text-sm text-slate-400 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-indigo-400"></i> معالج التكاليف والكميات الذكي
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 relative">
                <div class="bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100 flex flex-col">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-truck-field ml-1"></i> المورد الرئيسي
                    </span>
                    <select name="supplier_id" required class="bg-transparent font-black text-indigo-600 outline-none text-sm cursor-pointer min-w-[180px]">
                        <option value="">-- اختر مورد --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100 flex flex-col">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-arrow-right-arrow-left ml-1"></i> صرف (EGP/SDG)
                    </span>
                    <div class="flex items-center gap-2">
                        <input type="number" name="exchange_rate" id="exchange_rate" value="76" oninput="calculateAll()" 
                               class="bg-transparent font-black text-indigo-600 outline-none w-16 text-xl font-mono">
                    </div>
                </div>

                <div class="bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100 flex flex-col">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">
                        <i class="fa-solid fa-circle-check ml-1"></i> نوع الفاتورة
                    </span>
                    <select name="status" class="bg-transparent font-black text-slate-700 outline-none text-sm cursor-pointer">
                        <option value="completed">مكتملة (مخزني)</option>
                        <option value="pending">مسودة (عرض سعر)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <div class="xl:col-span-3 bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-visible table-container flex flex-col">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30 rounded-t-[3rem]">
                    <h3 class="font-black text-slate-700 uppercase text-xs tracking-[0.2em] flex items-center gap-2">
                        <i class="fa-solid fa-list-ol text-slate-400"></i> تفاصيل أصناف الشحنة
                    </h3>
                    <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-3 py-1 rounded-lg">الأسعار تشمل التحميل</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50">
                                <th class="p-6 w-20">الصورة</th>
                                <th class="p-6">الصنف</th>
                                <th class="p-6 text-center">EGP</th>
                                <th class="p-6 text-center">SDG</th>
                                <th class="p-6 text-center w-24">الكمية</th>
                                <th class="p-6 text-center">الإجمالي</th>
                                <th class="p-6 text-center text-indigo-600 bg-indigo-50/30">التكلفة النهائية</th>
                                <th class="p-6"></th>
                            </tr>
                        </thead>
                        <tbody id="items_body" class="divide-y divide-slate-50">
                            <tr class="item-row group hover:bg-slate-50/50 transition-all">
                                <td class="p-4 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 overflow-hidden group-hover:border-indigo-300 transition-colors">
                                        <img src="" class="product-img w-full h-full object-cover hidden">
                                        <div class="img-placeholder w-full h-full flex items-center justify-center text-[8px] text-slate-400 italic">
                                            <i class="fa-solid fa-image-slash text-lg"></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 relative">
                                    <div class="product-search-container relative">
                                        <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                        <input type="text" placeholder="ابحث باسم الصنف..." onkeyup="filterProducts(this)" onfocus="showDropdown(this)"
                                               class="product-search-input w-full pr-9 pl-3 py-3 border-2 border-slate-100 rounded-xl text-sm font-bold focus:border-indigo-500 outline-none transition-all">
                                        
                                        <div class="product-dropdown hidden absolute right-0 left-0 mt-2 bg-white shadow-2xl rounded-2xl max-h-64 overflow-y-auto border border-slate-200 z-[999]">
                                            @foreach($products as $product)
                                            <div class="p-4 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b border-slate-50 transition-colors"
                                                 onclick="selectProduct(this, {
                                                     id: {{ $product->id }}, 
                                                     name: '{{ $product->name }}', 
                                                     code: '{{ $product->code }}', 
                                                     img: '{{ $product->image ? asset('storage/'.$product->image) : '' }}'
                                                 })">
                                                <div class="flex items-center gap-4">
                                                    <i class="fa-solid fa-box text-slate-300"></i>
                                                    <div>
                                                        <div class="font-black text-slate-700 text-xs tracking-tight">{{ $product->name }}</div>
                                                        <div class="text-[9px] text-slate-400 font-mono italic">{{ $product->code }}</div>
                                                    </div>
                                                </div>
                                                <span class="text-[9px] bg-slate-100 px-2 py-1 rounded-md font-black text-slate-500">مخزن: {{ $product->quantity }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="items[0][product_id]" class="product-id-hidden">
                                        <input type="hidden" name="items[0][name]" class="item-name-hidden">
                                        <input type="hidden" name="items[0][code]" class="item-code-hidden">
                                    </div>
                                </td>
                                <td class="p-4">
                                    <input type="number" name="items[0][price_egp]" placeholder="0.00" oninput="calculateAll()" 
                                           class="price_egp w-full text-center p-3 bg-slate-50 rounded-xl font-black font-mono outline-none border-2 border-transparent focus:border-indigo-400 focus:bg-white transition-all">
                                </td>
                                <td class="p-4">
                                    <input type="text" class="price_sdg_row w-full text-center p-3 bg-transparent font-black text-slate-400 font-mono" readonly value="0">
                                </td>
                                <td class="p-4">
                                    <input type="number" name="items[0][qty]" placeholder="1" oninput="calculateAll()" 
                                           class="qty w-full text-center p-3 bg-slate-50 rounded-xl font-black font-mono outline-none border-2 border-transparent focus:border-indigo-400 focus:bg-white transition-all">
                                </td>
                                <td class="p-4">
                                    <input type="text" class="row_total_sdg w-full text-center p-3 bg-transparent font-black text-slate-800 font-mono text-lg" readonly value="0">
                                </td>
                                <td class="p-4 bg-indigo-50/20">
                                    <input type="text" name="items[0][unit_cost]" readonly 
                                           class="unit_cost w-full text-center p-3 bg-indigo-600 text-white rounded-xl font-black font-mono shadow-md shadow-indigo-100" value="0">
                                </td>
                                <td class="p-4 text-center">
                                    <button type="button" onclick="removeRow(this)" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-8 bg-slate-50/50 rounded-b-[3rem] mt-auto">
                    <button type="button" onclick="addRow()" class="group flex items-center gap-3 text-indigo-600 font-black text-xs uppercase tracking-[0.2em] hover:text-indigo-800 transition-all">
                        <span class="bg-indigo-600 text-white w-10 h-10 flex items-center justify-center rounded-xl shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        إضافة صنف جديد للفاتورة
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm p-8 border border-slate-100 sticky top-6">
                    <h3 class="font-black text-slate-800 mb-6 flex items-center gap-3 italic">
                        <i class="fa-solid fa-truck-ramp-box text-red-500"></i> التكاليف الإضافية
                    </h3>
                    
                    <div id="logistic_body" class="space-y-4">
                        <div class="logistic-row flex items-center gap-3 bg-slate-50 p-3 rounded-2xl border-2 border-transparent hover:border-slate-200 transition-all">
                            <div class="flex-1">
                                <input type="text" value="ترحيل بضاعة" class="bg-transparent font-black text-slate-500 text-[10px] uppercase outline-none w-full">
                            </div>
                            <div class="relative w-32">
                                <i class="fa-solid fa-sdg absolute right-3 top-1/2 -translate-y-1/2 text-[8px] text-slate-300"></i>
                                <input type="number" placeholder="0" oninput="calculateAll()" 
                                       class="log_price bg-white w-full pr-8 py-2 text-center font-black text-red-500 rounded-xl outline-none shadow-sm font-mono border border-slate-100">
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="addLogisticRow()" class="mt-6 w-full py-4 border-2 border-dashed border-slate-100 rounded-[1.5rem] text-[10px] font-black text-slate-400 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/30 transition-all uppercase tracking-widest">
                        <i class="fa-solid fa-plus-circle ml-1"></i> إضافة بند تكلفة
                    </button>
                    
                    <div class="mt-10 pt-8 border-t border-slate-100 space-y-5">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">إجمالي البضاعة</span>
                            <span id="grand_total_display" class="font-black text-slate-800 font-mono italic">0 SDG</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-red-400">إجمالي اللوجيستي</span>
                            <span id="logistics_total_display" class="font-black text-red-500 font-mono italic">0 SDG</span>
                        </div>
                        
                        <div class="bg-slate-900 p-6 rounded-[2rem] text-center shadow-2xl shadow-indigo-100 relative overflow-hidden group">
                            <div class="absolute top-0 left-0 w-24 h-24 bg-white/5 -ml-12 -mt-12 rounded-full group-hover:scale-150 transition-transform"></div>
                            <span class="block text-[9px] text-slate-400 font-black uppercase mb-2 tracking-[0.3em]">نسبة التحميل (Loading Ratio)</span>
                            <div class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-up-right-from-square text-emerald-400 text-xs"></i>
                                <span id="cost_percentage_display" class="text-3xl font-black text-white font-mono italic">+0.00%</span>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="w-full bg-indigo-600 text-white py-5 rounded-[2rem] font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] transition-all flex justify-center items-center gap-3 uppercase tracking-tighter text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i> حفظ وتحديث المخزن
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function showDropdown(input) {
        document.querySelectorAll('.product-dropdown').forEach(d => d.classList.add('hidden'));
        input.parentElement.querySelector('.product-dropdown').classList.remove('hidden');
    }

    function filterProducts(input) {
        const filter = input.value.toLowerCase();
        const dropdown = input.parentElement.querySelector('.product-dropdown');
        const items = dropdown.querySelectorAll('div[onclick]');
        dropdown.classList.remove('hidden');
        items.forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(filter) ? "flex" : "none";
        });
    }

    function selectProduct(element, data) {
        const row = element.closest('.item-row');
        row.querySelector('.product-search-input').value = data.name;
        row.querySelector('.product-id-hidden').value = data.id;
        row.querySelector('.item-name-hidden').value = data.name;
        row.querySelector('.item-code-hidden').value = data.code;
        
        const imgTag = row.querySelector('.product-img');
        const placeholder = row.querySelector('.img-placeholder');
        if(data.img) {
            imgTag.src = data.img;
            imgTag.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            imgTag.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
        element.closest('.product-dropdown').classList.add('hidden');
        calculateAll();
    }

    function calculateAll() {
        const exRate = parseFloat(document.getElementById('exchange_rate').value) || 0;
        let logisticTotal = 0;
        document.querySelectorAll('.log_price').forEach(i => logisticTotal += (parseFloat(i.value) || 0));

        let totalGoodsSdg = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const egp = parseFloat(row.querySelector('.price_egp').value) || 0;
            const qty = parseFloat(row.querySelector('.qty').value) || 0; 
            
            const sdgPrice = egp * exRate; 
            const rowTotal = sdgPrice * qty; 
            
            row.querySelector('.price_sdg_row').value = egp > 0 ? sdgPrice.toLocaleString() : "0";
            row.querySelector('.row_total_sdg').value = rowTotal > 0 ? rowTotal.toLocaleString() : "0";
            
            totalGoodsSdg += rowTotal;
        });

        const costRatio = totalGoodsSdg > 0 ? (totalGoodsSdg + logisticTotal) / totalGoodsSdg : 1;
        const displayPercent = ((costRatio - 1) * 100).toFixed(2);

        document.getElementById('grand_total_display').innerText = totalGoodsSdg.toLocaleString() + " SDG";
        document.getElementById('logistics_total_display').innerText = logisticTotal.toLocaleString() + " SDG";
        document.getElementById('cost_percentage_display').innerText = "+" + displayPercent + "%";

        document.querySelectorAll('.item-row').forEach(row => {
            const egp = parseFloat(row.querySelector('.price_egp').value) || 0;
            if(egp > 0) {
                const finalUnitCost = Math.round(egp * exRate * costRatio);
                row.querySelector('.unit_cost').value = finalUnitCost.toLocaleString();
            } else {
                row.querySelector('.unit_cost').value = "0";
            }
        });

        document.getElementById('hidden_total_goods').value = totalGoodsSdg;
        document.getElementById('hidden_total_logistic').value = logisticTotal;
        document.getElementById('hidden_cost_ratio').value = displayPercent;
    }

    let rowIndex = 1;
    function addRow() {
        const tbody = document.getElementById('items_body');
        const firstRow = tbody.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input').forEach(i => {
            if(i.name) i.name = i.name.replace(/\[\d+\]/, `[${rowIndex}]`);
            i.value = ""; 
        });
        newRow.querySelector('.product-img').classList.add('hidden');
        newRow.querySelector('.img-placeholder').classList.remove('hidden');
        tbody.appendChild(newRow);
        rowIndex++;
    }

    function removeRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove();
            calculateAll();
        }
    }

    function addLogisticRow() {
        const container = document.getElementById('logistic_body');
        const rows = container.querySelectorAll('.logistic-row');
        const newRow = rows[0].cloneNode(true);
        newRow.querySelector('input[type="text"]').value = "تكلفة إضافية";
        newRow.querySelector('.log_price').value = "";
        container.appendChild(newRow);
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.product-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    window.onload = calculateAll;
</script>
@endsection