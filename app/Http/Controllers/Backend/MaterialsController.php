<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreMaterialRequest;
use App\Http\Requests\Backend\Update\UpdateMaterialRequest;
use App\Http\Resources\Backend\Edit\EditMaterialResource;
use App\Http\Resources\Backend\MaterialsResource;
use App\Models\Material;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MaterialsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Material', ['index']),
            new Middleware('can:create,App\Models\Material', ['create', 'store']),
            new Middleware('can:update,App\Models\Material', ['edit', 'update']),
            new Middleware('can:delete,App\Models\Material', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status',]);

        return Inertia::render('backend/materials/Index', [
            'filters' => $filters,
            'materials' => Inertia::defer(function () use ($filters) {
                $materialsQuery = Material::query();

                $this->applySearch($materialsQuery, $filters);

                $materials = MaterialsResource::collection($materialsQuery->withCount('memorials')->with('createdBy')->latest()->paginate(10)->appends($filters));
                return $materials;
            }),
        ]);
    }

    protected function applySearch($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('backend/materials/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaterialRequest $request)
    {
        $validated = $request->validated();


        try {
            DB::beginTransaction();

            Material::create($validated);

            DB::commit();

            return redirect()->route('admin.materials.index')
                ->with(
                    'message',
                    ['type' => 'success', 'body' => 'Material created successfully']
                );
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
    public function show(Material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $material = new EditMaterialResource($material);
        return Inertia::render('backend/materials/Edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();

            // dd($validated);
            $material->update($validated);
            DB::commit();

            return redirect()->route('admin.materials.index')
                ->with(
                    'message',
                    ['type' => 'success', 'body' => 'Material updated successfully']
                );
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $count = $material->delete();

        $message = [
            'type' => 'success',
            'body' => 'Material Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.materials.index')->with('message', $message);
    }
}
