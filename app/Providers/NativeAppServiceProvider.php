<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
   public function boot(): void
{
    Window::open()
        ->width(1920)
        ->height(1200)
        ->showDevTools(false) // إخفاء نافذة المطور
         // إخفاء شريط القوائم العلوي (أو يمكنك استخدام rememberState)
        ->title('نظام إدارة المبيعات');

    // لإلغاء القائمة تماماً (File, Edit, etc.)
    \Native\Desktop\Facades\MenuBar::create()->label('نظام المبيعات');
}
    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
{
    return [
        'upload_max_filesize' => '20M',
        'post_max_size' => '25M',
        'memory_limit' => '512M',
        'max_execution_time' => '300',
    ];
}
}
