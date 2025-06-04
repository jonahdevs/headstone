<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Flat Headstones',
            'Upright Headstones',
            'Memorial Plaques',
            'Full Grave Covers',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'status' => 'published',
                'created_by' => User::operators()->inRandomOrder()->first()->id,
            ]);
        }
    }
}
