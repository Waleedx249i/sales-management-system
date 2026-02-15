@extends('layouts.app', ['title' => 'إنشاء فاتورة جديدة'])
@section('content')
<body class="p-4 md:p-8">

    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800"> الفاتورة</h2>
               
            </div>
            <div class="flex items-center gap-3">
                <label class="font-bold text-gray-600">سعر الصرف:</label>
                <input type="number" id="exchange_rate" value="76" oninput="calculateAll()" 
                    class="w-24 p-2 border-2 border-indigo-500 rounded-lg text-center text-xl font-bold outline-none focus:ring-2 ring-indigo-200">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-indigo-900 p-4 text-white font-bold">بيانات الأصناف</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="p-2 border text-xs">صورة الصنف</th>
                                <th class="p-2 border text-xs">اسم الصنف</th>
                                <th class="p-2 border text-xs">كود الصنف</th>
                                <th class="p-2 border text-xs bg-indigo-50">السعر (مصري)</th>
                                <th class="p-2 border text-xs">السعر (سوداني)</th>
                                <th class="p-2 border text-xs">الكمية</th>
                                <th class="p-2 border text-xs">إجمالي السعر</th>
                                <th class="p-2 border text-xs bg-green-50 text-green-700 font-bold">تكلفة القطعة النهائية</th>
                            </tr>
                        </thead>
                        <tbody id="items_body">
                            <tr class="item-row border-b hover:bg-gray-50">
                                <td class="p-2 border"><input type="file" class="w-20 text-[10px]"></td>
                                <td class="p-2 border"><input type="text" placeholder="اسم الصنف" class="w-full outline-none text-right px-1"></td>
                                <td class="p-2 border"><input type="text" placeholder="Code" class="w-full text-center outline-none"></td>
                                <td class="p-2 border bg-indigo-50"><input type="number" class="price_egp w-full text-center font-bold bg-transparent" value="100" oninput="calculateAll()"></td>
                                <td class="p-2 border"><input type="text" class="price_sdg w-full text-center bg-transparent" readonly></td>
                                <td class="p-2 border"><input type="number" class="qty w-full text-center font-bold" value="1" oninput="calculateAll()"></td>
                                <td class="p-2 border font-bold"><input type="text" class="item_total w-full text-center bg-transparent" readonly></td>
                                <td class="p-2 border bg-green-50"><input type="text" class="unit_cost w-full text-center font-bold text-green-700 bg-transparent" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    <button onclick="addRow()" class="text-indigo-600 font-bold text-sm hover:underline">+ إضافة صنف جديد</button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-fit">
                <div class="bg-slate-800 p-4 text-white font-bold text-center">جدول اللوجيستي</div>
                <table class="w-full text-sm text-center border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="p-2 border text-xs text-right px-4">اسم المصروف</th>
                            <th class="p-2 border text-xs">السعر للسوداني</th>
                        </tr>
                    </thead>
                    <tbody id="logistic_body">
                        <tr class="logistic-row border-b">
                            <td class="p-2 border"><input type="text" value="ترحيل" class="w-full outline-none text-right px-2"></td>
                            <td class="p-2 border"><input type="number" class="log_price w-full text-center font-bold" value="0" oninput="calculateAll()"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-3 border-b text-center">
                    <button onclick="addLogisticRow()" class="text-slate-600 font-bold text-sm hover:underline">+ إضافة مصروف لوجيستي</button>
                </div>
                
                <div class="p-6 space-y-4 bg-slate-50">
                    <div class="flex justify-between font-bold text-gray-600">
                        <span>إجمالي بضاعة الفاتورة:</span>
                        <span id="grand_total_display">0.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-red-600">
                        <span>مجموع اللوجيستي:</span>
                        <span id="logistics_total_display">0.00</span>
                    </div>
                    <div class="pt-4 border-t flex justify-between items-center">
                        <span class="font-black text-indigo-900">نسبة التكلفة الإضافية:</span>
                        <div class="text-right">
                            <span id="cost_percentage_display" class="text-2xl font-black text-indigo-600">0%</span>
                            <p class="text-[10px] text-gray-400">زيادة محملة على السعر</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex justify-end">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-4 rounded-xl font-bold shadow-lg shadow-indigo-100 transition-all active:scale-95">حفظ واعتماد الفاتورة</button>
        </div>
    </div>

    <script>
        function calculateAll() {
            const exRate = parseFloat(document.getElementById('exchange_rate').value) || 0;
            
            // 1. حساب مجموع اللوجيستي
            let logTotal = 0;
            document.querySelectorAll('.log_price').forEach(input => {
                logTotal += (parseFloat(input.value) || 0);
            });
            document.getElementById('logistics_total_display').innerText = logTotal.toLocaleString();

            // 2. حساب إجمالي الفاتورة (مجموع الأصناف بالسوداني)
            let grandTotalSdg = 0;
            const items = document.querySelectorAll('.item-row');
            
            items.forEach(row => {
                const egp = parseFloat(row.querySelector('.price_egp').value) || 0;
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                
                const sdg = egp * exRate; // سعر القطعة بالسوداني
                const itemTotal = sdg * qty; // إجمالي الصنف
                
                row.querySelector('.price_sdg').value = sdg.toLocaleString();
                row.querySelector('.item_total').value = itemTotal.toLocaleString();
                
                grandTotalSdg += itemTotal;
            });
            document.getElementById('grand_total_display').innerText = grandTotalSdg.toLocaleString();

            // 3. حساب نسبة التكلفة (تحويل الرقم العشري إلى نسبة مئوية مقروءة)
            let costRatio = 1;
            let displayPercentage = 0;

            if (grandTotalSdg > 0) {
                costRatio = (grandTotalSdg + logTotal) / grandTotalSdg;
                // النسبة المئوية هي (الزيادة / الأصل) * 100
                displayPercentage = ((costRatio - 1) * 100);
            }
            
            // عرض النسبة المئوية (مثلاً: +15.05%)
            document.getElementById('cost_percentage_display').innerText = "+" + displayPercentage.toFixed(2) + "%";

            // 4. حساب تكلفة القطعة النهائية
            items.forEach(row => {
                const egp = parseFloat(row.querySelector('.price_egp').value) || 0;
                const sdg = egp * exRate;
                const finalUnitCost = sdg * costRatio; // تطبيق المعامل في الحساب لضمان الدقة
                row.querySelector('.unit_cost').value = Math.round(finalUnitCost).toLocaleString();
            });
        }

        function addRow() {
            const tbody = document.getElementById('items_body');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelectorAll('input').forEach(i => { if(!i.readOnly && i.type !== 'file') i.value = i.classList.contains('qty') ? 1 : 0; });
            tbody.appendChild(newRow);
            calculateAll();
        }

        function addLogisticRow() {
            const tbody = document.getElementById('logistic_body');
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelector('.log_price').value = 0;
            tbody.appendChild(newRow);
            calculateAll();
        }

        window.onload = calculateAll;
    </script>
@endsection