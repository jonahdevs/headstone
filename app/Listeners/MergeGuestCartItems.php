<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\CartItem;
use App\Models\Memorial;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MergeGuestCartItems
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;

        if (!$user->hasRole('customer')) {
            return;
        }

        $cartItems = collect(session()->get('cartItems', []));

        foreach ($cartItems as $item) {
            $exisitingItem = CartItem::where('customer_id', $user->id)
                ->where('first_name', $item->first_name)
                ->where('last_name', $item->last_name)
                ->where('DOB', $item->DOB)
                ->where('DOD', $item->DOD)
                ->first();

            if ($exisitingItem) {
                return;
            }

            $memorial_id = Memorial::where('id', $item->memorial_id)->first()->id;
            $user->cartItems()->create([
                'memorial_id' => $memorial_id,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'DOB' => $item->DOB,
                'DOD' => $item->DOD,
                'epitaph' => $item->epitaph,
                'instructions' => $item->instructions,
                'image' => $item->image,
                'font' => $item->font,
                'quantity' => $item->quantity
            ]);
        }

        session()->forget('cartItems');
    }
}
