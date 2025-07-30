<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 1 admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        // Create 5 regular users
        User::factory(5)->create();

        // Create 5 categories
        Category::factory(5)->create();

        // Create 20 posts
        Post::factory(20)->create();
    }
}
