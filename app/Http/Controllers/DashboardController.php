<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk Bar Chart: Total Transaksi per Vendor
        $vendorTotals = Supplier::leftJoin('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->leftJoin('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->select(
                'suppliers.nama',
                DB::raw('COALESCE(SUM(purchase_order_items.net_value), 0) as total')
            )
            ->groupBy('suppliers.id', 'suppliers.nama')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Data untuk Line Chart: Tren Transaksi Bulanan (6 bulan terakhir)
        $monthlyTotals = PurchaseOrder::selectRaw('MONTH(date) as bulan, YEAR(date) as tahun, COUNT(*) as total_po')
            ->whereBetween('date', [
                now()->subMonths(5)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get();

        // Format data untuk chart
        $vendorLabels = $vendorTotals->pluck('nama')->toArray();
        $vendorValues = $vendorTotals->pluck('total')->toArray();

        // Format tanggal bulan
        $bulanIndonesia = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        $monthlyLabels = $monthlyTotals->map(function ($item) use ($bulanIndonesia) {
            return $bulanIndonesia[$item->bulan] ?? 'Bln';
        })->toArray();

        $monthlyValues = $monthlyTotals->pluck('total_po')->toArray();

        // Statistik Ringkas
        $totalPO = PurchaseOrder::count();
        $totalSupplier = Supplier::count();
        $totalValue = PurchaseOrderItem::sum('net_value') ?? 0;
        $poCompleted = PurchaseOrder::where('status', 'received')->count();

        return view('dashboard.index', [
            'tittle' => 'Dashboard | Portal Supplier',
            'vendorLabels' => $vendorLabels,
            'vendorValues' => $vendorValues,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'totalPO' => $totalPO,
            'totalSupplier' => $totalSupplier,
            'totalValue' => $totalValue,
            'poCompleted' => $poCompleted,
        ]);
    }
}
