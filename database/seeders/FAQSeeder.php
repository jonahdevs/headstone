<?php

namespace Database\Seeders;

use App\Models\FAQ;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What materials are your headstones made from?',
                'response' => 'Our headstone are primary made from high-quality granite and marble, known for their durability and timeless appearance. We also offer terrazzo and acrylic upon request.',
            ],
            [
                'question' => 'Can I customize the design of a headstone',
                'response' => 'Yes, absolutely. You can personalize the headstone with custom text, fonts, images, religious symbols, epitaphs, and even laser-etched portraits. Our team will provide a proof before production.',
            ],
            [
                'question' => 'How long does it take to complete an order?',
                'response' => 'Standard headstones typically take 2 to 4 weeks to complete. Custom or intricate designs may take longer, and we will communicate timelines clearly during the design approval process.',
            ],
            [
                'question' => 'Do you handle installation at the cemetery?',
                'response' => "Yes, we offer professional installation services in select locations. If you're outside our service area, we can coordinate with your local cemetery or provide instructions for your installer.",
            ],
            [
                'question' => 'What information do I need to provide to place an order?',
                'response' => 'You’ll need the full name of the deceased, birth and death dates, any epitaphs or messages, and the cemetery’s regulations (some have size or material restrictions).',
            ],
            [
                'question' => 'Are your headstones covered by a warranty?',
                'response' => 'Yes, all of our granite and marble headstones come with a lifetime guarantee against natural wear, cracking, or fading under normal conditions.',
            ],
            [
                'question' => 'Do you work with specific cemeteries or have their approval?',
                'response' => 'We regularly work with many cemeteries and are familiar with their requirements. Let us know where the stone will be placed, and we’ll ensure it meets their guidelines.',
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create([
                'question' => $faq['question'],
                'response' => $faq['response'],
                'created_by' => User::operators()->inRandomOrder()->first()->id,
                'status' => 'published'
            ]);
        }
    }
}
