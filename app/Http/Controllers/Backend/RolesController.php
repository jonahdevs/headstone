<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Update\UpdateRoleRequest;
use App\Http\Resources\Backend\Edit\EditRoleResource;
use App\Http\Resources\Backend\RolesResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search']);

        return Inertia::render('backend/roles/Index', [
            'roles' => Inertia::defer(function () use ($filters) {
                $query = Role::query();
                $this->applySearch($query, $filters);

                $roles = RolesResource::collection($query->withCount('permissions', 'users')->with([
                    'users' => function ($query) {
                        $query->take(4);
                    }
                ])->latest()->get());
                return $roles;
            }),
            'filters' => $filters,
        ]);
    }

    protected function applySearch($query, $filters)
    {
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }


    public function edit(Role $role)
    {
        $role->load('permissions');

        $role = new EditRoleResource($role);
        $groupedPermissions = Permission::get()->map(fn($permission) => explode(' ', $permission->name))
            ->groupBy(fn($parts) => $parts[1])
            ->map(fn($group) => $group->pluck(0)->values());

        return Inertia::render('backend/roles/Edit', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        try {
            if ($request->has('permissions')) {

                $role->syncPermissions($validated['permissions']);
            }

            return redirect()->route('admin.roles.index')->with('message', [
                'type' => 'success',
                'body' => 'Role updated successfully'
            ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }

    }
}
