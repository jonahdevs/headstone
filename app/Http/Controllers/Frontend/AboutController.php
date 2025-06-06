<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ReviewsResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AboutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('frontend/About', [
            'testimonies' => Inertia::defer(function () {
                $testimonies = ReviewsResource::collection(Review::with('customer')->published()->get());
                return $testimonies;
            }),
        ]);
    }
}
