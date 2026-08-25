<?php

namespace Tests\Feature\Genre;

use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはジャンル削除を実行できずログインへリダイレクトされる()
    {
        $genre = Genre::factory()->create();

        $response = $this->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 書籍が紐づいているジャンルは削除できずエラーメッセージが返る()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->attach($genre->id);

        $response = $this->delete(route('genres.destroy', $genre));

        // ★ 仕様書どおり：エラーメッセージが返ることを確認
        $response->assertSessionHas('error');

        // 削除されていない
        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
    }

    /** @test */
    public function 書籍が紐づいていないジャンルは正常に削除できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', 999999));

        $response->assertNotFound();
    }
}
