<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Update\UpdateCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\Backend\CustomersResource;
use App\Http\Resources\Backend\Edit\EditCustomerResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CustomersController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny, App\Models\User', ['index']),
            new Middleware('can:create, App\Models\User', ['create', 'store']),
            new Middleware('can:update, App\Models\User', ['edit', 'update']),
            new Middleware('can:update, App\Models\User', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);

        return Inertia::render('backend/customers/Index', [
            'customers' => Inertia::defer(function () use ($filters) {
                $query = User::query();

                $this->applySearch($query, $filters);
                $customers = CustomersResource::collection($query->customers()->latest()->paginate(10)->appends($filters));
                return $customers;
            }),
            'filters' => $filters,
        ]);
    }


    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(
                fn($query) =>
                $query->where('name', 'like', "%{$search}%")
                    ->where('email', 'like', "%{$search}%")
                    ->where('id', $search)
            );
        })->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status));

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia('backend/customers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $validated['password'] = bcrypt(Str::password(8));

            if ($request->hasFile('avatar')) {
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $path = $request->file('avatar')->storeAs('avatars', uniqid() . $extension, 'public');
                $validated['avatar'] = $path;
            } else {
                $validated['avatar'] = null;
            }

            $customer = User::create($validated);
            $customer->assignRole('customer');

            DB::commit();

            return redirect()->route('admin.customers.index')->with('message', ['type' => 'success', 'body' => 'Customer created successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $customer)
    {
        $customer = new EditCustomerResource($customer);
        return Inertia::render('backend/customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, User $customer)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->hasFile('avatar')) {
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $path = $request->file('avatar')->storeAs('avatars', uniqid() . $extension, 'public');
                $validated['avatar'] = $path;
            } else {
                unset($validated['avatar']);
            }

            $customer = $customer->update($validated);
            DB::commit();

            return redirect()->route('admin.customers.index')->with('message', ['type' => 'success', 'body' => 'Customer updated successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('messages', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $customer)
    {
        $count = $customer->delete();

        $message = [
            'type' => 'success',
            'body' => 'Customer Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.customers.index')->with('message', $message);
    }
}
