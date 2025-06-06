<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\FaqsResource;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('frontend/Faqs', [
            'faqs' => Inertia::defer(fn() => FaqsResource::collection(FAQ::published()->get())),
        ]);
    }
}
