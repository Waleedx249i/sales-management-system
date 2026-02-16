@extends('layouts.app')

@section('content')
<style>
    /* حل نهائي لمشكلة اختفاء القائمة المنسدلة */
    .product-dropdown { 
        z-index: 9999 !important; 
        max-height: 350px; 
        overflow-y: auto; 
        position: absolute;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }
    
    .product-item:hover { background-color: #f8fafc; cursor: pointer; }
    
    /* منع ظهور أسهم زيادة ونقصان الأرقام لشكل أنظف */
    input::-webkit-outer-spin-button, 
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    
    /* تنسيق السكرول بار للقائمة */
    .product-dropdown::-webkit-scrollbar { width: 5px; }
    .product-dropdown::-webkit-scrollbar-track { background: #f1f1f1; }
    .product-dropdown::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }

    /* أنيميشن بسيط للظهور */
    .fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="max-w-[100rem] mx-auto p-4 lg:p-8 space-y-8 text-right" dir="rtl">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-4xl font-black text-slate-800 tracking-tight italic">{{ $consignment->pos_name }}</h1>
            <div class="flex items-center gap-3 mt-2 font-bold text-slate-400">
                <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg font-mono text-xs">{{ $consignment->consignment_number }}</span>
                <span>•</span>
                <span class="text-xs italic">تاريخ البدء: {{ $consignment->created_at->format('Y-m-d') }}</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-3xl text-center min-w-[160px]">
                <span class="block text-[10px] font-black text-emerald-500 uppercase mb-1 tracking-tighter">إجمالي المبيعات</span>
                <span class="text-2xl font-black text-emerald-700 font-mono">
                    {{ number_format($consignment->items->sum(fn($i) => $i->sold_qty * $i->unit_price)) }} <small class="text-[10px]">SDG</small>
                </span>
            </div>
            <div class="px-6 py-4 bg-slate-50 border border-slate-100 rounded-3xl text-center min-w-[160px]">
                <span class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-tighter">بضاعة متبقية بالسوق</span>
                <span class="text-2xl font-black text-slate-700 font-mono">
                    {{ number_format($consignment->items->sum(fn($i) => ($i->delivered_qty - $i->sold_qty) * $i->unit_price)) }} <small class="text-[10px]">SDG</small>
                </span>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative">
        <div class="absolute top-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
        
        <div class="flex items-center gap-3 mb-8 relative z-10">
            <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-plus-circle text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-black italic tracking-tight">تعزيز العهدة (شحن إضافي)</h3>
                <p class="text-xs text-indigo-300 font-bold italic">اختر الصنف وقائمة السعر لتحديث بيانات المندوب</p>
            </div>
        </div>

        <form action="{{ route('pos.add_more_stock', $consignment->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end relative z-20">
            @csrf
            
            <div class="md:col-span-4 relative">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2 tracking-widest">ابحث عن الصنف</label>
                <input type="text" id="productSearchInput" onkeyup="filterProducts(this)" onfocus="filterProducts(this)" placeholder="اسم أو كود المنتج..." autocomplete="off"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-bold text-white outline-none focus:bg-white focus:text-slate-900 transition-all placeholder:text-white/30">
                
                <div id="productDropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-200 text-slate-800 product-dropdown min-w-[350px] fade-in">
                    @foreach($allProducts as $p)
                    @php
                        $currentImg = 'https://ui-avatars.com/api/?name=' . urlencode($p->name) . '&background=f1f5f9&color=64748b&size=128';
                        if($p->image && file_exists(public_path('storage/' . $p->image))) {
                            $currentImg = asset('storage/' . $p->image);
                        }
                    @endphp
                    <div class="product-item p-3 flex justify-between items-center border-b border-slate-50 last:border-0 transition-colors hover:bg-indigo-50"
                         data-search="{{ strtolower($p->name . ' ' . $p->code) }}"
                         onclick="selectProductForStock('{{$p->id}}', '{{$p->name}}', '{{$p->price}}', '{{$currentImg}}')">
                        <div class="flex items-center gap-3">
                            <img src="{{ $currentImg }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shadow-sm">
                            <div class="text-right">
                                <div class="font-black text-xs text-slate-800">{{ $p->name }}</div>
                                <div class="text-[9px] text-slate-400 font-mono italic">#{{ $p->code }}</div>
                            </div>
                        </div>
                        <span class="text-[9px] bg-slate-100 text-slate-500 px-2 py-1 rounded-md font-black">متاح: {{ $p->quantity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2 tracking-widest">قائمة السعر</label>
                <select name="price_list_id" id="priceListSelect" onchange="updatePriceFromList()" 
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-bold text-white outline-none focus:bg-white focus:text-slate-900 transition-all cursor-pointer">
                    <option value="0" class="text-slate-800 font-bold">السعر الافتراضي</option>
                    @foreach($priceLists as $list)
                        <option value="{{ $list->id }}" class="text-slate-800 font-bold">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2 tracking-widest">سعر الوحدة</label>
                <input type="number" name="price" id="selectedProductPrice" required onfocus="this.select()"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-black text-white outline-none focus:bg-white focus:text-slate-900 transition-all text-center font-mono">
            </div>

            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2 tracking-widest">الكمية</label>
                <input type="number" name="qty" value="1" min="1" onfocus="this.select()"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-black text-white outline-none focus:bg-white focus:text-slate-900 transition-all text-center font-mono">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-indigo-500 hover:bg-white hover:text-indigo-900 text-white p-4 rounded-2xl font-black transition-all shadow-xl shadow-indigo-500/20 uppercase text-xs">
                    شحن العهدة
                </button>
            </div>

            <input type="hidden" name="product_id" id="selectedProductId">
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden relative z-10">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-800 italic">مخزون المندوب الفعلي</h3>
            <span class="bg-slate-100 px-4 py-2 rounded-xl text-[10px] font-black text-slate-500 uppercase tracking-tighter">إجمالي الأصناف: {{ $consignment->items->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-widest border-b border-slate-100">
                        <th class="p-6">الصنف</th>
                        <th class="p-6 text-center">سعر الوحدة</th>
                        <th class="p-6 text-center">المشحن</th>
                        <th class="p-6 text-center text-emerald-600 bg-emerald-50/30">المباع</th>
                        <th class="p-6 text-center">المتبقي</th>
                        <th class="p-6 text-left">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($consignment->items as $item)
                    <tr class="hover:bg-slate-50/30 transition-all group">
                        <td class="p-6">
                            <div class="flex items-center gap-4">
                                @php
                                    $itemImg = 'https://ui-avatars.com/api/?name=' . urlencode($item->product_name) . '&background=f1f5f9&color=64748b&size=128';
                                    if(isset($item->product->image) && $item->product->image) {
                                         $itemImg = asset('storage/' . $item->product->image);
                                    }
                                @endphp
                                <img src="{{ $itemImg }}" class="w-12 h-12 rounded-xl object-cover border border-slate-100 shadow-sm group-hover:scale-110 transition-transform">
                                <div>
                                    <div class="font-black text-slate-700 group-hover:text-indigo-600 transition-colors text-sm">{{ $item->product_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold italic tracking-tighter">سعر البيع المعتمد</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-center font-black text-slate-600 font-mono">{{ number_format($item->unit_price) }}</td>
                        <td class="p-6 text-center font-bold text-slate-400 font-mono">{{ $item->delivered_qty }}</td>
                        <td class="p-6 text-center font-black text-emerald-600 bg-emerald-50/20 font-mono">{{ $item->sold_qty }}</td>
                        <td class="p-6 text-center">
                            @php $rem = $item->delivered_qty - $item->sold_qty; @endphp
                            <span class="px-4 py-2 rounded-2xl text-[11px] font-black font-mono {{ $rem <= 5 ? 'bg-red-50 text-red-600 border border-red-100 animate-pulse' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rem }}
                            </span>
                        </td>
                        <td class="p-6 text-left">
                            <button onclick="openSaleModal({{ $item->id }}, '{{ $item->product_name }}', {{ $rem }})" 
                                class="bg-white border-2 border-slate-100 text-slate-700 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 px-6 py-2.5 rounded-2xl text-[11px] font-black transition-all active:scale-95 shadow-sm">
                                <i class="fa-solid fa-cart-shopping ml-1"></i> تسجيل بيع
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="saleModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] p-10 max-w-md w-full shadow-2xl relative border border-white/20 fade-in">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 italic">تسجيل مبيعات</h2>
            <p id="modalProductName" class="text-emerald-600 font-black mt-2 text-xs uppercase tracking-widest bg-emerald-50 py-1 px-4 rounded-full inline-block"></p>
        </div>
        <form id="saleForm" method="POST" class="space-y-6 text-right">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2 tracking-widest">الكمية المباعة الآن</label>
                <input type="number" name="quantity_sold" id="maxQtyInput" required min="1" onfocus="this.select()"
                    class="w-full p-6 bg-slate-50 border border-slate-100 rounded-[2rem] text-center text-5xl font-black outline-none focus:ring-8 focus:ring-emerald-500/5 transition-all font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2 tracking-widest">تاريخ العملية</label>
                <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" required class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-black outline-none">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white py-5 rounded-[2rem] font-black shadow-xl hover:bg-emerald-700 active:scale-95 transition-all text-sm uppercase tracking-tighter">
                تأكيد المبيعات
            </button>
            <button type="button" onclick="closeModal()" class="w-full text-slate-300 hover:text-red-400 font-black text-[10px] uppercase pt-2 transition-colors">إلغاء وإغلاق</button>
        </form>
    </div>
</div>

<script>
    // بيانات قوائم الأسعار الممرة من الكنترولر
    const priceListsData = @json($priceLists);

    /**
     * فلترة الأصناف وإظهار القائمة
     */
    function filterProducts(input) {
        const filter = input.value.toLowerCase().trim();
        const dropdown = document.getElementById('productDropdown');
        const items = dropdown.querySelectorAll('.product-item');
        
        // إظهار القائمة
        dropdown.classList.remove('hidden');
        
        let hasResults = false;
        items.forEach(item => {
            const text = item.getAttribute('data-search');
            if (text.includes(filter)) {
                item.style.display = 'flex';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });

        // إذا لم توجد نتائج والفلتر ليس فارغاً، نخفي القائمة
        if (!hasResults && filter !== '') {
            dropdown.classList.add('hidden');
        }
    }

    /**
     * اختيار صنف من القائمة
     */
    function selectProductForStock(id, name, defaultPrice, img) {
        document.getElementById('productSearchInput').value = name;
        document.getElementById('selectedProductId').value = id;
        document.getElementById('selectedProductPrice').value = defaultPrice;
        document.getElementById('selectedProductPrice').setAttribute('data-default', defaultPrice);
        
        // إخفاء القائمة وإعادة ضبط قائمة الأسعار للافتراضي
        document.getElementById('productDropdown').classList.add('hidden');
        document.getElementById('priceListSelect').value = "0";
        
        // التركيز على حقل الكمية فوراً
        document.getElementsByName('qty')[0].focus();
    }

    /**
     * تحديث السعر بناءً على قائمة الأسعار المختارة
     */
    function updatePriceFromList() {
        const productId = document.getElementById('selectedProductId').value;
        const listId = document.getElementById('priceListSelect').value;
        const priceInput = document.getElementById('selectedProductPrice');

        if (!productId) return;

        if (listId == "0") {
            priceInput.value = priceInput.getAttribute('data-default');
        } else {
            const list = priceListsData.find(l => l.id == listId);
            const customItem = list.items.find(i => i.product_id == productId);
            if (customItem) {
                priceInput.value = customItem.price;
            } else {
                priceInput.value = priceInput.getAttribute('data-default');
                console.warn('الصنف غير مسعر في هذه القائمة، تم استخدام السعر الافتراضي');
            }
        }
    }

    /**
     * إغلاق القائمة عند الضغط في أي مكان خارجها
     */
    document.addEventListener('click', e => {
        const input = document.getElementById('productSearchInput');
        const dropdown = document.getElementById('productDropdown');
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    /**
     * التحكم في مودال المبيعات
     */
    function openSaleModal(itemId, productName, maxQty) {
        if(maxQty <= 0) {
            alert('عذراً، هذا الصنف نفد من عهدة المندوب');
            return;
        }
        document.getElementById('saleModal').classList.remove('hidden');
        document.getElementById('modalProductName').innerText = productName + " | متبقي: " + maxQty;
        document.getElementById('saleForm').action = "/pos-consignments/record-sale/" + itemId;
        document.getElementById('maxQtyInput').max = maxQty;
        document.getElementById('maxQtyInput').value = 1;
        document.getElementById('maxQtyInput').focus();
    }

    function closeModal() { 
        document.getElementById('saleModal').classList.add('hidden'); 
    }
</script>
@endsection