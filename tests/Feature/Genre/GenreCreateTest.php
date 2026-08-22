<?php

namespace Tests\Feature\Genre;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはジャンル作成画面にアクセスできずログインへリダイレクトされる()
    {
        $response = $this->get(route('genres.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログインユーザーはジャンル作成画面にアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.create'));

        $response->assertStatus(200);
        $response->assertSee('ジャンル登録'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function ジャンルを正常に登録できる()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'ミステリー',
        ];

        $response = $this->actingAs($user)->post(route('genres.store'), $payload);

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', [
            'name' => 'ミステリー',
        ]);
    }

    /** @test */
    public function バリデーションエラーの場合はエラーが返る()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => '', // 必須項目を空にする
        ];

        $response = $this->actingAs($user)->post(route('genres.store'), $payload);

        $response->assertSessionHasErrors(['name']);
    }
}
