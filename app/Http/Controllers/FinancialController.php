<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    // الواجهة الرئيسية - نظرة عامة
    public function index()
    {
        // 1. قيمة البضاعة الموجودة الآن في المخزن (على أساس متوسط التكلفة)
        $inventoryValue = DB::table('products as p')
            ->join('product_costs as pc', 'p.id', '=', 'pc.product_id')
            ->sum(DB::raw('pc.weighted_average_cost * p.quantity'));

        // 2. تكلفة البضاعة التي خرجت من المخزن وتم بيعها فعلياً (محل + نقاط بيع)
        $storeSoldCost = DB::table('sales_invoice_items')->sum(DB::raw('unit_cost * quantity'));
        $posSoldCost = DB::table('pos_sales as ps')
            ->join('pos_consignment_items as pci', 'ps.consignment_item_id', '=', 'pci.id')
            ->sum(DB::raw('pci.unit_cost * ps.quantity_sold'));

        $totalSoldCost = $storeSoldCost + $posSoldCost; // إجمالي تكلفة المبيعات

        // 3. رأس المال الكلي (قيمة ما تملكه من بضاعة الآن + ما صرفته على بضاعة تم بيعها)
        $workingCapital = $inventoryValue + $totalSoldCost;

        // 4. الأرباح (الإيرادات الكلية - التكلفة الكلية للبضاعة المباعة)
        $storeRevenue = DB::table('sales_invoices')->sum('final_amount');
        $posRevenue = DB::table('pos_sales')->sum('total_amount');
        $totalProfitGenerated = ($storeRevenue + $posRevenue) - $totalSoldCost;

        // 5. خصم المصاريف والمسحوبات
        try {
            $costsBalance = DB::table('financial_accounts')->where('account_type', 'costs')->sum('amount');
            $personalBalance = DB::table('financial_accounts')->where('account_type', 'personal_profits')->sum('amount');
        } catch (\Exception $e) {
            $costsBalance = 0;
            $personalBalance = 0;
        }

        // الربح الصافي المتبقي تحت يدك
        $remainingMainProfit = $totalProfitGenerated - ($costsBalance + $personalBalance);

        return view('finance.index', compact(
            'inventoryValue',
            'totalSoldCost',
            'workingCapital',
            'totalProfitGenerated',
            'remainingMainProfit',
            'costsBalance',
            'personalBalance'
        ));
    }

    // واجهة حساب التكاليف
    public function costsAccount()
    {
        $transactions = FinancialAccount::where('account_type', 'costs')->latest()->get();
        $total = FinancialAccount::getBalance('costs');

        return view('finance.costs', compact('transactions', 'total'));
    }

    // واجهة المسحوبات الشخصية
    public function personalAccount()
    {
        $transactions = FinancialAccount::where('account_type', 'personal_profits')->latest()->get();
        $total = FinancialAccount::getBalance('personal_profits');

        return view('finance.personal', compact('transactions', 'total'));
    }

    // تنفيذ عملية تحويل/صرف
    public function store(Request $request)
    {
        $request->validate([
            'account_type' => 'required',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
        ]);

        FinancialAccount::create($request->all());

        return back()->with('success', 'تم تسجيل العملية بنجاح');
    }
}
