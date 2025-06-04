<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\PermissionsResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $filters = $request->only(['search']);

        return Inertia::render('backend/permissions/Index', [
            'permissions' => Inertia::defer(function () use ($filters) {
                $permissionsQuery = Permission::query();
                $this->applySearch($permissionsQuery, $filters);

                $permissions = PermissionsResource::collection($permissionsQuery->with('roles:id,name')->latest()->paginate(10)->appends($filters));
                return $permissions;
            }),
            'filters' => $filters,
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }
}
