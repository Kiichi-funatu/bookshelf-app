<?php

namespace Tests\Feature\Review;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはレビュー編集画面にアクセスできずログインへリダイレクトされる()
    {
        $review = Review::factory()->create();

        $response = $this->get(route('reviews.edit', $review));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 他人はレビュー編集画面にアクセスすると403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $response = $this->actingAs($other)->get(route('reviews.edit', $review));

        $response->assertForbidden();
    }

    /** @test */
    public function 作成者はレビュー編集画面にアクセスできる()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create([
            'comment' => '旧レビュー',
            'rating' => 3,
        ]);

        $response = $this->actingAs($owner)->get(route('reviews.edit', $review));

        $response->assertStatus(200);
        $response->assertSee('レビューの編集'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function 作成者はレビューを正常に更新できる()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->for($owner)->for($book)->create();

        $payload = [
            'comment' => '更新後レビュー本文',
            'rating' => 5,
        ];

        $response = $this->actingAs($owner)->put(route('reviews.update', $review), $payload);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'comment' => '更新後レビュー本文',
            'rating' => 5,
        ]);
    }

    /** @test */
    public function contentが空の場合はバリデーションエラーになる()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $payload = [
            'comment' => '',
            'rating' => 4,
        ];

        $response = $this->actingAs($owner)->put(route('reviews.update', $review), $payload);

        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function ratingが空の場合はバリデーションエラーになる()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $payload = [
            'comment' => 'レビュー本文',
            'rating' => '',
        ];

        $response = $this->actingAs($owner)->put(route('reviews.update', $review), $payload);

        $response->assertSessionHasErrors(['rating']);
    }

    /** @test */
    public function ratingが範囲外の場合はバリデーションエラーになる()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create();

        $payload = [
            'comment' => 'レビュー本文',
            'rating' => 10, // 1〜5 の範囲外
        ];

        $response = $this->actingAs($owner)->put(route('reviews.update', $review), $payload);

        $response->assertSessionHasErrors(['rating']);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->get(route('reviews.edit', 999999));

        $response->assertNotFound();
    }
}
