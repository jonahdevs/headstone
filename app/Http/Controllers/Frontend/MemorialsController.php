<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\MemorialsResource;
use App\Http\Resources\Frontend\ReviewsResource;
use App\Http\Resources\Frontend\Show\ShowMemorialResource;
use App\Models\Category;
use App\Models\Memorial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemorialsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['style', 'min_price', 'max_price']);

        return Inertia::render('frontend/memorials/Index', [
            'memorials' => inertia::defer(function () use ($filters) {
                $memorialsQuery = Memorial::query();
                $this->applySearch($memorialsQuery, $filters);
                $memorials = MemorialsResource::collection($memorialsQuery->published()->latest()->paginate(12));
                return $memorials;
            }),
            'categories' => Inertia::defer(fn() => $categories = Category::pluck('name')),
            'filters' => $filters
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['style'] ?? null, function (Builder $query, $style) {
            $query->whereHas('category', function (Builder $query) use ($style) {
                $query->where('name', $style);
            })->when();
        })->when(isset($filters['min_price']) && isset($filters['max_price']), function (Builder $query) use ($filters) {
            $query->whereBetween('price', [
                $filters['min_price'],
                $filters['max_price']
            ]);
        });
    }

    public function byCategory(string $category)
    {
        $category = Category::where('name', $category)->first();
        return Inertia::render('frontend/memorials/Index', [
            'memorials' => Inertia::defer(function () use ($category) {

                $memorials = MemorialsResource::collection($category->memorials()->latest()->paginate(10));
                return $memorials;
            }),
            'category' => Inertia::defer(fn() => $category->only('id', 'name')),
            'categories' => Inertia::defer(fn() => Category::pluck('name')),
        ]);
    }

    public function show(Memorial $memorial)
    {
        $memorial->load('images', 'tags', 'reviews.customer');

        return Inertia::render('frontend/memorials/Details', [
            'memorial' => fn() => new ShowMemorialResource($memorial),
            'testimonies' => Inertia::defer(fn() => ReviewsResource::collection($memorial->reviews)),

        ]);
    }
}
