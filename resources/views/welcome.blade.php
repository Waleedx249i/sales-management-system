@extends('layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="text-center mb-12">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2">مرحباً بك في لوحة الإدارة</h1>
    <p class="text-gray-600">إليك اختصارات الوصول السريع لأقسام النظام</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <a href="{{ route('sales.index') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-blue-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-blue-100 rounded-full">
            <i class="fas fa-cart-shopping text-2xl text-blue-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">المبيعات</h3>
            <p class="text-gray-500 text-sm">إنشاء فواتير وعمليات البيع</p>
        </div>
    </a>

    <a href="{{ route('products.index') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-green-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-green-100 rounded-full">
            <i class="fas fa-boxes-stacked text-2xl text-green-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">المخزون والمنتجات</h3>
            <p class="text-gray-500 text-sm">إدارة الأصناف والكميات</p>
        </div>
    </a>

    <a href="{{ route('suppliers.index') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-orange-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-orange-100 rounded-full">
            <i class="fas fa-truck-field text-2xl text-orange-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">الموردين</h3>
            <p class="text-gray-500 text-sm">إدارة فواتير المشتريات</p>
        </div>
    </a>

    <a href="{{ route('finance.index') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-red-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-red-100 rounded-full">
            <i class="fas fa-money-bill-transfer text-2xl text-red-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">الإدارة المالية</h3>
            <p class="text-gray-500 text-sm">التكاليف، النثريات، والتحويلات</p>
        </div>
    </a>

    <a href="{{ route('reports.dashboard') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-purple-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-purple-100 rounded-full">
            <i class="fas fa-file-invoice-dollar text-2xl text-purple-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">التقارير</h3>
            <p class="text-gray-500 text-sm">تحليل الأداء والأرباح</p>
        </div>
    </a>

    <a href="{{ route('pos.index') }}" class="flex items-center p-6 bg-white rounded-xl shadow-md border-r-4 border-teal-600 hover:scale-105 transition-transform">
        <div class="p-4 bg-teal-100 rounded-full">
            <i class="fas fa-cash-register text-2xl text-teal-600"></i>
        </div>
        <div class="mr-4">
            <h3 class="font-bold text-xl">نقطة البيع</h3>
            <p class="text-gray-500 text-sm">واجهة البيع السريع</p>
        </div>
    </a>

</div>
@endsection