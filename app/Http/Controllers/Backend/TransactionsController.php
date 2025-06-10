<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\Show\ShowTransactionResource;
use App\Http\Resources\Backend\TransactionsResource;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class TransactionsController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Transaction', ['index']),
            new Middleware('can:view,transaction', ['show']),
            new Middleware('can:delete,App\Models\Transaction', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        return Inertia::render('backend/transactions/Index', [
            'filters' => $filters,
            'transactions' => Inertia::defer(function () use ($filters) {
                $transactionsQuery = Transaction::query();
                $this->applySearch($transactionsQuery, $filters);

                $transactions = TransactionsResource::collection($transactionsQuery->with('customer')->latest()->paginate(10)->appends($filters));
                return $transactions;
            }),
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn(Builder $query) => $query->where('email', 'like', "%{$search}%"));
            });
        })
            ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status));
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
    public function show(Transaction $transaction)
    {
        return Inertia::render('backend/transactions/Details', [
            'transaction' => new ShowTransactionResource($transaction->load(['customer', 'order.memorials'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $count = $transaction->delete();

        $message = [
            'type' => 'success',
            'body' => 'Transaction Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.transactions.index')->with('message', $message);
    }
}
