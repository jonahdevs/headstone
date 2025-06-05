<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\MemorialsResource;
use App\Http\Resources\Frontend\ReviewsResource;
use App\Models\Memorial;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render("frontend/Home", [
            'memorials' => Inertia::defer(function () {
                $memorials = MemorialsResource::collection(Memorial::published()->take(4)->get());
                return $memorials;
            }),
            'testimonies' => Inertia::defer(function () {
                $testimonies = ReviewsResource::collection(Review::with('customer')->published()->get());
                return $testimonies;
            }),
        ]);
    }
}
