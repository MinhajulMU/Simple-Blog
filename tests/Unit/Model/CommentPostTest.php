<?php

namespace Tests\Unit\Model;

use App\Models\Posts;
use App\Models\Comments;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CommentPostTest extends TestCase
{
    public function testEventRelation()
    {
        $title = fake()->sentence;
        $name = fake()->name();
        $user = User::create([
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123'),
            'remember_token' => Str::random(10),
        ]);
        $post = Posts::create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraph
        ]);
        Comments::create([
            'post_id' => $post->id,
            'content' => fake()->paragraph,
            'user_id' => $user->id
        ]);
        $comments = Comments::with(['post','user'])->find(1);
        $post = Posts::with(['user'])->find(1);
        $this->assertEquals($title, $comments->post->title);
        $this->assertEquals($name, $comments->user->name);
        $this->assertEquals($name, $post->user->name);
    }
}
