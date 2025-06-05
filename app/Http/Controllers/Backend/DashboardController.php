<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\OrdersResource;
use App\Models\Memorial;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);

        $startDate = !empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : null;
        $endDate = !empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : null;

        return Inertia::render('backend/Dashboard', [
            'filters' => $filters,
            'totalSales' => Inertia::defer(fn() => 'KES ' . Number::abbreviate(
                Transaction::when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                    ->sum('amount')
            )),

            'totalOrders' => Inertia::defer(fn() =>
                Order::when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count()),
            'totalUsers' => Inertia::defer(fn() => User::when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count()),
            'totalQuotations' => Inertia::defer(fn() => Quotation::when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count()),
            'orders' => Inertia::defer(fn() => OrdersResource::collection(Order::with('customer', 'payment')->withCount('memorials')->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->latest()->take(10)->get())),
            'topSellingMemorials' => Inertia::defer(
                fn() => (object) 
                Memorial::withCount([
                    'orders as orders_count' => function ($query) use ($startDate, $endDate) {
                        $query->when(
                            $startDate && $endDate,
                            fn($q) =>
                            $q->whereBetween('memorial_order.created_at', [$startDate, $endDate])
                        );
                    }
                ])
                    ->orderByDesc('orders_count')
                    ->take(5)
                    ->get()
                    ->map(fn($p) => ['label' => $p->title, 'value' => $p->orders_count])
            ),

            'salesTrend' => Inertia::defer(function () {
                $years = Transaction::selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->limit(2)
                    ->pluck('year')
                    ->sort()
                    ->values();

                $months = collect(range(1, 12))->mapWithKeys(fn($m) => [Carbon::create()->month($m)->format('F') => 0]);

                return Transaction::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                    ->whereIn(DB::raw('YEAR(created_at)'), $years)
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->groupBy('year')
                    ->map(function ($items) use ($months) {
                        $filled = clone $months;
                        foreach ($items as $item) {
                            $monthName = Carbon::create()->month($item->month)->format('F');
                            $filled[$monthName] = (float) $item->total;
                        }
                        return $filled;
                    });
            }),
        ]);
    }
}
