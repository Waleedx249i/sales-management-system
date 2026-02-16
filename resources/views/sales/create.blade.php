@extends('layouts.app', ['title' => 'فاتورة بيع جديدة'])

@section('content')
<style>
    .product-dropdown { z-index: 9999 !important; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinners { -moz-appearance: textfield; }
    .discount-active { @apply bg-slate-900 text-white shadow-sm; }
    /* تحسين شكل السكرول بار */
    .product-dropdown::-webkit-scrollbar { width: 6px; }
    .product-dropdown::-webkit-scrollbar-track { background: #f1f1f1; }
    .product-dropdown::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
</style>

<div class="max-w-[100rem] mx-auto p-4">
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 rounded-xl">
        <span class="text-red-800 font-black">خطأ:</span>
        <ul class="list-disc list-inside text-sm text-red-700 font-bold">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" id="salesForm">
        @csrf
        <input type="hidden" name="action" id="form_action" value="approve">

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-emerald-600 p-3 rounded-xl text-white shadow-lg shadow-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-tighter italic">فاتورة بيع جديدة</h1>
                    <p class="text-xs text-slate-400 font-bold">الخصم من المخزن يتم عند "التصديق" فقط</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 text-right">
                    <span class="block text-[10px] text-indigo-400 font-bold uppercase">حالة الدفع</span>
                    <select name="status" id="payment_status" onchange="togglePaidInput()" class="bg-transparent font-black text-indigo-600 outline-none text-sm cursor-pointer">
                        <option value="paid">نقدي (مدفوع)</option>
                        <option value="partial">دفع جزئي</option>
                        <option value="pending">آجل (دين)</option>
                    </select>
                </div>

                <div id="paid_amount_container" class="bg-amber-50 px-4 py-2 rounded-xl border border-amber-100 text-right hidden">
                    <span class="block text-[10px] text-amber-500 font-bold uppercase">المبلغ المستلم</span>
                    <input type="number" name="paid_amount" id="paid_amount_input" placeholder="0" onfocus="this.select()" class="bg-transparent font-black text-amber-700 outline-none text-sm w-24">
                </div>

                <div class="bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 text-right">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">قائمة الأسعار</span>
                    <select id="global_price_list" onchange="applyGlobalList(this.value)" class="bg-transparent font-black text-emerald-600 outline-none text-sm cursor-pointer">
                        <option value="0">السعر الافتراضي</option>
                        @foreach($priceLists as $list)
                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 text-right">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase">اسم العميل</span>
                    <input type="text" name="customer_name" placeholder="عميل نقدي..." class="bg-transparent font-black text-slate-700 outline-none text-sm w-40">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <div class="xl:col-span-3 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-visible min-h-[450px]">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-[11px] font-black uppercase tracking-wider border-b border-slate-100">
                            <th class="p-4 w-16 text-center">الصورة</th>
                            <th class="p-4">الصنف / الكود</th>
                            <th class="p-4 text-center">المصدر</th>
                            <th class="p-4 text-center w-32">السعر</th>
                            <th class="p-4 text-center w-28">الكمية</th>
                            <th class="p-4 text-center">الإجمالي</th>
                            <th class="p-4 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="sales_body"></tbody>
                </table>
                <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                    <button type="button" onclick="addRow()" class="flex items-center gap-2 text-emerald-600 font-black text-xs uppercase hover:gap-3 transition-all">
                        <span class="bg-emerald-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow-md">+</span>
                        إضافة منتج للفاتورة
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 sticky top-4">
                    <h3 class="font-black text-slate-800 mb-6 flex items-center gap-2 italic text-sm">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> ملخص الحساب النهائي
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold text-slate-500">
                            <span>الإجمالي:</span>
                            <span id="subtotal_display" class="text-slate-800 font-mono">0 SDG</span>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-[10px] text-slate-400 font-black uppercase tracking-widest">الخصم</label>
                                <div class="flex bg-white border border-slate-200 rounded-lg p-1 scale-90">
                                    <button type="button" onclick="setDiscountType('val')" id="btn_disc_val" class="px-2 py-0.5 rounded-md text-[9px] font-black transition-all bg-slate-900 text-white">SDG</button>
                                    <button type="button" onclick="setDiscountType('per')" id="btn_disc_per" class="px-2 py-0.5 rounded-md text-[9px] font-black transition-all text-slate-400 hover:bg-slate-100">%</button>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="number" name="discount_value" id="discount_input" placeholder="0" onfocus="this.select()" oninput="calculateFinalTotal()" class="w-full bg-transparent text-xl font-black text-red-500 outline-none font-mono">
                                <span id="discount_symbol" class="text-red-400 font-bold ml-2">SDG</span>
                            </div>
                            <input type="hidden" name="discount_type" id="discount_type" value="val">
                        </div>

                        <div class="bg-slate-900 p-5 rounded-2xl text-center shadow-xl relative overflow-hidden group">
                            <div class="absolute inset-0 bg-emerald-500/5 group-hover:bg-emerald-500/10 transition-colors"></div>
                            <span class="block text-[10px] text-slate-300 font-black mb-1 uppercase tracking-widest relative">الصافي المطلوب</span>
                            <span id="final_total_display" class="text-3xl font-black text-white font-mono italic relative">0</span>
                            
                            <input type="hidden" name="total_amount" id="hidden_subtotal">
                            <input type="hidden" name="discount" id="hidden_discount_amount">
                            <input type="hidden" name="final_amount" id="hidden_final">
                        </div>

                        <div class="space-y-3 pt-2">
                            <button type="button" onclick="submitInvoice('approve')" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-black shadow-lg shadow-emerald-200 hover:bg-emerald-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group">
                                <i class="fa-solid fa-check-double text-sm group-hover:scale-110 transition-transform"></i>
                                تصديق (خصم مخزني)
                            </button>

                            <button type="button" onclick="submitInvoice('draft')" class="w-full bg-white border-2 border-slate-200 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-50 hover:border-slate-300 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-invoice text-slate-400"></i>
                                حفظ كمسودة
                            </button>
                            
                            <a href="{{ route('sales.index') }}" class="block w-full text-center py-2 text-xs font-bold text-red-400 hover:text-red-600 transition-colors">
                                إلغاء العملية والعودة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="row_template">
    <tr class="item-row border-b border-slate-50 transition-all hover:bg-slate-50/30 group">
        <td class="p-3 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shadow-sm group-hover:border-emerald-300 transition-colors">
                <img src="" class="product-img w-full h-full object-cover hidden">
                <div class="img-placeholder w-full h-full flex items-center justify-center text-[8px] text-slate-300 italic">
                   <i class="fa-solid fa-image text-xs"></i>
                </div>
            </div>
        </td>
        <td class="p-3 relative">
            <div class="product-search-container">
                <input type="text" placeholder="ابحث باسم المنتج..." onkeyup="filterProducts(this)" onfocus="showDropdown(this)" class="product-search-input w-full p-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-emerald-500 transition-all">
                <div class="product-dropdown hidden absolute left-0 right-0 mt-2 bg-white shadow-2xl rounded-2xl max-h-64 overflow-y-auto border border-slate-200 z-[9999] min-w-[320px]">
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
                    <div class="p-2.5 hover:bg-emerald-50 cursor-pointer flex justify-between items-center border-b border-slate-50 transition-colors" 
                         onclick='selectProduct(this, {
                            id: "{{$product->id}}", 
                            name: "{{$product->name}}", 
                            price: "{{$product->price}}", 
                            qty: "{{$product->quantity}}",
                            img: "{{ $currentImg }}"
                         })'>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-100 bg-white">
                                <img src="{{ $currentImg }}" class="w-full h-full object-cover">
                            </div>
                            <div class="text-right">
                                <div class="font-black text-slate-700 text-xs">{{ $product->name }}</div>
                                <div class="text-[9px] text-slate-400 font-mono tracking-tighter italic">#{{ $product->code }}</div>
                            </div>
                        </div>
                        <span class="text-[9px] bg-slate-100 px-2 py-1 rounded-md font-black text-slate-500">متوفر: {{ $product->quantity }}</span>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="items[INDEX][product_id]" class="product-id-hidden">
            </div>
        </td>
        <td class="p-3">
            <select name="items[INDEX][price_list_id]" onchange="updateRowPrice(this)" class="price-list-select w-full text-[10px] font-black bg-slate-50 rounded-xl p-2.5 outline-none border border-transparent focus:border-slate-200 transition-all">
                <option value="0">السعر الافتراضي</option>
                @foreach($priceLists as $list) <option value="{{ $list->id }}">{{ $list->name }}</option> @endforeach
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[INDEX][price]" placeholder="0" onfocus="this.select()" oninput="calculateFinalTotal()" class="item-price w-full text-center p-2.5 bg-emerald-50/30 rounded-xl font-black text-emerald-700 outline-none border border-transparent focus:border-emerald-300 font-mono">
        </td>
        <td class="p-3">
            <input type="number" name="items[INDEX][qty]" placeholder="1" onfocus="this.select()" oninput="calculateFinalTotal()" class="item-qty w-full text-center p-2.5 bg-slate-50 rounded-xl font-black outline-none border border-transparent focus:border-slate-300 font-mono">
        </td>
        <td class="p-3">
            <input type="text" readonly class="row-total w-full text-center p-2.5 bg-transparent font-black text-slate-800 font-mono" value="0">
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); calculateFinalTotal();" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">✕</button>
        </td>
    </tr>
