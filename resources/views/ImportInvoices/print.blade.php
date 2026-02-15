<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة فاتورة #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 40px; color: #333; }
        .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { color: #4f46e5; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f3f4f6; padding: 12px; border: 1px solid #e5e7eb; text-align: right; font-weight: bold; }
        td { padding: 12px; border: 1px solid #e5e7eb; }
        .totals { margin-right: auto; width: 300px; margin-top: 20px; }
        .totals div { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .grand-total { font-size: 1.25rem; font-weight: 900; color: #4f46e5; border-bottom: none !important; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: left; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #4f46e5; color: white; padding: 10px 25px; border-radius: 8px; cursor: pointer; border: none; font-weight: bold;">🖨️ تنفيذ الطباعة</button>
        <button onclick="window.history.back()" style="background: #9ca3af; color: white; padding: 10px 25px; border-radius: 8px; cursor: pointer; border: none; font-weight: bold; margin-right: 10px;">رجوع</button>
    </div>

    <div class="invoice-header">
        <div class="company-info">
            <h1>فاتورة توريد</h1>
            <p>الرقم المرجعي: <strong>#INV-{{ $invoice->invoice_number }}</strong></p>
        </div>
        <div style="text-align: left;">
            <p>التاريخ: {{ $invoice->created_at->format('Y/m/d') }}</p>
            <p>الحالة: {{ $invoice->status == 'completed' ? 'مكتملة' : 'مسودة' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>الصنف / البيان</th>
                <th style="text-align: center;">الكمية</th>
                <th style="text-align: center;">سعر (EGP)</th>
                <th style="text-align: center;">سعر الصرف</th>
                <th style="text-align: center;">التكلفة النهائية (SDG)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product_name ?? $item->name }}</td>
                <td style="text-align: center;">{{ $item->qty }}</td>
                <td style="text-align: center;">{{ number_format($item->price_egp, 2) }}</td>
                <td style="text-align: center;">{{ number_format($invoice->exchange_rate, 2) }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($item->unit_cost) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>
            <span>إجمالي البضاعة (SDG):</span>
            <span>{{ number_format($invoice->total_goods_sdg) }}</span>
        </div>
        <div>
            <span>المصاريف اللوجستية:</span>
            <span>{{ number_format($invoice->total_logistic) }}</span>
        </div>
        <div class="grand-total">
            <span>الإجمالي الكلي:</span>
            <span>{{ number_format($invoice->total_goods_sdg + $invoice->total_logistic) }} SDG</span>
        </div>
    </div>

    <div style="margin-top: 100px; text-align: center; border-top: 1px solid #eee; pt: 20px;">
        <p style="font-size: 12px; color: #999;">نظام إدارة المبيعات الذكي - توقيع المستلم: ............................</p>
    </div>

</body>
</html>