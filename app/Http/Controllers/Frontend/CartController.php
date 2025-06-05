<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Store\StoreCartRequest;
use App\Models\CartItem;
use App\Models\Memorial;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // session()->flush();
        return Inertia::render("frontend/Cart", [
            'cart' => Inertia::defer(fn() => auth()->check() ? $this->getUserCartItems() : $this->getGuestCartItems($request)),
        ]);
    }

    protected function getGuestCartItems($request)
    {
        $cartItems = collect($request->session()->get('cartItems', []));
        // dd($cartItems);
        $items = $cartItems->map(function ($item) {
            $memorial = Memorial::select('id', 'title', 'price', 'sale_price', 'image', 'stock')->find($item->memorial_id);
            if (!$memorial) {
                return null; // Skip if memorial not found
            }

            $price = $memorial->sale_price ?? $memorial->price;
            $total = $price * $item->quantity;

            return (object) [
                'id' => $item->id,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'quantity' => $item->quantity,
                'DOD' => $item->DOD->toFormattedDateString(),
                'DOB' => $item->DOB->toFormattedDateString(),
                'epitaph' => $item->epitaph,
                'instructions' => $item->instructions,
                'image' => $item->image,
                'total' => $total,
                'memorial' => (object) [
                    'id' => $memorial->id,
                    'title' => $memorial->title,
                    'image' => $memorial->image,
                    'price' => $memorial->price,
                    'sale_price' => $memorial->sale_price,
                    'stock' => $memorial->stock,
                ]
            ];
        });

        $subtotal = $items->sum(fn($item) => $item->memorial->price * $item->quantity); // Full price
        $total = $items->sum(fn($item) => ($item->memorial->sale_price ?? $item->memorial->price) * $item->quantity);
        $discount = $subtotal - $total;

        $items->map(function ($item) {
            $item->total = Number::currency($item->total ?? 0, 'kes');
            $item->memorial->price = Number::currency($item->memorial->price, 'kes');
            $item->memorial->sale_price = $item->memorial->sale_price ? Number::currency($item->memorial->sale_price, 'kes') : null;
            return $item;
        });

        $cart = (object) [
            'items' => $items,
            'subtotal' => Number::currency($subtotal, 'kes'),
            'discount' => Number::currency($discount, 'kes'),
            'total' => Number::currency($total, 'kes'),
        ];

        return $cart;
    }

    protected function getUserCartItems()
    {
        $items = CartItem::where('customer_id', auth()->id())->with('memorial')->get();

        $items = $items->map(function ($item) {
            $price = $item->memorial->sale_price ?? $item->memorial->price;
            $total = $price * $item->quantity;

            return (object) [
                'id' => $item->id,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'quantity' => $item->quantity,
                'DOD' => Carbon::parse($item->DOD)->toFormattedDateString(),
                'DOB' => Carbon::parse($item->DOB)->toFormattedDateString(),
                'epitaph' => $item->epitaph,
                'instructions' => $item->instructions,
                'image' => $item->image,
                'total' => $total,
                'memorial' => (object) [
                    'id' => $item->memorial->id,
                    'title' => $item->memorial->title,
                    'image' => $item->memorial->image,
                    'price' => $item->memorial->price,
                    'sale_price' => $item->memorial->sale_price,
                    'stock' => $item->memorial->stock,
                ]
            ];
        });

        $subtotal = $items->sum(fn($item) => $item->memorial->price * $item->quantity);
        $total = $items->sum(fn($item) => ($item->memorial->sale_price ?? $item->memorial->price) * $item->quantity);
        $discount = $subtotal - $total;

        $items->map(function ($item) {
            $item->total = Number::currency($item->total, 'kes');
            $item->memorial->price = Number::currency($item->memorial->price, 'kes');
            $item->memorial->sale_price = $item->memorial->sale_price ? Number::currency($item->memorial->sale_price, 'kes') : null;
            return $item;
        });

        $cart = (object) [
            'items' => $items,
            'subtotal' => Number::currency($subtotal, 'kes'),
            'discount' => Number::currency($discount, 'kes'),
            'total' => Number::currency($total, 'kes'),
        ];
        return $cart;
    }

    public function store(StoreCartRequest $request)
    {
        $validated = $request->validated();
        $validated['quantity'] = 1;

        try {
            if ($request->hasFile('image')) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $path = $request->file('image')->storeAs('cart', uniqid() . '.' . $extension, 'public');
                $validated['image'] = $path;
            } else {
                $validated['image'] = null;
            }

            $validated['DOB'] = Carbon::parse($validated['DOB']);
            $validated['DOD'] = Carbon::parse($validated['DOD']);


            if (auth()->check()) {
                $existingCartItem = CartItem::where(function (Builder $query) use ($validated) {
                    $query->where('memorial_id', $validated['memorial_id'])
                        ->where('first_name', $validated['first_name'])
                        ->where('last_name', $validated['last_name'])
                        ->where('DOD', $validated['DOD'])
                        ->where('DOB', $validated['DOB']);
                })->exists();

                if ($existingCartItem) {
                    return back()->with('message', [
                        'type' => 'success',
                        'body' => 'Item already in the cart'
                    ]);
                }

                CartItem::create($validated);
            } else {
                $cartItems = collect(session()->get('cartItems', []));

                $existingCartItem = $cartItems->where('memorial_id', $validated['memorial_id'])
                    ->where('first_name', $validated['first_name'])
                    ->where('last_name', $validated['last_name'])
                    ->where('DOD', $validated['DOD'])
                    ->where('DOD', $validated['DOD'])
                    ->first();

                if (isset($existingCartItem)) {
                    return back()->with('message', [
                        'type' => 'success',
                        'body' => 'Item already in the cart'
                    ]);
                }

                $cartItems->push((object) [
                    'id' => (string) Str::uuid(),
                    'memorial_id' => $validated['memorial_id'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'DOB' => $validated['DOB'],
                    'DOD' => $validated['DOD'],
                    'font' => $validated['font'] ?? null,
                    'epitaph' => $validated['epitaph'] ?? null,
                    'instructions' => $validated['instructions'] ?? null,
                    'image' => $validated['image'],
                    'quantity' => 1,
                ]);

                $request->session()->put('cartItems', $cartItems);
            }

            return back()->with('message', [
                'type' => 'success',
                'body' => 'Item added to the cart'
            ]);

        } catch (\Throwable $th) {
            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    public function update(Request $request, $itemId)
    {
        $quantity = $request->input('quantity');

        if (auth()->check()) {
            CartItem::find($itemId)->update(['quantity' => $quantity]);
        } else {
            $cartItems = collect(session()->get('cartItems', []));
            $cartItems = $cartItems->map(function ($item) use ($itemId, $quantity) {
                if ($item->id == $itemId) {
                    $item->quantity = $quantity;
                }
                return $item;
            });

            $request->session()->put('cartItems', $cartItems);
        }

        return back();
    }

    public function destroy(Request $request, $itemId)
    {
        if (auth()->check()) {
            CartItem::find($itemId)->delete();
        } else {
            $cartItems = collect(session()->get('cartItems', []));

            // Use array access for session data
            $cartItems = $cartItems->filter(fn($item) => $item->id != $itemId);

            $request->session()->put('cartItems', $cartItems->values()); // reindex the array
        }

        return back();
    }
}
