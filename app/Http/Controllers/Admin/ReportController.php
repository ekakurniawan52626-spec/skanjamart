<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Keuntungan & unit terjual per produk, dari order yang statusnya bukan cancelled
        $perProduct = OrderItem::query()
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(price * quantity) as total_omzet')
            )
            ->whereHas('order', fn ($o) => $o->where('status', '!=', 'cancelled'))
            ->groupBy('product_id', 'product_name')
            ->get();

        // Ambil cost_price produk yang masih ada (produk yang sudah dihapus tetap tampil tanpa profit)
        $costMap = Product::pluck('cost_price', 'id');

        $report = $perProduct->map(function ($row) use ($costMap) {
            $cost = $costMap[$row->product_id] ?? null;
            $profit = $cost !== null ? ($row->total_omzet - ($cost * $row->total_qty)) : null;

            return (object) [
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'total_qty' => (int) $row->total_qty,
                'total_omzet' => (int) $row->total_omzet,
                'profit' => $profit,
            ];
        })->sortByDesc('total_qty')->values();

        $totalProfit = $report->whereNotNull('profit')->sum('profit');
        $totalOmzet = $report->sum('total_omzet');

        // Top 5 laris & top 5 sepi (dari produk yang masih aktif/ada, minimal pernah terjual)
        $terlaris = $report->sortByDesc('total_qty')->take(5)->values();
        $palingSepi = $report->sortBy('total_qty')->take(5)->values();

        // Produk yang belum pernah terjual sama sekali juga masuk kategori "paling ga diminati"
        $soldProductIds = $report->pluck('product_id')->filter()->all();
        $neverSold = Product::whereNotIn('id', $soldProductIds)->get(['id', 'name'])
            ->map(fn ($p) => (object) [
                'product_id' => $p->id,
                'product_name' => $p->name,
                'total_qty' => 0,
                'total_omzet' => 0,
                'profit' => 0,
            ]);

        $palingSepi = $palingSepi->concat($neverSold)->sortBy('total_qty')->take(5)->values();

        return view('admin.reports.index', compact('report', 'terlaris', 'palingSepi', 'totalProfit', 'totalOmzet'));
    }
}
