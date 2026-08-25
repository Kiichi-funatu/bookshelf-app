<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはお気に入り操作できずログインへリダイレクトされる()
    {
        $book = Book::factory()->create();

        $response = $this->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 未お気に入りの場合はお気に入りが追加される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 既にお気に入り済みの場合はお気に入りが解除される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 事前にお気に入り登録
        $book->favorites()->attach($user->id);

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $response->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 二重お気に入りが起きない()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1回目（追加）
        $this->actingAs($user)->post(route('favorites.toggle', $book));

        // 2回目（解除）
        $this->actingAs($user)->post(route('favorites.toggle', $book));

        // 3回目（再追加）
        $this->actingAs($user)->post(route('favorites.toggle', $book));

        // 最終的に1件だけ存在すること
        $this->assertDatabaseHas('favorites', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.toggle', 999999));

        $response->assertNotFound();
    }
}
