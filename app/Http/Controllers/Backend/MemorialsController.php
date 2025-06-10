<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreMemorialRequest;
use App\Http\Requests\Backend\Update\UpdateMemorialRequest;
use App\Http\Resources\Backend\Edit\EditMemorialResource;
use App\Http\Resources\Backend\MemorialsResource;
use App\Models\Category;
use App\Models\Material;
use App\Models\Memorial;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MemorialsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Memorial', ['index']),
            new Middleware('can:create,App\Models\Memorial', ['create', 'store']),
            new Middleware('can:update,App\Models\Memorial', ['edit', 'update']),
            new Middleware('can:delete,App\Models\Memorial', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        return Inertia::render('backend/memorials/Index', [
            'memorials' => Inertia::defer(function () use ($filters) {
                $memorialsQuery = Memorial::query();
                $this->applySearch($memorialsQuery, $filters);

                $memorials = MemorialsResource::collection($memorialsQuery->latest()->paginate(10)->appends($filters));
                return $memorials;
            }),
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where("title", 'like', "%{$search}%")
                ->orWhere("sku", 'like', "%{$search}%");
        })
            ->when($filters["status"] ?? null, fn(Builder $query, $status) => $query->where("status", $status));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('backend/memorials/Create', [
            'categories' => Inertia::defer(fn() => Category::pluck('name', 'id')),
            'tags' => Inertia::defer(fn() => Tag::select('name', 'id')->get()),
            'materials' => Inertia::defer(fn() => Material::select('name', 'id')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemorialRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->hasFile('main_image')) {
                $file = $request->file('main_image');
                $extension = $file->getClientOriginalExtension();

                $path = $file->storeAs('memorials', uniqid() . '.' . $extension, 'public');
                $validated['image'] = $path;
            } else {
                unset($validated['image']);
            }
            $memorial = Memorial::create($validated);

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $extension = $image->getClientOriginalExtension();
                    $path = $image->storeAs('memorials/gallery', uniqid() . '.' . $extension, 'public');
                    $memorial->images()->create([
                        'path' => $path
                    ]);
                }
            }

            if ($request->has('materials')) {
                $materialIds = collect($validated['materials'])->pluck('id')->toArray();

                $memorial->materials()->attach($materialIds);
            }

            if ($request->has('tags')) {
                $materialIds = collect($validated['tags'])->pluck('id')->toArray();

                $memorial->tags()->attach($materialIds);
            }

            if ($request->has('category')) {
                $category = Category::where('name', $request->category)->first();

                if (isset($category)) {
                    $memorial->category()->associate($category);
                    $memorial->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.memorials.index')->with('message', [
                'type' => 'success',
                'body' => 'Memorial Created successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Memorial $memorial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Memorial $memorial)
    {
        $memorial->load(['category', 'materials', 'tags', 'images']);
        $memorial = new EditMemorialResource($memorial);

        return Inertia::render('backend/memorials/Edit', [
            'memorial' => $memorial,
            'categories' => Inertia::defer(fn() => Category::pluck('name', 'id')),
            'tags' => Inertia::defer(fn() => Tag::select('name', 'id')->get()),
            'materials' => Inertia::defer(fn() => Material::select('name', 'id')->get()),
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemorialRequest $request, Memorial $memorial)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->hasFile('main_image')) {
                $file = $request->file('main_image');
                $extension = $file->getClientOriginalExtension();

                $path = $file->storeAs('memorials', uniqid() . '.' . $extension, 'public');
                $validated['image'] = $path;
            } else {
                unset($validated['main_image']);
            }

            $memorial->update($validated);


            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $extension = $image->getClientOriginalExtension();
                    $path = $image->storeAs('memorials/gallery', uniqid() . '.' . $extension, 'public');
                    $memorial->images()->create([
                        'path' => $path
                    ]);
                }
            }

            if ($request->has('materials')) {
                $materialIds = collect($validated['materials'])->pluck('id')->toArray();

                $memorial->materials()->sync($materialIds);
            }

            if ($request->has('tags')) {
                $materialIds = collect($validated['tags'])->pluck('id')->toArray();

                $memorial->tags()->sync($materialIds);
            }

            if ($request->has('category')) {
                $category = Category::where('name', $request->category)->first();

                if (isset($category)) {
                    $memorial->category()->associate($category);
                }
            }


            DB::commit();
            return redirect()->route('admin.memorials.index')->with('message', [
                'type' => 'success',
                'body' => 'Memorial Updated successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Memorial $memorial)
    {
        $count = $memorial->delete();

        $message = [
            'type' => 'success',
            'body' => 'Memorial Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.memorials.index')->with('message', $message);
    }
}
