@extends('layouts.app')

@section('content')
<style>
    .product-dropdown { z-index: 100; max-height: 250px; overflow-y: auto; }
    .product-item:hover { background-color: #f8fafc; cursor: pointer; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<div class="max-w-[100rem] mx-auto p-4 lg:p-8 space-y-8 text-right" dir="rtl">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="space-y-1">
            <h1 class="text-4xl font-black text-slate-800 tracking-tight">{{ $consignment->pos_name }}</h1>
            <div class="flex items-center gap-3 mt-2 font-bold text-slate-400">
                <span class="bg-slate-100 px-3 py-1 rounded-lg font-mono text-xs text-slate-600">{{ $consignment->consignment_number }}</span>
                <span>•</span>
                <span class="text-xs">تاريخ البدء: {{ $consignment->created_at->format('Y-m-d') }}</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            <div class="px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-3xl text-center min-w-[150px]">
                <span class="block text-[10px] font-black text-emerald-500 uppercase mb-1 tracking-tighter">إجمالي المبيعات (كاش)</span>
                <span class="text-2xl font-black text-emerald-700">
                    {{ number_format($consignment->items->sum(fn($i) => $i->sold_qty * $i->unit_price)) }} <small class="text-xs font-bold">SDG</small>
                </span>
            </div>
            <div class="px-6 py-4 bg-indigo-50 border border-indigo-100 rounded-3xl text-center min-w-[150px]">
                <span class="block text-[10px] font-black text-indigo-500 uppercase mb-1 tracking-tighter">بضاعة متبقية بالسوق</span>
                <span class="text-2xl font-black text-indigo-700">
                    {{ number_format($consignment->items->sum(fn($i) => ($i->delivered_qty - $i->sold_qty) * $i->unit_price)) }} <small class="text-xs font-bold">SDG</small>
                </span>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-black italic">تعزيز العهدة (شحن إضافي)</h3>
                <p class="text-xs text-indigo-300 font-bold">اختر الصنف وقائمة السعر لتحديث بيانات المندوب</p>
            </div>
        </div>

        <form action="{{ route('pos.add_more_stock', $consignment->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
            @csrf
            
            <div class="md:col-span-4 relative">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2">ابحث عن الصنف (اسم/كود)</label>
                <input type="text" id="productSearchInput" onkeyup="filterProducts(this)" placeholder="اكتب اسم المنتج..." autocomplete="off"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-bold text-white outline-none focus:bg-white focus:text-slate-900 transition-all placeholder:text-white/30">
                
                <div id="productDropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 text-slate-800 product-dropdown">
                    @foreach($allProducts as $p)
                    <div class="product-item p-4 flex justify-between items-center border-b border-slate-50 last:border-0"
                         data-search="{{ strtolower($p->name . ' ' . $p->code) }}"
                         onclick="selectProductForStock('{{$p->id}}', '{{$p->name}}', '{{$p->price}}')">
                        <div class="text-right">
                            <div class="font-black text-sm">{{ $p->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $p->code }}</div>
                        </div>
                        <span class="text-[9px] bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg font-black uppercase">متاح: {{ $p->quantity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2">قائمة السعر</label>
                <select name="price_list_id" id="priceListSelect" onchange="updatePriceFromList()" 
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-bold text-white outline-none focus:bg-white focus:text-slate-900 transition-all cursor-pointer">
                    <option value="0" class="text-slate-800">السعر الافتراضي</option>
                    @foreach($priceLists as $list)
                        <option value="{{ $list->id }}" class="text-slate-800">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2">سعر الوحدة</label>
                <input type="number" name="price" id="selectedProductPrice" required onfocus="this.select()"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-black text-white outline-none focus:bg-white focus:text-slate-900 transition-all text-center">
            </div>

            <div class="md:col-span-1">
                <label class="block text-[10px] font-black text-indigo-300 uppercase mb-2 mr-2">الكمية</label>
                <input type="number" name="qty" value="1" min="1" onfocus="this.select()"
                    class="w-full p-4 bg-white/10 border border-white/20 rounded-2xl font-black text-white outline-none focus:bg-white focus:text-slate-900 transition-all text-center">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-indigo-500 hover:bg-white hover:text-indigo-900 text-white p-4 rounded-2xl font-black transition-all shadow-xl shadow-indigo-500/20">
                    شحن العهدة
                </button>
            </div>

            <input type="hidden" name="product_id" id="selectedProductId">
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-800">مخزون المندوب الفعلي</h3>
            <span class="bg-slate-100 px-4 py-2 rounded-xl text-xs font-black text-slate-500 uppercase">إجمالي الأصناف: {{ $consignment->items->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-[0.1em] border-b border-slate-100">
                        <th class="p-6">بيانات الصنف</th>
                        <th class="p-6 text-center">سعر الوحدة</th>
                        <th class="p-6 text-center">إجمالي المشحن</th>
                        <th class="p-6 text-center text-emerald-600">المباع</th>
                        <th class="p-6 text-center">المتبقي بالميدان</th>
                        <th class="p-6 text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($consignment->items as $item)
                    <tr class="hover:bg-slate-50/30 transition-all group">
                        <td class="p-6">
                            <div class="font-black text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $item->product_name }}</div>
                        </td>
                        <td class="p-6 text-center font-black text-slate-600 font-mono">{{ number_format($item->unit_price) }}</td>
                        <td class="p-6 text-center font-bold text-slate-400">{{ $item->delivered_qty }}</td>
                        <td class="p-6 text-center font-black text-emerald-600 bg-emerald-50/20">{{ $item->sold_qty }}</td>
                        <td class="p-6 text-center">
                            @php $rem = $item->delivered_qty - $item->sold_qty; @endphp
                            <span class="px-4 py-2 rounded-2xl text-xs font-black {{ $rem <= 5 ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-100 text-slate-500' }}">
                                {{ $rem }}
                            </span>
                        </td>
                        <td class="p-6 text-left">
                            <button onclick="openSaleModal({{ $item->id }}, '{{ $item->product_name }}', {{ $rem }})" 
                                class="bg-white border border-slate-200 text-slate-700 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 px-6 py-2 rounded-xl text-[11px] font-black transition-all">
                                تسجيل بيع
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="saleModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] p-10 max-w-md w-full shadow-2xl relative">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800">تسجيل مبيعات</h2>
            <p id="modalProductName" class="text-emerald-600 font-bold mt-2 text-sm"></p>
        </div>
        <form id="saleForm" method="POST" class="space-y-6 text-right">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">الكمية المباعة</label>
                <input type="number" name="quantity_sold" id="maxQtyInput" required min="1" onfocus="this.select()"
                    class="w-full p-6 bg-slate-50 border border-slate-100 rounded-[2rem] text-center text-5xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 mr-2">تاريخ اليوم</label>
                <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" required class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl font-black outline-none">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white py-5 rounded-[2rem] font-black shadow-xl hover:bg-emerald-700 transition-all">تأكيد العملية</button>
            <button type="button" onclick="closeModal()" class="w-full text-slate-400 font-bold text-xs uppercase pt-2">إغلاق</button>
        </form>
    </div>
</div>

<script>
    const priceListsData = @json($priceLists);

    function filterProducts(input) {
        const filter = input.value.toLowerCase().trim();
        const dropdown = document.getElementById('productDropdown');
        const items = dropdown.querySelectorAll('.product-item');
        if (filter.length > 0) {
            dropdown.classList.remove('hidden');
            items.forEach(item => {
                item.style.display = item.getAttribute('data-search').includes(filter) ? "flex" : "none";
            });
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function selectProductForStock(id, name, defaultPrice) {
        document.getElementById('productSearchInput').value = name;
        document.getElementById('selectedProductId').value = id;
        document.getElementById('selectedProductPrice').value = defaultPrice;
        document.getElementById('selectedProductPrice').setAttribute('data-default', defaultPrice);
        document.getElementById('productDropdown').classList.add('hidden');
        
        // إعادة تعيين قائمة الأسعار للافتراضي عند اختيار منتج جديد
        document.getElementById('priceListSelect').value = "0";
        document.getElementsByName('qty')[0].focus();
    }

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
                alert('هذا الصنف غير مسعر في القائمة المختارة، تم استخدام السعر الافتراضي');
            }
        }
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('#productSearchInput')) document.getElementById('productDropdown').classList.add('hidden');
    });

    function openSaleModal(itemId, productName, maxQty) {
        if(maxQty <= 0) return alert('الكمية نافدة');
        document.getElementById('saleModal').classList.remove('hidden');
        document.getElementById('modalProductName').innerText = productName + " | متبقي: " + maxQty;
        document.getElementById('saleForm').action = "/pos-consignments/record-sale/" + itemId;
        document.getElementById('maxQtyInput').max = maxQty;
        document.getElementById('maxQtyInput').value = 1;
        document.getElementById('maxQtyInput').focus();
    }

    function closeModal() { document.getElementById('saleModal').classList.add('hidden'); }
</script>
@endsection