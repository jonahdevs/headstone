<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Update\UpdateQuotationRequest;
use App\Http\Resources\Backend\QuotationsResource;
use App\Http\Resources\Backend\Show\ShowQuotationResource;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'urgency']);

        return Inertia::render(
            'backend/quotations/Index',
            [
                'filters' => $filters,
                'quotations' => Inertia::defer(function () use ($filters) {
                    $quotationsQuery = Quotation::query();

                    $this->applySearch($quotationsQuery, $filters);
                    $quotations = QuotationsResource::collection($quotationsQuery->with('customer', 'memorial', 'material')
                        ->latest()->paginate(10)->appends($filters));
                    return $quotations;
                }),
            ]
        );
    }

    protected function applySearch($query, $filters)
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('memorial', fn($q) => $q->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('material', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        })
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status));
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
    public function show(Quotation $quotation)
    {
        return Inertia::render('backend/quotations/Details', [
            'quotation' => new ShowQuotationResource($quotation->load('customer', 'memorial', 'material')),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $validated = $request->validated();

        try {
            $existingResponse = $quotation->response_data ?? [];


            if (!is_array($existingResponse)) {
                $existingResponse = [$existingResponse];
            }

            $newResponse = [
                'quoted_price' => $validated['quoted_price'],
                'message' => $validated['message'],
                'valid_until' => Carbon::parse($validated['valid_until']),
                'response_by' => auth()->user()->only(['id', 'name', 'email']),
                'response_at' => now(),
            ];

            $updatedResponse = [...$existingResponse, $newResponse];

            $quotation->response_data = $updatedResponse;
            $quotation->save();

            return back()
                ->with('message', [
                    'type' => 'success',
                    'body' => __('Quotation updated successfully.'),
                ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'error',
                'body' => __('Failed to update quotation. Please try again.'),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        $count = $quotation->delete();

        $message = [
            'type' => 'success',
            'body' => 'Quotation Deleted successfully',
        ];

        $message = $count < 1 ?: [
            'type' => 'error',
            'body' => 'Something went wrong',
        ];

        return redirect()->route('admin.quotations.index')->with('message', $message);
    }
}
