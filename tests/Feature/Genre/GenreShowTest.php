<?php

namespace Tests\Feature\Genre;

use App\Models\User;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはジャンル詳細にアクセスできずログインへリダイレクトされる()
    {
        $genre = Genre::factory()->create();

        $response = $this->get(route('genres.show', $genre));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル詳細にアクセスできる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => 'ミステリー']);

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertSee('ミステリー');
    }

    /** @test */
    public function ジャンルに紐づく書籍が表示される()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => 'ファンタジー']);

        $book1 = Book::factory()->hasAttached($genre)->create(['title' => '本A']);
        $book2 = Book::factory()->hasAttached($genre)->create(['title' => '本B']);

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertSee('本A');
        $response->assertSee('本B');
    }

    /** @test */
    public function 書籍一覧は10件でページネーションされる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Book::factory()->count(15)->hasAttached($genre)->create();

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        // 1ページ目に10件表示される
        $response->assertSee('?page=2');
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.show', 999999));

        $response->assertNotFound();
    }
}
