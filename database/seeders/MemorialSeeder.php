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
                'title' => 'Flat Headstone (24"x12"x4")',
                'description' => 'A mid-sized flat granite headstone that lies flush with the ground. Perfectly balanced in visibility and affordability, this 24"x12" marker provides enough space for names, dates, and a short message. Designed to resist weathering and maintain its elegant appearance.',
                'category_id' => Category::where('name', 'Flat Headstones')->first()->id,
                'image' => 'headstones/flatD1.webp',
                'images' => [
                    'headstones/flatD1.webp',
                    'headstones/flatD2.webp',
                    'headstones/flatD3.webp',
                    'headstones/flatD4.webp',
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
                'title' => 'Flat Headstone (36"x12"x4")',
                'description' => 'This wider flat headstone offers additional room for a full epitaph or artistic design. Ideal for couples or those who wish to include longer messages. Its flush design supports a minimalist, low-profile gravesite appearance.',
                'image' => 'headstones/flatD2.webp',
                'category_id' => Category::where('name', 'Flat Headstones')->first()->id,
                'images' => [
                    'headstones/flatD2.webp',
                    'headstones/flatD1.webp',
                    'headstones/flatD3.webp',
                    'headstones/flatD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'The extra space let us include everything we wanted. Very pleased with the result.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Beautiful stone and finish. Very respectful presentation.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "This stone captures exactly who my grandmother was — elegant, strong, and graceful", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "I cried when I saw it in person. Thank you for creating something so meaningful and dignified.", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "Stunning quality. The engraving is crisp and the stone feels solid and durable", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Flat Headstone (16"x12"x4")',
                'description' => 'Compact and elegant, this headstone is perfect for individual plots or limited space. It offers a neat, tidy look while allowing essential personalization.',
                'image' => 'headstones/flatD3.webp',
                'category_id' => Category::where('name', 'Flat Headstones')->first()->id,
                'images' => [
                    'headstones/flatD3.webp',
                    'headstones/flatD1.webp',
                    'headstones/flatD2.webp',
                    'headstones/flatD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Perfect for our small family plot. Simple and heartfelt.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'A respectful memorial with just the right size for our needs.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "Very impressed by the finish — it looks even better than the photos", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "Excellent materials used. The bronze plaque detail stands out beautifully.", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => "The font styles and layout options made it easy to personalize.", 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (5"x7.5")',
                'description' => 'A small, elegant plaque ideal for wall memorials or garden placements. Best suited for minimal text, such as names or initials with dates.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD1.webp',
                'images' => [
                    'headstones/memorialD1.webp',
                    'headstones/memorialD2.webp',
                    'headstones/memorialD3.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Very tasteful and compact. Great for a small tribute.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Delivered quickly and looks lovely in our memorial garden.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Delivered on time and very well packaged. Communication was also great throughout.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'It arrived earlier than expected and installation was seamless.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The team was incredibly responsive and made a tough time easier.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (6"x8")',
                'description' => 'A versatile plaque size for basic memorial inscriptions. Commonly used in family altars or prayer areas as a dignified remembrance.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD2.webp',
                'images' => [
                    'headstones/memorialD2.webp',
                    'headstones/memorialD1.webp',
                    'headstones/memorialD3.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Fits perfectly on the family altar. The finish is top-notch.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Elegant and meaningful. Just what we needed.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Everything from ordering to delivery was smooth. Highly recommend.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Customer support was empathetic and helpful. I felt cared for.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'A very moving experience from start to finish. The memorial is perfect.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (8"x10")',
                'description' => 'Larger surface area suitable for short poems, prayers, or dedications. Excellent for garden stones or fixed wall placements.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD3.webp',
                'images' => [
                    'headstones/memorialD3.webp',
                    'headstones/memorialD1.webp',
                    'headstones/memorialD2.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'We added a short poem and it came out beautifully.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The engraving is clear and emotional. Thank you.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Worth every penny. It’s a beautiful resting place marker for my son.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Simple process with respectful service. Beautiful outcome.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'We’ve received many compliments from visitors. Thank you', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (12"x18")',
                'description' => 'A substantial plaque ideal for outdoor placement. It offers room for decorative motifs and extended inscriptions. Popular for commemorative installations.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD4.webp',
                'images' => [
                    'headstones/memorialD4.webp',
                    'headstones/memorialD1.webp',
                    'headstones/memorialD2.webp',
                    'headstones/memorialD3.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Used it for a church memorial. Visitors always mention how moving it is.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'A great blend of size and detail. Made with care.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'A graceful and lasting tribute that brings comfort every time we visit.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (16"x20")',
                'description' => 'Large enough to be a focal point in memorial gardens or grave surroundings. Made of weather-resistant material for lasting tribute.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD1.webp',
                'images' => [
                    'headstones/memorialD1.webp',
                    'headstones/memorialD2.webp',
                    'headstones/memorialD3.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'It stands out just the way we hoped. Very dignified.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Perfect craftsmanship and great durability.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (18"x24")',
                'description' => 'One of the largest plaques available, excellent for group memorials, dedications, or detailed personal stories. Heavy and durable, suitable for public installations.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD2.webp',
                'images' => [
                    'headstones/memorialD2.webp',
                    'headstones/memorialD1.webp',
                    'headstones/memorialD3.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'This piece tells our family story. Thank you for preserving it so beautifully.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Powerful presence in our community garden. Everyone loves it.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Memorial Plaques (24"x36")',
                'description' => 'Our largest plaque size for institutions, churches, or family estate memorials. Allows for custom artwork, extensive text, and logos.',
                'category_id' => Category::where('name', 'Memorial Plaques')->first()->id,
                'image' => 'headstones/memorialD3.webp',
                'images' => [
                    'headstones/memorialD3.webp',
                    'headstones/memorialD1.webp',
                    'headstones/memorialD2.webp',
                    'headstones/memorialD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'This was used for our school’s memorial wall. A stunning piece.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Sturdy and well crafted. Survives the weather and still looks brand new.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Granite Full Grave Cover',
                'description' => 'A premium full grave cover crafted from high-grade granite. Known for its timeless beauty, it adds permanence and dignity to a loved one’s final resting place.',
                'category_id' => Category::where('name', 'Full Grave Covers')->first()->id,
                'image' => 'headstones/fullgraveD1.webp',
                'images' => [
                    'headstones/fullgraveD1.webp',
                    'headstones/fullgraveD2.webp',
                    'headstones/fullgraveD3.webp',
                    'headstones/fullgraveD4.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Heavy, beautiful, and elegant. It honors my father’s memory perfectly.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'The granite gives it a noble presence. Extremely satisfied.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Concrete Full Grave Cover',
                'description' => 'A cost-effective and durable grave cover made from reinforced concrete. Common in traditional Kenyan settings and appreciated for its stability.',
                'category_id' => Category::where('name', 'Full Grave Covers')->first()->id,
                'image' => 'headstones/fullgraveD2.webp',
                'images' => [
                    'headstones/fullgraveD2.webp',
                    'headstones/fullgraveD4.webp',
                    'headstones/fullgraveD1.webp',
                    'headstones/fullgraveD3.webp',
                ],
                'reviews' => [
                    ['rating' => rand(3, 5), 'comment' => 'Budget-friendly but still very respectful. Serves its purpose well.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                    ['rating' => rand(3, 5), 'comment' => 'Simple yet strong. We appreciate the guidance in choosing this.', 'customer_id' => User::customers()->inRandomOrder()->first()->id],
                ]
            ],
            [
                'title' => 'Upright Headstone',
                'description' => 'A classic vertical gravestone made from polished granite, typically used to mark and identify individual graves. Upright headstones are popular for their visibility, elegance, and the space they offer for inscriptions, religious symbols, and portraits. Designed to withstand weather conditions in Kenya and preserve the memory of a loved one with dignity.',
                'category_id' => Category::where('name', 'Upright Headstones')->first()->id,
                'image' => 'headstones/uprightD1.png',
                'images' => [
                    'headstones/uprightD1.png',
                    'headstones/uprightD2.png',
                    'headstones/uprightD3.png',
                    'headstones/uprightD4.png',
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
