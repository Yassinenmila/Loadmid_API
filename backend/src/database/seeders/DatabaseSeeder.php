<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Post::factory(50)->create();

        Like::factory(200)->make()->each(function ($like) {
            DB::table('likes')->insertOrIgnore($like->toArray());
        });


        Comment::factory(200)->create();


    }
}
