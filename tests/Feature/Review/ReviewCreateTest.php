<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはレビュー投稿できずログインへリダイレクトされる()
    {
        $book = Book::factory()->create();

        $response = $this->post(route('reviews.store', $book), [
            'content' => 'レビュー本文',
            'rating' => 5,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはレビューを正常に投稿できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $payload = [
            'content' => 'とても面白かった！',
            'rating' => 5,
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $payload);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('reviews', [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'content' => 'とても面白かった！',
            'rating' => 5,
        ]);
    }

    /** @test */
    public function contentが空の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $payload = [
            'content' => '',
            'rating' => 5,
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $payload);

        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function ratingが空の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $payload = [
            'content' => 'レビュー本文',
            'rating' => '',
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $payload);

        $response->assertSessionHasErrors(['rating']);
    }

    /** @test */
    public function ratingが範囲外の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $payload = [
            'content' => 'レビュー本文',
            'rating' => 10, // 1〜5 の範囲外
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $payload);

        $response->assertSessionHasErrors(['rating']);
    }
}
