<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Gemre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 公開ページとして書籍詳細にアクセスできる()
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    /** @test */
    public function 書籍の基本情報が表示される()
    {
        $book = Book::factory()->create([
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'description' => 'テスト説明文',
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertSee('テストタイトル');
        $response->assertSee('テスト著者');
        $response->assertSee('テスト説明文');
    }

    /** @test */
    public function 書籍に紐づくジャンルが表示される()
    {
        $genre = Genre::factory()->create(['name' => 'ミステリー']);
        $book = Book::factory()->hasAttached($genre)->create();

        $response = $this->get(route('books.show', $book));

        $response->assertSee('ミステリー');
    }

    /** @test */
    public function 書籍に紐づくレビューが表示される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->for($book)->for($user)->create([
            'content' => 'レビュー本文',
            'rating' => 4,
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertSee('レビュー本文');
        $response->assertSee('4'); // 評価
    }

    /** @test */
    public function いいね数が表示される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // pivot: likes
        $book->likes()->attach($user->id);

        $response = $this->get(route('books.show', $book));

        $response->assertSee('1'); // いいね数
    }

    /** @test */
    public function お気に入り状態が表示される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $book->favorites()->attach($user->id);

        $response = $this->actingAs($user)->get(route('books.show', $book));

        $response->assertSee('お気に入り済み'); // Blade の文言に合わせる
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $response = $this->get(route('books.show', 999999));

        $response->assertNotFound();
    }
}
