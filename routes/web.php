<?php

use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ImportInvoiceController;
use App\Http\Controllers\PosConsignmentController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackupController;

Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
Route::get('/backup/export', [BackupController::class, 'export'])->name('backup.export');
Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');

Route::get('/', function () {
    return view('welcome');
});
Route::resource('products', ProductController::class);

Route::resource('import-invoices', ImportInvoiceController::class);
Route::resource('suppliers', SupplierController::class);

Route::prefix('import-invoices')->name('import-invoices.')->group(function () {
    // مسار تغيير الحالة (الذي تسبب في الخطأ)
    Route::patch('/{id}/update-status', [ImportInvoiceController::class, 'updateStatus'])->name('update-status');

    // مسار الطباعة
    Route::get('/{id}/print', [ImportInvoiceController::class, 'print'])->name('print');
});

Route::resource('price-lists', PriceListController::class);
Route::post('price-lists/{id}/toggle', [PriceListController::class, 'toggleStatus'])->name('price-lists.toggle');

Route::resource('sales', SalesInvoiceController::class);
Route::post('/sales/{id}/add-payment', [SalesInvoiceController::class, 'addPayment'])->name('sales.add_payment');
Route::post('sales/{id}/approve', [SalesInvoiceController::class, 'approve'])->name('sales.approve');

Route::prefix('pos-consignments')->name('pos.')->group(function () {
    Route::get('/', [PosConsignmentController::class, 'index'])->name('index');
    Route::get('/create', [PosConsignmentController::class, 'create'])->name('create');
    Route::post('/store', [PosConsignmentController::class, 'store'])->name('store');
    Route::get('/show/{id}', [PosConsignmentController::class, 'show'])->name('show');

    // تسجيل بيعة جديدة (بدل الـ updateOld)
    Route::post('/record-sale/{itemId}', [PosConsignmentController::class, 'storeSale'])->name('record_sale');

    // شحن بضاعة إضافية
    Route::post('/add-more-stock/{id}', [PosConsignmentController::class, 'addMoreStock'])->name('add_more_stock');

    // حذف وإرجاع
    Route::get('/remove-item/{id}', [PosConsignmentController::class, 'removeItem'])->name('remove_item');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('dashboard'); // الداشبورد
    route::get('/sales', [ReportController::class, 'sales'])->name('sales'); // تقارير المبيعات
    Route::get('/store-sales', [ReportController::class, 'storeSales'])->name('store_sales');
    Route::get('/pos-sales', [ReportController::class, 'posSales'])->name('pos_sales');
    Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases'); // المشتريات
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory'); // المخزون
    Route::get('/customers', [ReportController::class, 'customers'])->name('customers'); // العملاء والديون
});

// مجموعة مسارات الإدارة المالية
Route::prefix('finance')->group(function () {

    // 1. الواجهة الرئيسية (لوحة التحكم المالية)
    Route::get('/', [FinancialController::class, 'index'])->name('finance.index');

    // 2. واجهة حساب التكاليف والنثريات
    Route::get('/costs', [FinancialController::class, 'costsAccount'])->name('finance.costs');

    // 3. واجهة حساب الأرباح والمسحوبات الشخصية
    Route::get('/personal', [FinancialController::class, 'personalAccount'])->name('finance.personal');

    // 4. تنفيذ عملية تحويل مالي (POST) من الحساب الرئيسي للفرعي
    Route::post('/store', [FinancialController::class, 'store'])->name('finance.store');

});
