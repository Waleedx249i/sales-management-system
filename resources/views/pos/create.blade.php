@extends('layouts.app', ['title' => 'شحن عهدة جديدة'])

@section('content')
<style>
    .product-dropdown { z-index: 9999 !important; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinners { -moz-appearance: textfield; }
    /* تحسين شكل السكرول بار في القائمة */
    .product-dropdown::-webkit-scrollbar { width: 6px; }
    .product-dropdown::-webkit-scrollbar-track { background: #f1f1f1; }
    .product-dropdown::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }
</style>

<div class="max-w-[100rem] mx-auto p-4">
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 rounded-xl">
        <span class="text-red-800 font-black">خطأ في البيانات:</span>
        <ul class="list-disc list-inside text-sm text-red-700 font-bold">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pos.store') }}" method="POST" id="consignmentForm">
        @csrf

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-600 p-3 rounded-xl text-white shadow-lg shadow-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-tighter italic">شحن عهدة جديدة</h1>
                    <p class="text-xs text-slate-400 font-bold italic">تجهيز بضاعة لنقطة بيع أو مندوب</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                <div class="bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 text-right">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-widest">تطبيق قائمة أسعار</span>
                    <select id="global_price_list" onchange="applyGlobalList(this.value)" class="bg-transparent font-black text-indigo-600 outline-none text-sm cursor-pointer">
                        <option value="0">السعر الافتراضي</option>
                        @foreach($priceLists as $list)
                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 text-right">
                    <span class="block text-[10px] text-indigo-400 font-bold uppercase tracking-widest">نقطة البيع / المندوب</span>
                    <input type="text" name="pos_name" required placeholder="اسم المندوب..." onfocus="this.select()" class="bg-transparent font-black text-indigo-700 outline-none text-sm w-48 placeholder:text-indigo-200">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <div class="xl:col-span-3 bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-visible min-h-[450px]">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-[11px] font-black uppercase tracking-wider border-b border-slate-100">
                            <th class="p-4 w-16 text-center">الصورة</th>
                            <th class="p-4">الصنف / الكود</th>
                            <th class="p-4 text-center">قائمة السعر</th>
                            <th class="p-4 text-center w-32">سعر الوحدة</th>
                            <th class="p-4 text-center w-28">الكمية</th>
                            <th class="p-4 text-center">الإجمالي</th>
                            <th class="p-4 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="consignment_body"></tbody>
                </table>
                
                <div class="p-4 bg-slate-50/50 border-t border-slate-100 rounded-b-[2.5rem]">
                    <button type="button" onclick="addRow()" class="flex items-center gap-2 text-indigo-600 font-black text-xs uppercase hover:gap-3 transition-all">
                        <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow-md">+</span>
                        إضافة صنف للعهدة
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-[2rem] shadow-sm p-6 border border-slate-100 sticky top-4">
                    <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2 italic text-sm">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span> ملخص العهدة
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <label class="block text-[10px] text-slate-400 font-black uppercase mb-1 tracking-widest italic">ملاحظات الشحنة</label>
                            <textarea name="notes" rows="2" placeholder="أي تفاصيل إضافية..." class="w-full bg-transparent text-sm font-bold text-slate-700 outline-none resize-none"></textarea>
                        </div>

                        <div class="bg-slate-900 p-6 rounded-[1.5rem] text-center shadow-xl relative overflow-hidden group">
                            <div class="absolute inset-0 bg-indigo-500/5 group-hover:bg-indigo-500/10 transition-colors"></div>
                            <span class="block text-[10px] text-indigo-300 font-black mb-1 tracking-[0.3em] uppercase relative">إجمالي القيمة</span>
                            <span id="grand_total_display" class="text-3xl font-black text-white font-mono italic relative">0</span>
                            <input type="hidden" name="total_amount" id="hidden_total">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-[1.2rem] font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 uppercase text-xs tracking-tighter">
                            <i class="fa-solid fa-truck-fast"></i>
                            اعتماد وشحن العهدة
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('pos.index') }}'" class="w-full bg-slate-300 text-slate-700 py-4 rounded-[1.2rem] font-black shadow-sm hover:bg-slate-400 transition-all flex items-center justify-center gap-2 uppercase text-xs tracking-tighter">
                            <i class="fa-solid fa-xmark"></i>
                            إلغاء العهدة والعودة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="row_template">
    <tr class="item-row border-b border-slate-50 transition-all hover:bg-indigo-50/10 group">
        <td class="p-3 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shadow-sm group-hover:border-indigo-300 transition-colors">
                <img src="" class="product-img w-full h-full object-cover hidden">
                <div class="img-placeholder w-full h-full flex items-center justify-center text-[8px] text-slate-300">
                   <i class="fa-solid fa-box-open text-xs"></i>
                </div>
            </div>
        </td>
        <td class="p-3 relative">
            <div class="product-search-container">
                <input type="text" placeholder="بحث عن صنف..." onkeyup="filterProducts(this)" onfocus="showDropdown(this); this.select();" class="product-search-input w-full p-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-indigo-500 transition-all">
                <div class="product-dropdown hidden absolute left-0 right-0 mt-2 bg-white shadow-2xl rounded-2xl max-h-60 overflow-y-auto border border-slate-200 z-[9999] min-w-[320px]">
                    @foreach($products as $product)
                    @php
                        $currentImg = 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=f1f5f9&color=64748b&size=128';
                        if($product->image) {
                            if(file_exists(public_path($product->image))) {
                                $currentImg = asset($product->image);
                            } elseif(file_exists(public_path('storage/' . $product->image))) {
                                $currentImg = asset('storage/' . $product->image);
                            }
                        }
                    @endphp
                    <div class="p-2.5 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b border-slate-50 transition-colors" 
                         onclick='selectProduct(this, {
                            id: "{{$product->id}}", 
                            name: "{{$product->name}}", 
                            code: "{{$product->code}}",
                            price: "{{$product->price}}", 
                            qty: "{{$product->quantity}}",
                            img: "{{ $currentImg }}"
                         })'>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-100 bg-white shadow-sm">
                                <img src="{{ $currentImg }}" class="w-full h-full object-cover">
                            </div>
                            <div class="text-right">
                                <div class="font-black text-slate-700 text-xs">{{ $product->name }}</div>
                                <div class="text-[9px] text-slate-400 font-mono italic">#{{ $product->code }}</div>
                            </div>
                        </div>
                        <span class="text-[9px] bg-slate-100 px-2 py-1 rounded-md font-black text-slate-500">المخزن: {{ $product->quantity }}</span>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="items[INDEX][product_id]" class="product-id-hidden">
            </div>
        </td>
        <td class="p-3">
            <select name="items[INDEX][price_list_id]" onchange="updateRowPrice(this)" class="price-list-select w-full text-[10px] font-black bg-slate-50 rounded-xl p-2.5 outline-none border border-transparent focus:border-slate-200 transition-all">
                <option value="0">الافتراضي</option>
                @foreach($priceLists as $list) <option value="{{ $list->id }}">{{ $list->name }}</option> @endforeach
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[INDEX][price]" placeholder="0" onfocus="this.select()" oninput="calculateTotal()" class="item-price w-full text-center p-2.5 bg-indigo-50/30 rounded-xl font-black text-indigo-700 outline-none border border-transparent focus:border-indigo-300 font-mono">
        </td>
        <td class="p-3">
            <input type="number" name="items[INDEX][qty]" value="1" onfocus="this.select()" oninput="calculateTotal()" class="item-qty w-full text-center p-2.5 bg-slate-50 rounded-xl font-black outline-none border border-transparent focus:border-slate-300 font-mono">
        </td>
        <td class="p-3 text-center">
            <span class="row-total font-black text-slate-700 font-mono">0</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); calculateTotal();" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">✕</button>
        </td>
    </tr>
