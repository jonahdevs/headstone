<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::withoutEvents(function () {
            User::factory()->create([
                'email' => 'customer@example.com',
                'password' => 'password'
            ])->assignRole('customer');

            User::factory()->create([
                'email' => 'manager@example.com',
                'password' => 'password'
            ])->assignRole('manager');

            User::factory()->create([
                'email' => 'admin@example.com',
                'password' => 'password'
            ])->assignRole('admin');

            User::factory()->count(20)->create()->each(fn(User $user) => $user->assignRole('customer'));
            User::factory()->count(3)->create()->each(fn(User $user) => $user->assignRole('manager'));
            User::factory()->count(2)->create()->each(fn(User $user) => $user->assignRole('admin'));
        });
    }
}
