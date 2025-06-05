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
        $notifications = auth()->user()->notifications()->paginate(10);

        return Inertia::render('backend/notifications/Index', [
            'notifications' => inertia()->merge(fn() => $notifications->items()),
            'notification_paginations' => $notifications->toArray(),
        ]);
    }

    public function unread()
    {
        return response()->json(NotificationsResource::collection(auth()->user()->unreadNotifications));
    }


    public function markAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
