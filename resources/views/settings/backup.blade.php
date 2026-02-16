@extends('layouts.app')

@section('title', 'النسخ الاحتياطي والاستعادة')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <div class="p-3 bg-blue-600 rounded-lg shadow-lg">
            <i class="fas fa-database text-2xl text-white"></i>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة البيانات</h1>
            <p class="text-gray-500 text-sm">تأمين بياناتك من خلال النسخ الاحتياطي والاستعادة</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-600 text-green-800 p-4 mb-6 rounded shadow-sm">
            <i class="fas fa-check-circle ml-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-600 text-red-800 p-4 mb-6 rounded shadow-sm">
            <i class="fas fa-exclamation-triangle ml-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-export text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">تصدير نسخة احتياطية</h3>
                <p class="text-gray-500 text-sm mb-8 px-4">
                    سيتم فتح نافذة لاختيار مكان حفظ ملف البيانات الخاص بك. يمكنك حفظه على فلاشة أو قرص صلب خارجي.
                </p>
                <a href="{{ route('backup.export') }}" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-save ml-2"></i> حفظ النسخة الآن
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-file-import text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">استرجاع بيانات قديمة</h3>
                <p class="text-gray-500 text-sm mb-8 px-4">
                    <span class="text-red-500 font-bold">تحذير:</span> استعادة نسخة قديمة سيؤدي لمسح البيانات الحالية واستبدالها بالكامل.
                </p>
                <form action="{{ route('backup.import') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('هل أنت متأكد من رغبتك في استبدال قاعدة البيانات الحالية؟')" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-upload ml-2"></i> اختيار ملف واستعادة
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div class="mt-10 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-4">
        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
        <p class="text-sm text-blue-800 leading-relaxed">
            <strong>نصيحة أمنية:</strong> يفضل عمل نسخة احتياطية بشكل يومي وحفظها في مكان منفصل عن جهاز الكمبيوتر لضمان عدم فقدان بيانات المبيعات في حال تعطل الجهاز.
        </p>
    </div>
</div>
@endsection