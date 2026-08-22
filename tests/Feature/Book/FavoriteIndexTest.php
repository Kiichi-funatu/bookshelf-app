<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはお気に入り一覧にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get(route('favorites.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはお気に入り一覧にアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertSee('お気に入り一覧'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function ログインユーザーのお気に入り書籍だけが表示される()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $book1 = Book::factory()->create(['title' => 'ユーザーの本']);
        $book2 = Book::factory()->create(['title' => '他人の本']);

        // ユーザーのお気に入り
        $book1->favorites()->attach($user->id);

        // 他人のお気に入り
        $book2->favorites()->attach($other->id);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertSee('ユーザーの本');
        $response->assertDontSee('他人の本');
    }

    /** @test */
    public function お気に入り一覧は10件でページネーションされる()
    {
        $user = User::factory()->create();

        // 15件お気に入り登録
        $books = Book::factory()->count(15)->create();
        foreach ($books as $book) {
            $book->favorites()->attach($user->id);
        }

        $response = $this->actingAs($user)->get(route('favorites.index'));

        // 2ページ目が存在する
        $response->assertSee('?page=2');
    }
}
