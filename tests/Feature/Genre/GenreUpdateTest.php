<?php

namespace Tests\Feature\Genre;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはジャンル編集画面にアクセスできずログインへリダイレクトされる()
    {
        $genre = Genre::factory()->create();

        $response = $this->get(route('genres.edit', $genre));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル編集画面にアクセスできる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => 'ミステリー']);

        $response = $this->actingAs($user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
        $response->assertSee('ジャンル編集'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function ジャンルを正常に更新できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '旧ジャンル名']);

        $payload = [
            'name' => '新ジャンル名',
        ];

        $response = $this->actingAs($user)->put(route('genres.update', $genre), $payload);

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '新ジャンル名',
        ]);
    }

    /** @test */
    public function バリデーションエラーの場合はエラーが返る()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $payload = [
            'name' => '', // 必須項目を空にする
        ];

        $response = $this->actingAs($user)->put(route('genres.update', $genre), $payload);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.edit', 999999));

        $response->assertNotFound();
    }
}
