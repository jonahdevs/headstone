<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Store\StoreContactRequest;
use App\Jobs\ProcessContactData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('frontend/Contact');
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        ProcessContactData::dispatch($validated);

        return redirect()->route('contact');
    }
}
