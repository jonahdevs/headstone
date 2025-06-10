<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreTagRequest;
use App\Http\Requests\Backend\Update\UpdateTagRequest;
use App\Http\Resources\Backend\Edit\EditTagResource;
use App\Http\Resources\Backend\TagsResource;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TagsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Tag', ['index']),
            new Middleware('can:create,App\Models\Tag', ['create', 'store']),
            new Middleware('can:update,App\Models\Tag', ['edit', 'update']),
            new Middleware('can:delete,App\Models\Tag', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status',]);

        return Inertia::render('backend/tags/Index', [
            'filters' => $filters,
            'tags' => Inertia::defer(function () use ($filters) {
                $tagsQuery = Tag::query();

                $this->applySearch($tagsQuery, $filters);

                $tags = TagsResource::collection($tagsQuery->withCount('memorials')->with('createdBy')->latest()->paginate(10)->appends($filters));
                return $tags;
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
        return Inertia::render('backend/tags/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            Tag::create($validated);

            DB::commit();

            return redirect()->route('admin.tags.index')
                ->with(
                    'message',
                    ['type' => 'success', 'body' => 'Tag created successfully']
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
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $tag = new EditTagResource($tag);
        return Inertia::render('backend/tags/Edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $tag->update($validated);

            DB::commit();

            return redirect()->route('admin.tags.index')
                ->with(
                    'message',
                    ['type' => 'success', 'body' => 'tag updated successfully']
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
    public function destroy(Tag $tag)
    {
        $count = $tag->delete();

        $message = [
            'type' => 'success',
            'body' => 'Tag Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.tags.index')->with('message', $message);
    }
}
