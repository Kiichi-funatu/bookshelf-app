<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function ゲストは書籍編集画面にアクセスできずログインへリダイレクトされる()
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.edit', $book));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 他人が書籍編集画面にアクセスすると403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($other)->get(route('books.edit', $book));

        $response->assertForbidden();
    }

    /** @test */
    public function 作成者は書籍編集画面にアクセスできる()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($owner)->get(route('books.edit', $book));

        $response->assertStatus(200);
        $response->assertSee('書籍の編集'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function 作成者は書籍を正常に更新できる()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $genre = Genre::factory()->create();

        $payload = [
            'title' => '更新後タイトル',
            'author' => '更新後著者',
            'isbn' => '1234567890123',
            'published_date' => '2024-02-01',
            'description' => '更新後説明文',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($owner)->put(route('books.update', $book), $payload);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後タイトル',
            'author' => '更新後著者',
        ]);

        $book->refresh();
        $this->assertTrue($book->genres->contains($genre));
    }

    /** @test */
    public function バリデーションエラーの場合はエラーが返る()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $payload = [
            'title' => '', // 必須項目を空にする
            'author' => '著者',
        ];

        $response = $this->actingAs($owner)->put(route('books.update', $book), $payload);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->get(route('books.edit', 999999));

        $response->assertNotFound();
    }
}
