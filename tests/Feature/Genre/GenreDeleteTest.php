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
        $genre = Genre::factory()->create();
        Book::factory()->for($genre)->create(); // 紐づけ

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('genres', ['id' => $genre->id]); // 削除されていない
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
