<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('App.Models.Order.{id}', function ($user, $id) {
    $order = Order::findOrFail($id);
    return (int) $user->id === (int) $order->customer_id;
});

Broadcast::channel('admins', fn(User $user) => $user->hasRole('admin'));
