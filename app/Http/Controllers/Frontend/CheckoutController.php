<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\Checkout\CheckoutResource;
use App\Models\CartItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $total = $user->cartItems->sum(fn($item) => ($item->memorial->sale_price ?? $item->memorial->price) * $item->quantity);

        $fields = [
            'email' => $user->email,
            'amount' => $total * 100,
            'callback_url' => route('checkout.callback'),
        ];

        try {
            $payment = $this->initializeTransaction($fields);

            if (!$payment || !$payment->status || !isset($payment->data->reference)) {
                throw new \Exception('Payment initialization failed');
            }

            DB::beginTransaction();

            $orderSubtotal = $user->cartItems->sum(fn(CartItem $item) => $item->memorial->price * $item->quantity);
            $orderDiscount = $user->cartItems->sum(fn(CartItem $item) =>
                $item->memorial->sale_price
                ? ($item->memorial->price - $item->memorial->sale_price) * $item->quantity : 0);

            $order = Order::create([
                'customer_id' => $user->id,
                'subtotal' => $orderSubtotal,
                'discount' => $orderDiscount,
                'total' => $total,
            ]);

            $order->payment()->create([
                'customer_id' => $user->id,
                'amount' => $total,
                'reference_id' => $payment->data->reference,
                'status' => 'pending',
            ]);

            $cartItems = $user->cartItems;
            foreach ($cartItems as $item) {
                $price = $item->memorial->price;
                $salePrice = $item->memorial->sale_price;
                $discount = $salePrice ? $price - $salePrice : 0;
                $finalPrice = $salePrice ?? $price;

                $quantity = $item->quantity;
                $total = $finalPrice * $quantity;

                $order->memorials()->attach($item->memorial_id, [
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'total' => $total,
                    'font' => $item->font,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name,
                    'epitaph' => $item->epitaph,
                    'DOB' => $item->DOB,
                    'DOD' => $item->DOD,
                    'instructions' => $item->instructions,
                    'image' => $item->image,
                ]);
            }

            $user->cartItems()->delete();
            DB::commit();

            return Inertia::location($payment->data->authorization_url);

        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return back()->with('message', [
                'type' => 'error',
                'body' => 'An error occurred while processing your request. Please try again later.',
            ]);
        }
    }

    protected function initializeTransaction($fields)
    {
        $url = "https://api.paystack.co/transaction/initialize";

        $fields_string = http_build_query($fields);

        // open a connection
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result);
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            throw new \Exception('Payment reference is required');
        }

        try {
            $transaction = $this->verifyTransaction($reference);

            if ($transaction['status'] && $transaction['data']['status'] === 'success') {
                $order = Order::whereHas('payment', function ($query) use ($reference) {
                    $query->where('reference_id', $reference);
                })->first();

                if ($order) {
                    $order->payment->update([
                        'status' => $transaction['data']['status'],
                        'transaction_id' => $transaction['data']['id'],
                        'amount' => $transaction['data']['amount'] / 100,
                        'paid_at' => Carbon::parse($transaction['data']['paid_at']),
                        'payment_method' => $transaction['data']['channel'],
                    ]);

                    $user = $order->customer;

                    return redirect()->route('checkout.success')->with('message', [
                        'type' => 'success',
                        'body' => 'Payment successful. Your order has been placed.',
                    ]);
                }

            }

            return redirect()->route('checkout.error')->with('message', [
                'type' => 'error',
                'body' => 'Payment verification failed. Please try again later.',
            ]);

        } catch (\Throwable $th) {
            return redirect()->route('checkout.error')->with('message', [
                'type' => 'error',
                'body' => 'An error occurred while verifying your payment. Please try again later.',
            ]);
        }
    }

    public function verifyTransaction(string $reference)
    {
        $url = "https://api.paystack.co/transaction/verify/{$reference}";

        // open a connection
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return json_decode($response, true);
        }
    }
    public function checkoutSuccess()
    {
        $user = auth()->user();
        $customer = new CheckoutResource($user->load([
            'latestOrder' => [
                'payment',
                'memorials.materials'
            ]
        ]));

        // dd($customer);
        return Inertia::render('frontend/checkout/Success', compact('customer'));
    }
    public function checkoutError()
    {
        return Inertia::render('frontend/checkout/Error');
    }
}
