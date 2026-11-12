<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate   = $request->input('end_date', date('Y-m-d'));

        $salesQuery = Sale::whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);

        $totalSales        = (clone $salesQuery)->sum('total');
        $totalTransactions = (clone $salesQuery)->count();
        $avgTransaction    = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        $detailTable = DB::getSchemaBuilder()->hasTable('sale_items') ? 'sale_items' : 'sale_details';

        $totalCost = DB::table($detailTable)
            ->join('sales', "{$detailTable}.sale_id", '=', 'sales.id')
            ->join('products', "{$detailTable}.product_id", '=', 'products.id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$startDate, $endDate])
            ->sum(DB::raw("{$detailTable}.quantity * products.purchase_price"));

        $totalProfit = $totalSales - $totalCost;

        $dailySales = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(total) as omzet')
        )
        ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date', 'DESC')
        ->get();

        $topProducts = DB::table($detailTable)
            ->select(
                'products.name',
                DB::raw("SUM({$detailTable}.quantity) as total_qty"),
                DB::raw("SUM({$detailTable}.subtotal) as total_omzet")
            )
            ->join('sales', "{$detailTable}.sale_id", '=', 'sales.id')
            ->join('products', "{$detailTable}.product_id", '=', 'products.id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalProfit',
            'totalTransactions',
            'avgTransaction',
            'dailySales',
            'topProducts'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate   = $request->input('end_date', date('Y-m-d'));

        $salesQuery = Sale::whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        $totalSales = (clone $salesQuery)->sum('total');
        $totalTransactions = (clone $salesQuery)->count();

        $detailTable = DB::getSchemaBuilder()->hasTable('sale_items') ? 'sale_items' : 'sale_details';

        $dailySales = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(total) as omzet')
        )
        ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date', 'ASC')
        ->get();

        $topProducts = DB::table($detailTable)
            ->select(
                'products.name',
                DB::raw("SUM({$detailTable}.quantity) as total_qty"),
                DB::raw("SUM({$detailTable}.subtotal) as total_omzet")
            )
            ->join('sales', "{$detailTable}.sale_id", '=', 'sales.id')
            ->join('products', "{$detailTable}.product_id", '=', 'products.id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'DESC')
            ->get();

        $fileName = "Laporan_Penjualan_{$startDate}_sd_{$endDate}.xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"></head>
        <body>
            <h2>LAPORAN PENJUALAN - WARUNG DARIS</h2>
            <p><b>Periode:</b> '.$startDate.' s/d '.$endDate.'</p>
            <p><b>Total Penjualan:</b> Rp '.number_format($totalSales, 0, ',', '.').'<br>
               <b>Total Transaksi:</b> '.$totalTransactions.'</p>
            <br>
            <h3>Rincian Penjualan Harian</h3>
            <table border="1">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah Transaksi</th>
                        <th>Omzet (Rp)</th>
                    </tr>
                </thead>
                <tbody>';
                $no = 1;
                foreach ($dailySales as $sale) {
                    echo '<tr>
                        <td>'.$no++.'</td>
                        <td>'.$sale->date.'</td>
                        <td>'.$sale->total_transactions.'</td>
                        <td>'.$sale->omzet.'</td>
                    </tr>';
                }
        echo '  </tbody>
            </table>
            <br>
            <h3>Produk Terjual</h3>
            <table border="1">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Omzet (Rp)</th>
                    </tr>
                </thead>
                <tbody>';
                $noProd = 1;
                foreach ($topProducts as $prod) {
                    echo '<tr>
                        <td>'.$noProd++.'</td>
                        <td>'.$prod->name.'</td>
                        <td>'.$prod->total_qty.'</td>
                        <td>'.$prod->total_omzet.'</td>
                    </tr>';
                }
        echo '  </tbody>
            </table>
        </body>
        </html>';
        exit;
    }
}