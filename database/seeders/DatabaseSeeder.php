<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\About;
use App\Models\Advertisement;
use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            "name" => "Admin",
            "username" => "admin",
            "email" => "admin@gmail.com",
            "password" => Hash::make('admin123'),
            "birthday" => "1999-06-12",
            "is_admin" => true,
            "is_agent" => false,
            "phone" => "09123456789",
            "address" => null
        ]);

        $agent = User::create([
            "name" => "Kyaw Kyaw",
            "username" => "kyawkyaw",
            "email" => "kyawkyaw@gmail.com",
            "password" => Hash::make('kyawkyaw'),
            "birthday" => "1997-01-12",
            "is_admin" => false,
            "is_agent" => true,
            "phone" => "09744785126",
            "address" => "Pyigyidagon, Mandalay"
        ]);

        // User::factory(5)->create(['is_agent' => true]); // agents
        // User::factory(5)->create(['is_agent' => false]); // users

        Post::factory(10)->create(['user_id' => 1]); // admin posts

        Post::factory(3)->create(['description' => 'A flat in Mandalay']);
        Post::factory(3)->create(['township' => 'Chanmyathazi', 'city' => 'Mandalay', 'state_or_division' => 'Mandalay', 'status' => 'sell']);
        Post::factory(3)->create(['township' => 'Pyigyidagun', 'city' => 'Mandalay', 'state_or_division' => 'Mandalay', 'status' => 'rent']);
        Post::factory(3)->create(['township' => 'Patheingyi', 'city' => 'Mandalay', 'state_or_division' => 'Mandalay', 'status' => 'sell']);

        Blog::factory(10)->create(['user_id' => $admin->id]);

        Advertisement::factory(4)->create();

        About::factory(1)->create();
    }
}