</template>

<script>
    const priceListsData = @json($priceLists);
    let rowIndex = 0;

    function addRow() {
        const tbody = document.getElementById('consignment_body');
        const template = document.getElementById('row_template').innerHTML;
        tbody.insertAdjacentHTML('beforeend', template.replace(/INDEX/g, rowIndex++));
    }

    function showDropdown(input) {
        document.querySelectorAll('.product-dropdown').forEach(d => d.classList.add('hidden'));
        input.parentElement.querySelector('.product-dropdown').classList.remove('hidden');
    }

    function filterProducts(input) {
        const filter = input.value.toLowerCase();
        const dropdown = input.parentElement.querySelector('.product-dropdown');
        dropdown.querySelectorAll('div[onclick]').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(filter) ? "flex" : "none";
        });
    }

    function selectProduct(element, product) {
        const row = element.closest('.item-row');
        row.querySelector('.product-search-input').value = product.name;
        row.querySelector('.product-id-hidden').value = product.id;
        row.querySelector('.item-price').value = product.price;
        row.querySelector('.item-price').setAttribute('data-default', product.price);
        
        // تحديث صورة السطر
        const imgTag = row.querySelector('.product-img');
        const placeholder = row.querySelector('.img-placeholder');
        if(product.img) {
            imgTag.src = product.img;
            imgTag.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }

        element.closest('.product-dropdown').classList.add('hidden');
        calculateTotal();
    }

    function updateRowPrice(select) {
        const row = select.closest('.item-row');
        const productId = row.querySelector('.product-id-hidden').value;
        const priceInput = row.querySelector('.item-price');
        if (!productId) return;

        if (select.value == "0") {
            priceInput.value = priceInput.getAttribute('data-default');
        } else {
            const list = priceListsData.find(l => l.id == select.value);
            const custom = list.items.find(i => i.product_id == productId);
            if (custom) priceInput.value = custom.price;
        }
        calculateTotal();
    }

    function applyGlobalList(listId) {
        document.querySelectorAll('.price-list-select').forEach(s => { 
            s.value = listId; 
            updateRowPrice(s); 
        });
    }

    function calculateTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const p = parseFloat(row.querySelector('.item-price').value) || 0;
            const q = parseFloat(row.querySelector('.item-qty').value) || 0;
            const total = p * q;
            row.querySelector('.row-total').innerText = total.toLocaleString();
            grandTotal += total;
        });
        document.getElementById('grand_total_display').innerText = grandTotal.toLocaleString();
        document.getElementById('hidden_total').value = grandTotal;
    }

    document.addEventListener('click', e => { 
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.product-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    window.onload = addRow;
</script>
@endsection