</template>

<script>
    const priceListsData = @json($priceLists);
    let rowIndex = 0;
    let discountType = 'val';

    function setDiscountType(type) {
        discountType = type;
        document.getElementById('discount_type').value = type;
        const btnVal = document.getElementById('btn_disc_val');
        const btnPer = document.getElementById('btn_disc_per');
        const symbol = document.getElementById('discount_symbol');

        if(type === 'val') {
            btnVal.className = "px-2 py-0.5 rounded-md text-[9px] font-black bg-slate-900 text-white";
            btnPer.className = "px-2 py-0.5 rounded-md text-[9px] font-black text-slate-400 hover:bg-slate-100";
            symbol.innerText = "SDG";
        } else {
            btnPer.className = "px-2 py-0.5 rounded-md text-[9px] font-black bg-slate-900 text-white";
            btnVal.className = "px-2 py-0.5 rounded-md text-[9px] font-black text-slate-400 hover:bg-slate-100";
            symbol.innerText = "%";
        }
        calculateFinalTotal();
    }

    function calculateFinalTotal() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const p = parseFloat(row.querySelector('.item-price').value) || 0;
            const q = parseFloat(row.querySelector('.item-qty').value) || 0;
            const total = p * q;
            row.querySelector('.row-total').value = total.toLocaleString();
            subtotal += total;
        });

        const discInputValue = parseFloat(document.getElementById('discount_input').value) || 0;
        let calculatedDiscount = 0;

        if (discountType === 'per') {
            const safeDisc = discInputValue > 100 ? 100 : discInputValue;
            if(discInputValue > 100) document.getElementById('discount_input').value = 100;
            calculatedDiscount = subtotal * (safeDisc / 100);
        } else {
            calculatedDiscount = discInputValue;
        }

        const finalAmount = Math.max(0, subtotal - calculatedDiscount);
        document.getElementById('subtotal_display').innerText = subtotal.toLocaleString() + " SDG";
        document.getElementById('final_total_display').innerText = finalAmount.toLocaleString();
        document.getElementById('hidden_subtotal').value = subtotal;
        document.getElementById('hidden_discount_amount').value = calculatedDiscount;
        document.getElementById('hidden_final').value = finalAmount;
    }

    function submitInvoice(actionType) {
        document.getElementById('form_action').value = actionType;
        const rows = document.querySelectorAll('.item-row');
        if(rows.length === 0) {
            alert('يرجى إضافة صنف واحد على الأقل قبل الحفظ');
            return;
        }
        if(actionType === 'approve') {
            if(confirm('تأكيد: هل أنت متأكد من تصديق الفاتورة؟ سيتم خصم الكميات من المخزن الآن.')) {
                document.getElementById('salesForm').submit();
            }
        } else {
            document.getElementById('salesForm').submit();
        }
    }

    function addRow() {
        const tbody = document.getElementById('sales_body');
        const template = document.getElementById('row_template').innerHTML;
        tbody.insertAdjacentHTML('beforeend', template.replace(/INDEX/g, rowIndex++));
    }

    function togglePaidInput() {
        const status = document.getElementById('payment_status').value;
        const container = document.getElementById('paid_amount_container');
        if(status === 'partial') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            document.getElementById('paid_amount_input').value = "";
        }
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
        calculateFinalTotal();
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
        calculateFinalTotal();
    }

    function applyGlobalList(listId) {
        document.querySelectorAll('.price-list-select').forEach(s => { 
            s.value = listId; 
            updateRowPrice(s); 
        });
    }

    document.addEventListener('click', e => { 
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.product-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    window.onload = addRow;
</script>
@endsection