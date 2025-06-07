<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\NotificationsResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    public function index()
    {
        return Inertia::render('backend/notifications/Index', [
            'notifications' => inertia()->defer(fn() => NotificationsResource::collection(auth()->user()->notifications()->paginate(10))),
        ]);
    }

    public function unread()
    {
        return response()->json(NotificationsResource::collection(auth()->user()->unreadNotifications));
    }


    public function markAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return;
    }
}
