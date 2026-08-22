<?php

namespace Tests\Feature\Review;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはレビュー削除を実行できずログインへリダイレクトされる()
    {
        $review = Review::factory()->create();

        $response = $this->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 他人がレビュー削除を実行すると403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $response = $this->actingAs($other)->delete(route('reviews.destroy', $review));

        $response->assertForbidden();
    }

    /** @test */
    public function 作成者はレビューを正常に削除できる()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->for($owner)->for($book)->create();

        $response = $this->actingAs($owner)->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->delete(route('reviews.destroy', 999999));

        $response->assertNotFound();
    }
}
