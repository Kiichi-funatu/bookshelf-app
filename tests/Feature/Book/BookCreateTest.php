<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function ゲストは書籍作成画面にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーは書籍作成画面にアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('books.create'));

        $response->assertStatus(200);
        $response->assertSee('書籍登録'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function 書籍を正常に登録できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $payload = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2024-01-01',
            'description' => '説明文',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $payload);

        $response->assertRedirect(route('books.show', 1));

        $this->assertDatabaseHas('books', [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
        ]);

        $book = Book::where('title', 'テスト書籍')->first();
        $this->assertTrue($book->genres->contains($genre));
    }

    /** @test */
    public function バリデーションエラーの場合はエラーが返る()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => '', // 必須項目を空にする
            'author' => '著者',
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $payload);

        $response->assertSessionHasErrors(['title']);
    }
}
