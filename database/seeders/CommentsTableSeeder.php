<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comments;
use App\Models\User;
use App\Models\Posts;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Posts::all()->each(function ($post) {
            // Create 5 comments for each post
            Comments::factory(rand(5, 10))->create([
                'post_id' => $post->id,
            ]);
        });
    }
}
