<?php

namespace Tests\Feature\Genre;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはジャンル一覧にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get(route('genres.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル一覧にアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200);
        $response->assertSee('ジャンル管理'); // Blade のタイトル
    }

    /** @test */
    public function ジャンル名が一覧に表示される()
    {
        $user = User::factory()->create();

        Genre::factory()->create(['name' => '技術']);
        Genre::factory()->create(['name' => '小説']);

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertSee('技術');
        $response->assertSee('小説');
    }

    /** @test */
    public function 書籍数が表示される()
    {
        $user = User::factory()->create();

        Genre::factory()->create(['name' => '技術']);

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertSee('冊'); // books_count の表示
    }

}
