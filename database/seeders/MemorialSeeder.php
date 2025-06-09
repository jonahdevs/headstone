<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\Memorial;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MemorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Granite',
                'description' => 'A durable and elegant stone, granite is a popular choice for headstones due to its resistance to weathering and its ability to hold intricate engravings.',
            ],
            [
                'name' => 'Marble',
                'description' => 'Known for its beauty and variety of colors, marble is often used for headstones and memorials. It is softer than granite but can be polished to a high sheen.',
            ],
            [
                'name' => 'Terrazzo',
                'description' => 'A composite material made from chips of marble, quartz, granite, or glass poured with a cement binder. It is often used for memorials and can be polished to a high gloss.',
            ],
            [
                'name' => 'Acrylic',
                'description' => 'A lightweight and versatile material, acrylic can be used for plaques and markers. It is available in various colors and can be engraved or printed with designs.',
            ]
        ];

        $headstoneTags = [
            // Material
            "Granite",
            "Marble",
            "Bronze",
            "Sandstone",
            "Limestone",
            "Slate",
            "Concrete",

            // Color
            "Black",
            "Gray",
            "White",
            "Red",
            "Blue Pearl",
            "Rose",
            "Green",

            // Design Style
            "Traditional",
            "Modern",
            "Rustic",
            "Minimalist",
            "Ornate",
            "Contemporary",
            "Vintage",

            // Religious/Cultural
            "Christian",
            "Catholic",
            "Muslim",
            "Jewish",
            "Buddhist",
            "Hindu",
            "Secular",
            "African Traditional",

            // Shape/Form
            "Upright",
            "Flat",

            // Finish
            "Polished",
            "Honed",
            "Rock Pitch",
            "Steeled",
            "Carved"
        ];

        $memorials = [
            [
                'title' => 'Flat Headstone',
                'description' => 'A mid-sized flat granite headstone that lies flush with the ground. Perfectly balanced in visibility and affordability, this 24"x12" marker provides enough space for names, dates, and a short message. Designed to resist weathering and maintain its elegant appearance.',
                'category_id' => Category::where('name', 'Flat Headstones')->first()->id,
                'image' => 'headstones/FlatHd4.jpg',
                'images' => [
                    'headstones/FlatHd4.jpg',
                    'headstones/FlatHd3.jpg',
                    'headstones/FlatHd2.jpg',
                    'headstones/FlatHd1.jpg',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Ideal size and well crafted. It blends beautifully into the lawn cemetery.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Excellent quality and durable finish. We were impressed with the clarity of the engraving.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "“A beautiful tribute to my father. The craftsmanship is outstanding — thank you for helping us honor him properly.”", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "The detail and finish are perfect. This memorial gave our family some peace during a hard time", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "It was just what we were hoping for. The epitaph engraving turned out lovely and heartfelt", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques',
                'description' => 'A small, elegant plaque ideal for wall memorials or garden placements. Best suited for minimal text, such as names or initials with dates.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/MemorialPlaque1.jpg',
                'images' => [
                    'headstones/MemorialPlaque1.jpg',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Very tasteful and compact. Great for a small tribute.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Delivered quickly and looks lovely in our memorial garden.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Delivered on time and very well packaged. Communication was also great throughout.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'It arrived earlier than expected and installation was seamless.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The team was incredibly responsive and made a tough time easier.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Fits perfectly on the family altar. The finish is top-notch.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Elegant and meaningful. Just what we needed.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Everything from ordering to delivery was smooth. Highly recommend.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Customer support was empathetic and helpful. I felt cared for.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'A very moving experience from start to finish. The memorial is perfect.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'We added a short poem and it came out beautifully.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The engraving is clear and emotional. Thank you.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Worth every penny. It’s a beautiful resting place marker for my son.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Simple process with respectful service. Beautiful outcome.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'We’ve received many compliments from visitors. Thank you', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Full Grave Cover',
                'description' => 'A premium full grave cover crafted from high-grade granite. Known for its timeless beauty, it adds permanence and dignity to a loved one’s final resting place.',
                'category_id' => Category::where('name', 'Full Grave Covers')->first()->id,
                'image' => 'headstones/FullGRC4.jpg',
                'images' => [
                    'headstones/FullGRC4.jpg',
                    'headstones/FullGRC3.jpg',
                    'headstones/FullGRC2.jpg',
                    'headstones/FullGRC1.jpg',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Heavy, beautiful, and elegant. It honors my father’s memory perfectly.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The granite gives it a noble presence. Extremely satisfied.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],

            [
                'title' => 'Upright Headstone',
                'description' => 'A classic vertical gravestone made from polished granite, typically used to mark and identify individual graves. Upright headstones are popular for their visibility, elegance, and the space they offer for inscriptions, religious symbols, and portraits. Designed to withstand weather conditions in Kenya and preserve the memory of a loved one with dignity.',
                'category_id' => Category::where('name', 'Upright Headstones')->first()->id,
                'image' => 'headstones/UprightHd1.jpg',
                'images' => [
                    'headstones/UprightHd1.jpg',
                ],
                'reviews' => [
                    [
                        'rating' => rand(3, 5),
                        'comment' => 'The craftsmanship on this headstone exceeded our expectations. The lettering is crisp and clear, and it truly honors our loved one’s memory.',
                        'customer_id' => User::customers()->inRandomOrder()->first()->id,
                    ],
                    [
                        'rating' => rand(3, 5),
                        'comment' => 'Customer service was compassionate and responsive. The upright headstone arrived on time and looks beautiful in the cemetery',
                        'customer_id' => User::customers()->inRandomOrder()->first()->id,
                    ],
                    [
                        'rating' => rand(3, 5),
                        'comment' => 'Very satisfied with the design and granite quality. It feels strong and durable, and the customization turned out just right.',
                        'customer_id' => User::customers()->inRandomOrder()->first()->id,
                    ],
                    [
                        'rating' => rand(3, 5),
                        'comment' => 'This headstone brought peace to our family. The design is elegant, and the team handled everything with great care and respect.',
                        'customer_id' => User::customers()->inRandomOrder()->first()->id,
                    ],

                ]
            ],
        ];

        foreach ($materials as $index => $materialData) {
            Model::withoutEvents(function () use ($materialData, $index) {
                Material::factory()->create([
                    'name' => $materialData['name'],
                    'sku' => 'MAT-' . strtoupper(Str::take($materialData['name'], 3)) . '-' . $index + 1,
                    'slug' => Str::slug($materialData['name'], '-'),
                    'description' => $materialData['description'],
                ]);
            });
        }

        foreach ($headstoneTags as $tag) {
            Model::withoutEvents(function () use ($tag) {
                Tag::create([
                    'name' => $tag,
                    'slug' => Str::slug($tag, '-'),
                    'status' => 'published',
                    'created_by' => User::operators()->inRandomOrder()->first()->id,
                ]);
            });
        }

        foreach ($memorials as $index => $memorialData) {
            Model::withoutEvents(function () use ($memorialData, $index) {
                $memorial = Memorial::factory()->create([
                    'title' => $memorialData['title'],
                    'sku' => 'MHS-' . date('Y') . '-' . $index + 1,
                    'slug' => Str::slug($memorialData['title'], '-'),
                    'description' => $memorialData['description'],
                    'image' => $memorialData['image'],
                    'category_id' => $memorialData['category_id'],
                ]);

                $randomMaterialsIds = Material::inRandomOrder()->take(rand(1, 4))->pluck('id')->toArray();
                $randomTags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();

                $memorial->materials()->sync($randomMaterialsIds);
                $memorial->tags()->sync($randomTags);

                if (isset($memorialData['images'])) {
                    foreach ($memorialData['images'] as $image) {
                        $memorial->images()->create(['path' => $image]);
                    }
                }

                if (isset($memorialData['reviews'])) {
                    foreach ($memorialData['reviews'] as $review) {
                        $memorial->reviews()->create([
                            'rating' => $review['rating'],
                            'review' => $review['comment'],
                            'customer_id' => $review['customer_id'],
                            'status' => 'published',
                        ]);
                    }
                }
            });
        }
    }
}
