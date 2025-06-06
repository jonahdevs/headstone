<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Store\StoreQuotationRequest;
use App\Models\Material;
use App\Models\Memorial;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuotationController extends Controller
{
    public function index()
    {
        return Inertia::render('frontend/Quotation', [
            'memorials' => Inertia::defer(fn() => Memorial::select('id', 'title')->get()),
            'materials' => Inertia::defer(fn() => Material::select('id', 'name')->get()),
        ]);
    }

    public function store(StoreQuotationRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            if ($request->hasFile('reference_image')) {
                $extension = $request->file('reference_image')->getClientOriginalExtension();
                $path = $request->file('reference_image')->storeAs('quotations', uniqid() . '.' . $extension, 'public');
                $validated['reference_image'] = $path;
            } else {
                $validated['reference_image'] = null;
            }

            $validated['memorial_id'] = $request->input('memorial');
            $validated['material_id'] = $request->input('material');
            unset($validated['memorial'], $validated['material']);
            $validated['deadline'] = Carbon::parse($validated['deadline']);

            if (auth()->check()) {
                $validated['customer_id'] = auth()->id();
            }

            Quotation::create($validated);

            DB::commit();

            return back()->with('message', [
                'type' => 'success',
                'body' => 'Thank you for contacting us, we will get back to you as soon as possible',
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'success',
                'body' => 'Something went wrong',
            ]);
        }
    }
}
