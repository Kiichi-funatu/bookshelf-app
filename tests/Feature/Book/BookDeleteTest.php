<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストは書籍削除を実行できずログインへリダイレクトされる()
    {
        $book = Book::factory()->create();

        $response = $this->delete(route('books.destroy', $book));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 他人が書籍削除を実行すると403が返る()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $book = Book::factory()->for($owner)->create();

        $response = $this->actingAs($other)->delete(route('books.destroy', $book));

        $response->assertForbidden();
    }

    /** @test */
    public function 作成者は書籍を正常に削除できる()
    {
        $owner = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()
            ->for($owner)
            ->hasAttached($genre)
            ->create();

        $response = $this->actingAs($owner)->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        // pivot が detach されていること
        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->delete(route('books.destroy', 999999));

        $response->assertNotFound();
    }
}
