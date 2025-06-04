<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreCategoryRequest;
use App\Http\Requests\Backend\Update\UpdateCategoryRequest;
use App\Http\Resources\Backend\CategoriesResource;
use App\Http\Resources\Backend\Edit\EditCategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status',]);


        return Inertia::render('backend/categories/Index', [
            'filters' => $filters,
            'categories' => Inertia::defer(function () use ($filters) {
                $categoryQuery = Category::query();

                $this->applySearch($categoryQuery, $filters);

                $categories = CategoriesResource::collection($categoryQuery->withCount('memorials')->with('createdBy')->latest()->paginate(10)->appends($filters));
                return $categories;
            }),
        ]);
    }

    protected function applySearch($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        })->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('backend/categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();


        try {
            DB::beginTransaction();

            Category::create($validated);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with('message', [
                    'type' => 'success',
                    'body' => 'Category created successfully'
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
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category = new EditCategoryResource($category);
        return Inertia::render('backend/categories/Edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $category->update($validated);

            DB::commit();

            return redirect()->route('admin.categories.index')
                ->with(
                    'message',
                    ['type' => 'success', 'body' => 'Category updated successfully']
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
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $count = $category->delete();

        $message = [
            'type' => 'success',
            'body' => 'Category Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.categories.index')->with('message', $message);
    }
}
