<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\ReviewsResource;
use App\Http\Resources\Backend\Show\ShowReviewResource;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class ReviewsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Review', ['index']),
            new Middleware('can:view,App\Models\Review', ['show']),
            new Middleware('can:update,App\Models\Review', ['update']),
            new Middleware('can:delete,quotation', ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        return Inertia::render('backend/reviews/Index', [
            'reviews' => Inertia::defer(function () use ($filters) {
                $reviewsQuery = Review::query();
                $this->applySearch($reviewsQuery, $filters);
                $reviews = ReviewsResource::collection($reviewsQuery->with('customer', 'memorial')
                    ->latest()->paginate(10)->appends($filters));
                return $reviews;
            }),
            'filters' => $filters,
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhereHas('customer', fn(Builder $query) => $query->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('memorial', fn(Builder $query) => $query->where('title', 'LIKE', "%{$search}%"));
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
    public function show(Review $review)
    {
        $review->load('customer', 'memorial');
        return Inertia::render('backend/reviews/Details', [
            'review' => new ShowReviewResource($review),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        try {
            $review->status = $validated['status'];

            $review->save();

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
    public function destroy(Review $review)
    {
        $count = $review->delete();

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
