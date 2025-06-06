<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\OrdersResource;
use App\Http\Resources\Customer\Show\ShowOrderResource;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersController extends Controller
{
    public function index(Request $request)
    {

        $user = $request->user();
        $filters = $request->only(['search', 'payment_status', 'status']);

        $ordersQuery = $user->orders()->with('memorials')->latest();

        $this->applySearch($ordersQuery, $filters);
        $orders = OrdersResource::collection($ordersQuery->paginate(5));

        return Inertia::render('customer/orders/Index', compact('orders', 'filters'));
    }

    protected function applySearch($query, $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn(Builder $query, $status) => $query->where('id', 'like', "%{$status}%"))
            ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, function (Builder $query, $status) {
                $query->whereHas('payment', fn(Builder $query) => $query->where('status', $status));
            });
    }

    public function show(Order $order)
    {
        $order->load('memorials', 'customer', 'payment')->loadCount('memorials');

        $order = new ShowOrderResource($order);
        return Inertia::render('customer/orders/Details', compact('order'));
    }
}
