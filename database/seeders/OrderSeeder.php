<?php

namespace Database\Seeders;

use App\Models\Memorial;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::withoutEvents(function () {


            $orders = Order::factory()->count(50)->create(
                [
                    'subtotal' => 0,
                    'total' => 0,
                ]
            )->each(function ($order) {
                $memorials = Memorial::inRandomOrder()->take(rand(1, 2))->get();

                foreach ($memorials as $memorial) {
                    $quantity = rand(1, 2);
                    $price = $memorial->price;
                    $discount = $memorial->sale_price ? ($memorial->price - $memorial->sale_price) : 0;
                    $total = ($price - $discount) * $quantity;

                    $order->memorials()->attach($memorial->id, [
                        'price' => $price,
                        'discount' => $discount,
                        'quantity' => $quantity,
                        'total' => $total,
                        'first_name' => fake()->firstName(),
                        'last_name' => fake()->lastName(),
                        'epitaph' => fake()->sentence(rand(5, 10)),
                        'DOB' => fake()->dateTimeBetween('-50 years', '-5 years'),
                        'DOD' => fake()->dateTimeBetween('-4 years', 'today'),
                        'instructions' => fake()->sentences(rand(2, 7), true),
                    ]);
                }

                $order->subtotal = $order->memorials->sum(function ($memorial) {
                    return $memorial->pivot->price * $memorial->pivot->quantity;
                });

                $order->discount = $order->memorials->sum(function ($memorial) {
                    return $memorial->pivot->discount * $memorial->pivot->quantity;
                });

                $order->total = $order->memorials->sum(function ($memorial) {
                    return $memorial->pivot->total;
                });

                $order->save();

                Transaction::create([
                    'customer_id' => $order->customer_id,
                    'amount' => $order->total,
                    'order_id' => $order->id,
                    'transaction_id' => Str::uuid(),
                    'reference_id' => Str::random(8),
                    'status' => 'success',
                    'created_at' => $order->created_at,
                ]);
            });
        });
    }
}
