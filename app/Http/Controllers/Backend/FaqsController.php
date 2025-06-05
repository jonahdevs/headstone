<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Store\StoreFaqRequest;
use App\Http\Requests\Backend\Update\UpdateFaqRequest;
use App\Http\Resources\Backend\FaqsResource;
use App\Models\FAQ;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);


        return Inertia::render('backend/faqs/Index', [
            'faqs' => Inertia::defer(function () use ($filters) {
                $faqsQuery = FAQ::query();
                $this->applySearch($faqsQuery, $filters);

                $faqs = FaqsResource::collection($faqsQuery->with('createdBy')->latest()->paginate(10)->appends($filters));
                return $faqs;
            }),
            'filters' => $filters,
        ]);
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('id', $search)
                    ->orwhere('response', 'like', "%{$search}%")
                    ->orwhere('question', 'like', "%{$search}%")
                    ->orwhereHas('createdBy', fn(Builder $query) => $query->where('email', 'like', "%{$search}%"));
            });
        })
            ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('backend/faqs/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFaqRequest $request)
    {
        $validated = $request->validated();
        try {
            FAQ::create($validated);

            return redirect()->route('admin.faqs.index')->with('message', [
                'type' => 'success',
                'body' => 'FAQ updated successfully',
            ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'success',
                'body' => 'FAQ updated successfully',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FAQ $fAQ)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FAQ $faq)
    {
        return Inertia::render('backend/faqs/Edit', [
            'faq' => $faq
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFaqRequest $request, FAQ $faq)
    {
        $validated = $request->validated();

        try {
            $faq->update($validated);

            return redirect()->route('admin.faqs.index')->with('message', [
                'type' => 'success',
                'body' => 'FAQ updated successfully',
            ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'success',
                'body' => 'FAQ updated successfully',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FAQ $faq)
    {
        $count = $faq->delete();

        $message = [
            'type' => 'success',
            'body' => 'FAQ Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.faqs.index')->with('message', $message);
    }
}
