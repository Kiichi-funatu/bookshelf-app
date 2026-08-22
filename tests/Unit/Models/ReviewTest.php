<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function bookリレーションが機能する()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->for($book)->create();

        $this->assertEquals($book->id, $review->book->id);
    }

    /** @test */
    public function userリレーションが機能する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $this->assertEquals($user->id, $review->user->id);
    }

    /** @test */
    public function likesリレーションが機能する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $review->likes()->attach($user->id);

        $this->assertTrue($review->likes->contains($user));
    }
}
