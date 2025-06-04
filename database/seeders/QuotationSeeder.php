<?php

namespace Database\Seeders;

use App\Models\Quotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotations = [
            [
                'reference_image' => 'headstone1.jpg',
                'note' => 'I\'d like a white marble finish.',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'website',
            ],
            [
                'reference_image' => 'example2.jpg',
                'note' => 'Need this urgently for burial',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'website',
            ],
            [
                'reference_image' => null,
                'note' => 'Please include engraving',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'whatsapp',
            ],
            [
                'reference_image' => 'sketch.jpg',
                'note' => 'Do you deliver outside Nairobi?',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'website',
            ],
            [
                'reference_image' => null,
                'note' => 'Can it be ready in a week?',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'walk-in',
            ],
            [
                'reference_image' => 'simple.png',
                'note' => 'Just something simple',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'website',
            ],
            [
                'reference_image' => 'ref7.jpg',
                'note' => 'Include transport fee',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'email',
            ],
            [
                'reference_image' => null,
                'note' => 'Can I pay in installments?',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'whatsapp',
            ],
            [
                'reference_image' => 'gold_look.jpg',
                'note' => 'Want a golden finish if possible',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'website',
            ],
            [
                'reference_image' => 'concept.jpg',
                'note' => 'Will send final design tomorrow',
                'deadline' => fake()->dateTimeBetween('1 week', '1 month'),
                'source' => 'walk-in',
            ],
        ];

        foreach ($quotations as $quotation) {
            Model::withoutEvents(function () use ($quotation) {
                Quotation::factory()->create($quotation);
            });
        }
    }
}
