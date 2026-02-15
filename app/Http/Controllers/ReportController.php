<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // 1. لوحة القيادة (Dashboard)
    public function index(Request $request)
    {
        $totalSales = DB::table('sales_invoices')->sum('final_amount') + DB::table('pos_sales')->sum('total_amount');
        $totalPurchases = DB::table('import_invoices')->sum('total_goods_sdg');

        $stockValue = DB::table('products as p')
            ->leftJoin('product_costs as pc', 'p.id', '=', 'pc.product_id')
            ->sum(DB::raw('p.quantity * COALESCE(pc.weighted_average_cost, 0)'));

        $totalDebts = DB::table('sales_invoices')->sum('remaining_amount');

        $topProducts = DB::table('sales_invoice_items as sii')
            ->join('products as p', 'p.id', '=', 'sii.product_id')
            ->select('p.name', 'p.code', DB::raw('SUM(sii.quantity) as qty'))
            ->groupBy('sii.product_id', 'p.name', 'p.code')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        return view('reports.dashboard', compact('totalSales', 'totalPurchases', 'stockValue', 'totalDebts', 'topProducts'));
    }

    // 2. إحصائيات المبيعات العامة
    public function sales(Request $request)
    {
        $storeRevenue = DB::table('sales_invoices')->sum('final_amount');
        $storeCost = DB::table('sales_invoice_items')->sum(DB::raw('unit_cost * quantity'));
        $storeProfit = $storeRevenue - $storeCost;

        $posRevenue = DB::table('pos_sales')->sum('total_amount');
        $posCost = DB::table('pos_sales as ps')
            ->join('pos_consignment_items as pci', 'ps.consignment_item_id', '=', 'pci.id')
            ->sum(DB::raw('pci.unit_cost * ps.quantity_sold'));
        $posProfit = $posRevenue - $posCost;

        $chartData = DB::table('sales_invoices')
            ->select(DB::raw('date(created_at) as date'), DB::raw('SUM(final_amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->limit(7)
            ->get();

        return view('reports.sales', compact('storeRevenue', 'storeProfit', 'posRevenue', 'posProfit', 'chartData'));
    }

    // --- مبيعات المحل المباشرة ---
    public function storeSales(Request $request)
    {
        $query = DB::table('sales_invoices as si');

        if ($request->filled('date')) {
            $query->whereDate('si.created_at', $request->date);
        }
        if ($request->filled('month')) {
            $query->where('si.created_at', 'like', $request->month.'%');
        }

        $selectFields = [
            'si.invoice_number',
            'si.customer_name',
            'si.final_amount',
            'si.paid_amount',
            'si.remaining_amount',
            'si.status',
            // تحويل التاريخ لنص صريح لمنع مشاكل الإكسيل
            DB::raw("strftime('%d-%m-%Y', si.created_at) as date_text"),
            DB::raw('(SELECT SUM(unit_cost * quantity) FROM sales_invoice_items WHERE sales_invoice_id = si.id) as total_cost'),
        ];

        $data = $query->select($selectFields)->orderByDesc('si.created_at')->get();

        if ($request->has('export')) {
            $headers = ['رقم الفاتورة', 'العميل', 'الإجمالي', 'المدفوع', 'المتبقي', 'الحالة', 'التاريخ', 'التكلفة'];

            return $this->exportToExcel($data, $headers, 'Store_Report');
        }

        return view('reports.store_sales', compact('data'));
    }

    // --- مبيعات نقاط التوزيع (العهد) ---
    public function posSales(Request $request)
    {
        $query = DB::table('pos_sales as ps')
            ->join('pos_consignment_items as pci', 'ps.consignment_item_id', '=', 'pci.id')
            ->join('pos_consignments as pc', 'pci.pos_consignment_id', '=', 'pc.id');

        if ($request->filled('date')) {
            $query->whereDate('ps.sale_date', $request->date);
        }
        if ($request->filled('pos_name')) {
            $query->where('pc.pos_name', $request->pos_name);
        }

        $selectFields = [
            'pc.pos_name',
            'pci.product_name',
            'pci.product_code',
            'ps.quantity_sold',
            'ps.unit_price',
            'ps.total_amount',
            DB::raw("strftime('%d-%m-%Y', ps.sale_date) as date_text"),
            DB::raw('(pci.unit_cost * ps.quantity_sold) as item_cost'),
        ];

        $data = $query->select($selectFields)->orderByDesc('ps.sale_date')->get();

        if ($request->has('export')) {
            $headers = ['النقطة', 'الصنف', 'الكود', 'الكمية', 'السعر', 'الإجمالي', 'التاريخ', 'التكلفة'];

            return $this->exportToExcel($data, $headers, 'POS_Report');
        }

        $posList = DB::table('pos_consignments')->distinct()->pluck('pos_name');

        return view('reports.pos_sales', compact('data', 'posList'));
    }

    // --- تقرير المشتريات ---
    public function purchases(Request $request)
    {
        $query = DB::table('import_invoices as i')
            ->join('suppliers as s', 's.id', '=', 'i.supplier_id');

        $query = $this->applyDateFilter($query, $request, 'i.created_at');

        $selectFields = [
            'i.invoice_number',
            's.name as supplier',
            DB::raw("strftime('%d-%m-%Y', i.created_at) as date_text"),
            'i.total_goods_sdg',
            'i.total_logistic',
            'i.cost_ratio_percent',
            'i.status',
        ];

        $data = $query->select($selectFields)->orderByDesc('i.created_at')->get();

        if ($request->has('export')) {
            $headers = ['رقم الفاتورة', 'المورد', 'التاريخ', 'قيمة البضاعة', 'تكاليف لوجستية', 'نسبة التكلفة %', 'الحالة'];

            return $this->exportToExcel($data, $headers, 'purchases_report');
        }

        return view('reports.purchases', compact('data'));
    }

    // --- تقرير المخزون ---
    public function inventory(Request $request)
    {
        $selectFields = [
            'p.code',
            'p.name',
            'p.quantity',
            DB::raw('COALESCE(pc.weighted_average_cost, 0) as cost'),
            DB::raw('(p.quantity * COALESCE(pc.weighted_average_cost, 0)) as total_value'),
        ];

        $data = DB::table('products as p')
            ->leftJoin('product_costs as pc', 'p.id', '=', 'pc.product_id')
            ->select($selectFields)
            ->where('p.quantity', '>', 0)
            ->orderByDesc('total_value')
            ->get();

        if ($request->has('export')) {
            $headers = ['كود الصنف', 'اسم الصنف', 'الكمية الحالية', 'متوسط التكلفة', 'إجمالي القيمة'];

            return $this->exportToExcel($data, $headers, 'inventory_report');
        }

        return view('reports.inventory', compact('data'));
    }

    // --- تقرير العملاء والديون ---
    public function customers(Request $request)
    {
        $selectFields = [
            'customer_name',
            DB::raw('COUNT(id) as invoices_count'),
            DB::raw('SUM(final_amount) as total_purchases'),
            DB::raw('SUM(paid_amount) as total_paid'),
            DB::raw('SUM(remaining_amount) as total_debt'),
        ];

        $data = DB::table('sales_invoices')
            ->select($selectFields)
            ->groupBy('customer_name')
            ->orderByDesc('total_debt')
            ->get();

        if ($request->has('export')) {
            $headers = ['اسم العميل', 'عدد الفواتير', 'إجمالي المشتريات', 'المسدد', 'المديونية الحالية'];

            return $this->exportToExcel($data, $headers, 'customers_debts');
        }

        return view('reports.customers', compact('data'));
    }

    // --- Helpers ---

    private function applyDateFilter($query, $request, $dateColumn)
    {
        if ($request->filled('date')) {
            return $query->whereDate($dateColumn, $request->date);
        }
        if ($request->filled('month')) {
            return $query->where($dateColumn, 'like', $request->month.'%');
        }
        if ($request->filled('year')) {
            return $query->whereYear($dateColumn, $request->year);
        }

        return $query;
    }

    private function exportToExcel($data, $headers, $filename)
    {
        return new StreamedResponse(function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($handle, $headers);

            foreach ($data as $row) {
                $rowArray = (array) $row;
                $cleanRow = [];
                foreach ($rowArray as $key => $value) {
                    // حماية حقل التاريخ من "ذكاء" إكسيل
                    if ($key === 'date_text') {
                        $cleanRow[] = '="'.$value.'"';
                    } else {
                        $cleanRow[] = $value;
                    }
                }
                fputcsv($handle, $cleanRow);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'_'.date('Y-m-d').'.csv"',
        ]);
    }
}
