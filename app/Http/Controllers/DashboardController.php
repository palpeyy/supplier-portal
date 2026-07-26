<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Status invoice yang sudah divalidasi Admin/Purchasing. */
    private const COMPLETED_INVOICE_STATUSES = ['completed', 'ready_for_payment', 'paid', 'processed'];

    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;
        $isSupplier = $userRole === 'Supplier';
        $supplierId = $isSupplier ? $user->supplier_id : null;

        $poItemTotalsSubquery = PurchaseOrderItem::query()
            ->select('purchase_order_id', DB::raw('COALESCE(SUM(net_value), 0) as total_value'))
            ->groupBy('purchase_order_id');

        // Bar chart: total nilai PO per vendor dari invoice yang sudah divalidasi
        $vendorQuery = Supplier::query()
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('invoices', 'purchase_orders.id', '=', 'invoices.purchase_order_id')
            ->joinSub($poItemTotalsSubquery, 'po_item_totals', function ($join) {
                $join->on('purchase_orders.id', '=', 'po_item_totals.purchase_order_id');
            })
            ->whereIn('invoices.status', self::COMPLETED_INVOICE_STATUSES)
            ->select(
                'suppliers.nama',
                DB::raw('COALESCE(SUM(po_item_totals.total_value), 0) as total')
            )
            ->groupBy('suppliers.id', 'suppliers.nama')
            ->orderByDesc('total');

        if ($isSupplier && $supplierId) {
            $vendorQuery->where('suppliers.id', $supplierId);
        } elseif (in_array($userRole, ['Admin', 'Purchasing'], true)) {
            $vendorQuery->where('purchase_orders.created_by', $user->id);
        }

        $vendorTotals = $vendorQuery->take(10)->get();

        // Line chart: tren PO diterima per bulan (berdasarkan waktu status received)
        $chartStart = now()->subMonths(5)->startOfMonth();
        $chartEnd = now()->endOfMonth();

        $monthlyQuery = PurchaseOrder::selectRaw('MONTH(updated_at) as bulan, YEAR(updated_at) as tahun, COUNT(*) as total_po')
            ->where('status', 'received')
            ->whereBetween('updated_at', [$chartStart, $chartEnd])
            ->groupByRaw('YEAR(updated_at), MONTH(updated_at)')
            ->orderByRaw('YEAR(updated_at), MONTH(updated_at)');

        if ($isSupplier && $supplierId) {
            $monthlyQuery->where('supplier_id', $supplierId);
        }

        $monthlyTotals = $monthlyQuery->get()->mapWithKeys(
            fn ($item) => [sprintf('%04d-%02d', $item->tahun, $item->bulan) => (int) $item->total_po]
        );

        $bulanIndonesia = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $monthlyLabels = [];
        $monthlyValues = [];
        for ($i = 0; $i < 6; $i++) {
            $month = $chartStart->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $monthlyLabels[] = ($bulanIndonesia[(int) $month->format('n')] ?? 'Bln') . ' ' . $month->format('Y');
            $monthlyValues[] = $monthlyTotals->get($key, 0);
        }

        $vendorLabels = $vendorTotals->pluck('nama')->toArray();
        $vendorValues = $vendorTotals->pluck('total')->toArray();

        // Statistik ringkas
        $poStatsQuery = PurchaseOrder::query();
        $itemStatsQuery = PurchaseOrderItem::query()->whereHas('purchaseOrder');
        $invoiceStatsQuery = Invoice::query()->whereIn('status', self::COMPLETED_INVOICE_STATUSES);

        if ($isSupplier && $supplierId) {
            $poStatsQuery->where('supplier_id', $supplierId);
            $itemStatsQuery->whereHas('purchaseOrder', fn ($q) => $q->where('supplier_id', $supplierId));
            $invoiceStatsQuery->whereHas('purchaseOrder', fn ($q) => $q->where('supplier_id', $supplierId));
        } elseif (in_array($userRole, ['Admin', 'Purchasing'], true)) {
            $poStatsQuery->where('created_by', $user->id);
            $itemStatsQuery->whereHas('purchaseOrder', fn ($q) => $q->where('created_by', $user->id));
            $invoiceStatsQuery->whereHas('purchaseOrder', fn ($q) => $q->where('created_by', $user->id));
        }

        $totalPO = (clone $poStatsQuery)->count();
        $totalSupplier = $isSupplier ? null : Supplier::count();
        $totalValue = (clone $itemStatsQuery)->sum('net_value') ?? 0;
        $poCompleted = (clone $poStatsQuery)->where('status', 'received')->count();
        $invoiceCompleted = (clone $invoiceStatsQuery)->count();

        return view('dashboard.index', [
            'tittle' => 'Dashboard | Portal Supplier',
            'isSupplier' => $isSupplier,
            'vendorLabels' => $vendorLabels,
            'vendorValues' => $vendorValues,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'totalPO' => $totalPO,
            'totalSupplier' => $totalSupplier,
            'totalValue' => $totalValue,
            'poCompleted' => $poCompleted,
            'invoiceCompleted' => $invoiceCompleted,
        ]);
    }
}
