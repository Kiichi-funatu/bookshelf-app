<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reviewsリレーションが機能する()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->for($book)->create();

        $this->assertTrue($book->reviews->contains($review));
    }

    /** @test */
    public function favoritesリレーションが機能する()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favorites()->attach($user->id);

        $this->assertTrue($book->favorites->contains($user));
    }

    /** @test */
    public function genresリレーションが機能する()
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre->id);

        $this->assertTrue($book->genres->contains($genre));
    }
}
