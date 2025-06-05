<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\OrdersResource;
use App\Http\Resources\Backend\Show\ShowOrderResource;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'payment_status']);

        return Inertia::render('backend/orders/Index', [
            'orders' => Inertia::defer(function () use ($filters) {
                $ordersQuery = Order::query();
                $this->applySearch($ordersQuery, $filters);

                $orders = OrdersResource::collection($ordersQuery->withCount('memorials')->with('payment', 'customer')->latest()->paginate(10)->appends($filters));

                return $orders;
            }),
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('id', $search)
                    ->orWhereHas('customer', fn(Builder $query) => $query->where('email', 'LIKE', "%{$search}%"))
                    ->orWhereHas('order', fn(Builder $query) => $query->where('id', $search));
            });
        })
            ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, function (Builder $query, $status) {
                $query->whereHas('payment', fn(Builder $query) => $query->where('status', $status));
            });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return Inertia::render('backend/orders/Details', [
            'order' => new ShowOrderResource($order->load(['memorials', 'customer', 'payment'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        try {
            $order->status = $validated['status'];
            $order->save();

            return back()->with('message', [
                'type' => 'success',
                'body' => 'Updated successfully'
            ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $count = $order->delete();

        $message = [
            'type' => 'success',
            'body' => 'Order Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.orders.index')->with('message', $message);
    }
}
