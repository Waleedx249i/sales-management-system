<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
// التعديل هنا:
use Native\Desktop\Dialog;
use Native\Desktop\Notification;

class BackupController extends Controller
{
    public function index()
    {
        return view('settings.backup');
    }

 // تصدير نسخة احتياطية
    public function export()
    {
        // التصليح: استخدام الاسم الصحيح لقاعدة بيانات نيتف
        $dbPath = database_path('nativephp.sqlite');
        
        // التحقق من وجود الملف الأصلي قبل البدء
        if (!File::exists($dbPath)) {
            return back()->with('error', 'ملف قاعدة البيانات الأصلي غير موجود.');
        }

        $fileName = 'backup-' . date('Y-m-d-His') . '.sqlite';

        // فتح نافذة "Save As"
        $savePath = Dialog::new()
            ->title('اختر مكان حفظ النسخة الاحتياطية')
            ->defaultPath($fileName)
            ->save();

        if (!empty($savePath)) {
            File::copy($dbPath, $savePath);
            
            Notification::new()
                ->title('تم بنجاح')
                ->message('تم حفظ النسخة الاحتياطية بنجاح')
                ->show();

            return back()->with('success', 'تم الحفظ في: ' . $savePath);
        }

        return back()->with('error', 'لم يتم تحديد مسار للحفظ.');
    }

    // استيراد نسخة احتياطية
   public function import()
{
    $result = Dialog::new()
        ->title('اختر ملف قاعدة البيانات لاستعادته')
        ->open();

    if (empty($result)) {
        return back()->with('error', 'تم إلغاء العملية.');
    }

    $selectedFile = is_array($result) ? $result[0] : $result;

    if (!File::exists($selectedFile)) {
        return back()->with('error', 'الملف المختار غير موجود.');
    }

    $dbPath = database_path('nativephp.sqlite');
    $backupPath = $dbPath . '.' . time() . '.bak'; 
    
    try {
        // الحل: نستخدم Copy بدل Move عشان نتجنب خطأ "File in use"
        // الويندوز بيسمح بنسخ ملف مفتوح لكن بيمنع نقله
        if (File::exists($dbPath)) {
            File::copy($dbPath, $backupPath); 
        }
        
        // استخدام سيل من البيانات لكتابة الملف الجديد فوق القديم 
        // هاد السطر بيعمل Overwrite حتى لو الملف مفتوح في بعض الحالات
        File::put($dbPath, File::get($selectedFile));

        Notification::new()
            ->title('تمت الاستعادة بنجاح')
            ->message('يرجى إعادة تشغيل التطبيق لتحديث البيانات')
            ->show();

        return back()->with('success', 'تمت الاستعادة! أغلق البرنامج وافتحه الآن لترى البيانات الجديدة.');

    } catch (\Exception $e) {
        return back()->with('error', 'فشل الاستبدال لأن الملف قيد الاستخدام. حاول إغلاق أي برنامج يفتح القاعدة ثم حاول مجدداً.');
    }
}
}