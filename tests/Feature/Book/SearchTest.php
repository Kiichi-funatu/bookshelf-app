<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 公開ページとして検索が利用できる()
    {
        $response = $this->get(route('books.index', ['keyword' => 'テスト']));

        $response->assertStatus(200);
        $response->assertSee('書籍一覧'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function タイトルで部分一致検索できる()
    {
        Book::factory()->create(['title' => 'Laravel入門']);
        Book::factory()->create(['title' => 'PHPの本']);

        $response = $this->get(route('books.index', ['keyword' => 'Laravel']));

        $response->assertSee('Laravel入門');
        $response->assertDontSee('PHPの本');
    }

    /** @test */
    public function 著者名で部分一致検索できる()
    {
        Book::factory()->create(['author' => '山田太郎', 'title' => '本A']);
        Book::factory()->create(['author' => '佐藤花子', 'title' => '本B']);

        $response = $this->get(route('books.index', ['keyword' => '山田']));

        $response->assertSee('本A');
        $response->assertDontSee('本B');
    }

    /** @test */
    public function 検索キーワードがBladeに保持されて表示される()
    {
        $response = $this->get(route('books.index', ['keyword' => 'Laravel']));

        $response->assertSee('value="Laravel"', false); // <input value="Laravel">
    }

    /** @test */
    public function 検索結果は10件でページネーションされる()
    {
        // created_at をずらして 15 件作成
        for ($i = 0; $i < 15; $i++) {
            Book::factory()->create([
                'title' => 'Laravel本',
                'author' => 'Laravel著者' . $i,
                'created_at' => now()->subMinutes($i),
            ]);
        }

        $response = $this->get(route('books.index', ['keyword' => 'Laravel']));

        // 2ページ目が存在する
        $response->assertSee('rel="next"', false);
    }

    /** @test */
    public function 検索に一致しない場合は何も表示されない()
    {
        Book::factory()->create(['title' => 'Laravel入門']);

        $response = $this->get(route('books.index', ['keyword' => 'Python']));

        $response->assertDontSee('Laravel入門');
        $response->assertSee('書籍が見つかりませんでした。'); // Blade の文言に合わせる
    }
}
