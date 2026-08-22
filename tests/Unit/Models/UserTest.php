<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function favoritesリレーションが機能する()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favorites()->attach($book->id);

        $this->assertTrue($user->favorites->contains($book));
    }

    /** @test */
    public function likesリレーションが機能する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likes()->attach($review->id);

        $this->assertTrue($user->likes->contains($review));
    }

    /** @test */
    public function reviewsリレーションが機能する()
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $this->assertTrue($user->reviews->contains($review));
    }
}
