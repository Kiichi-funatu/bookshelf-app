<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response->assertSee('ジャンル一覧'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function ジャンル名が一覧に表示される()
    {
        $user = User::factory()->create();

        $genre1 = Genre::factory()->create(['name' => 'ミステリー']);
        $genre2 = Genre::factory()->create(['name' => 'ファンタジー']);

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertSee('ミステリー');
        $response->assertSee('ファンタジー');
    }

    /** @test */
    public function ジャンル一覧が最新順で表示される()
    {
        $user = User::factory()->create();

        $old = Genre::factory()->create(['created_at' => now()->subDays(2), 'name' => '古いジャンル']);
        $new = Genre::factory()->create(['created_at' => now()->subDay(), 'name' => '新しいジャンル']);

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertSeeInOrder([
            '新しいジャンル',
            '古いジャンル',
        ]);
    }
}
