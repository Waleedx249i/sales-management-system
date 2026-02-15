<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'نظام الإدارة' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .nav-link-active { @apply bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 shadow-sm; }
        [x-cloak] { display: none !important; } /* لمنع وميض العناصر قبل تحميل Alpine */
    </style>
</head>
<body class="bg-[#f8fafc] flex flex-col min-h-screen text-slate-700">

    <nav class="bg-indigo-950 text-white shadow-2xl sticky top-0 z-50 print:hidden border-b border-white/5">
    <div class="max-w-[100rem] mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <div class="flex items-center gap-6">
                <div class="hidden xl:flex items-center gap-1">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all {{ Request::is('products*') || Request::is('price-lists*') ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-box-archive"></i> المخزن والأسعار
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute top-full right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 text-slate-800">
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-5 py-3 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-boxes-stacked text-indigo-500 w-5"></i> قائمة المنتجات
                            </a>
                            <a href="{{ route('price-lists.index') }}" class="flex items-center gap-3 px-5 py-3 hover:bg-indigo-50 transition font-bold text-sm border-t border-slate-50">
                                <i class="fa-solid fa-tags text-orange-500 w-5"></i> قوائم الأسعار
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('sales.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all {{ Request::is('sales*') ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-cash-register"></i> المبيعات
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all {{ Request::is('import-invoices*') || Request::is('suppliers*') ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-truck-moving"></i> المشتريات
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute top-full right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 text-slate-800">
                            <a href="{{ route('import-invoices.index') }}" class="flex items-center gap-3 px-5 py-3 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-file-invoice text-indigo-500 w-5"></i> فواتير الوارد
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-5 py-3 hover:bg-indigo-50 transition font-bold text-sm border-t border-slate-50">
                                <i class="fa-solid fa-building text-blue-500 w-5"></i> سجل الموردين
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('pos.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all {{ Request::is('pos-consignments*') ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fa-solid fa-store"></i> نقاط البيع
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all {{ Request::is('reports*') || Request::is('finance*') ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-chart-pie"></i> التقارير والمالية
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition class="absolute top-full right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 py-3 text-slate-800">
                            
                            <p class="px-5 py-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">تقارير النظام</p>
                            <a href="{{ route('reports.dashboard') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-tachometer-alt text-indigo-500 w-5"></i> لوحة التحكم 
                            </a>
                            <a href="{{ route('reports.sales') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-chart-line text-indigo-500 w-5"></i> مبيعات عامة
                            </a>
                            <a href="{{ route('reports.store_sales') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-shop text-blue-500 w-5"></i> مبيعات المتجر
                            </a>
                            <a href="{{ route('reports.pos_sales') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-location-dot text-orange-500 w-5"></i> مبيعات نقاط البيع
                            </a>
                            <a href="{{ route('reports.purchases') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-cart-shopping text-emerald-500 w-5"></i> تقارير المشتريات
                            </a>
                            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm">
                                <i class="fa-solid fa-warehouse text-amber-500 w-5"></i> تقرير المخزون
                            </a>
                            <a href="{{ route('reports.customers') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-indigo-50 transition font-bold text-sm border-b border-slate-50 pb-3">
                                <i class="fa-solid fa-users text-rose-500 w-5"></i> الديون والعملاء
                            </a>

                            <p class="px-5 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">المحاسبة الشخصية</p>
                            <a href="{{ route('finance.index') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-emerald-50 transition font-bold text-sm">
                                <i class="fa-solid fa-wallet text-emerald-600 w-5"></i> ملخص الحسابات
                            </a>
                            <a href="{{ route('finance.costs') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-orange-50 transition font-bold text-sm">
                                <i class="fa-solid fa-money-bill-transfer text-orange-600 w-5"></i> سجل التكاليف
                            </a>
                            <a href="{{ route('finance.personal') }}" class="flex items-center gap-3 px-5 py-2 hover:bg-purple-50 transition font-bold text-sm">
                                <i class="fa-solid fa-user-shield text-purple-600 w-5"></i> مسحوبات شخصية
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-black text-white shadow-lg">
                    A
                </div>
            </div>
        </div>
    </div>
</nav>

    <main class="flex-grow p-6 md:p-10 max-w-[100rem] mx-auto w-full">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="mb-8 flex items-center justify-between p-4 bg-emerald-50 border-r-8 border-emerald-500 rounded-2xl shadow-sm animate-in fade-in slide-in-from-left-5">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <p class="text-sm font-black text-emerald-900 leading-none">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-300 hover:text-emerald-500"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>