<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreUserRequest;
use App\Http\Requests\Backend\Update\UpdateUserRequest;
use App\Http\Resources\Backend\Edit\EditUserResource;
use App\Http\Resources\Backend\UsersResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search', 'role']);

        return Inertia::render('backend/users/Index', [
            'users' => Inertia::defer(function () use ($filters) {
                $query = User::query();

                $this->applySearch($query, $filters);
                $users = UsersResource::collection($query->operators()->with('roles')->latest()->paginate(10)->appends($filters));
                return $users;
            }),
            'filters' => $filters,
            'roles' => Inertia::defer(fn() => Role::whereNot('name', 'customer')->pluck('name'))
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->whereNot('email', auth()->user()->email)
            ->when($filters['search'] ?? null, function (Builder $query, $search) {
                $query->where(
                    fn($query) =>
                    $query->where('name', 'like', "%{$search}%")
                        ->where('email', 'like', "%{$search}%")
                        ->where('id', $search)
                );
            })->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
            ->when($filters['role'] ?? null, function (Builder $query, $role) {
                $query->whereHas('roles', function (Builder $query) use ($role) {
                    $query->where('name', $role);
                });
            });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('backend/users/Create', [
            'roles' => Inertia::defer(fn() => Role::whereNot('name', 'customer')->select('id', 'name')->get()),
            'permissions' => Inertia::defer(fn() => Permission::select('id', 'name')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->storeAs('avatars', uniqid() . '.' . '.jpg', 'public');
                $validated['avatar'] = $path;
            } else {
                $validated['avatar'] = null;
            }

            $validated['password'] = bcrypt(Str::password(8));
            $user = User::create($validated);

            if ($request->has('roles')) {
                $rolesNames = collect($validated['roles'])->pluck('name')->toArray();
                $user->syncRoles($rolesNames);
            }

            if ($request->has('permissions')) {
                $permissionNames = collect($validated['permissions'])->pluck('name')->toArray();
                $user->syncPermissions($permissionNames);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('message', [
                'type' => 'success',
                'body' => 'User created successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

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
    public function edit(User $user)
    {
        $user->load('permissions', 'roles');
        $user = new EditUserResource($user);


        return Inertia::render('backend/users/Edit', [
            'user' => $user,
            'roles' => Inertia::defer(fn() => Role::whereNot('name', 'customer')->select('id', 'name')->get()),
            'permissions' => Inertia::defer(fn() => Permission::select('id', 'name')->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $validated = $request->validated();

        try {
            DB::beginTransaction();
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->storeAs('avatars', uniqid() . '.' . '.jpg', 'public');
                $validated['avatar'] = $path;
            } else {
                unset($validated['avatar']);
            }

            $user->update($validated);

            if ($request->has('roles')) {
                $rolesNames = collect($validated['roles'])->pluck('name')->toArray();

                $user->syncRoles($rolesNames);
            }

            if ($request->has('permissions')) {
                $permissionNames = collect($validated['permissions'])->pluck('name')->toArray();

                $user->syncPermissions($permissionNames);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('message', [
                'type' => 'success',
                'body' => 'User updated successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $count = $user->delete();

        $message = [
            'type' => 'success',
            'body' => 'User Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.users.index')->with('message', $message);
    }
}
