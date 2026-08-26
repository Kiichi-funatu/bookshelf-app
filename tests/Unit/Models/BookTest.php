<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

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

    /** @test */
    public function averageRatingメソッドが正しく動作する()
    {
        $book = Book::factory()->create();

        Review::factory()->for($book)->create(['rating' => 5]);
        Review::factory()->for($book)->create(['rating' => 3]);

        $this->assertEquals(4, $book->averageRating());
    }

    /** @test */
    public function published_dateキャストがCarbonになる()
    {
        $book = Book::factory()->create([
            'published_date' => '2024-01-01',
        ]);

        $this->assertInstanceOf(Carbon::class, $book->published_date);
    }

    /** @test */
    public function keywordスコープが機能する()
    {
        Book::factory()->create(['title' => 'Laravel入門']);
        Book::factory()->create(['title' => 'PHPの本']);

        $results = Book::keyword('Laravel')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laravel入門', $results->first()->title);
    }
}
