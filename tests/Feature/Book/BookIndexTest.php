<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF 無効化
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ★ 毎テストで検索条件（GETパラメータ）を完全リセット
        $_GET = [];
        $_SERVER['QUERY_STRING'] = '';
    }

    /** @test */
    public function 公開ページとして書籍一覧にアクセスできる()
    {
        $response = $this->get('/books');

        $response->assertStatus(200);
        $response->assertSee('書籍一覧');
    }

    /** @test */
    public function 書籍一覧が最新順で表示される()
    {
        // ★ published_date の新しい順で並ぶ仕様に合わせる
        $old = Book::factory()->create([
            'published_date' => '2020-01-01',
            'created_at' => now()->subMinutes(5),
        ]);

        $new = Book::factory()->create([
            'published_date' => '2024-01-01',
            'created_at' => now()->subMinutes(1),
        ]);

        // ★ sort=newest を明示的に指定する
        $response = $this->get('/books?sort=newest');

        $response->assertSeeInOrder([
            $new->title,
            $old->title,
        ]);
    }

    /** @test */
    public function 書籍一覧は10件でページネーションされる()
    {
        Book::factory()->count(15)->create([
            'published_date' => '2024-01-01',
        ]);

        $response = $this->get('/books');

        // 1ページ目には最新のタイトルが表示される
        $response->assertSee(Book::orderBy('created_at', 'desc')->first()->title);

        // 2ページ目が存在する
        $response->assertSee('?page=2');
    }

    /** @test */
    public function 書籍のタイトルと著者が表示される()
    {
        $book = Book::factory()->create([
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'published_date' => '2024-01-01',
        ]);

        $response = $this->get('/books');

        $response->assertSee('テストタイトル');
        $response->assertSee('テスト著者');
    }

    /** @test */
    public function Nプラス1問題が発生しない()
    {
        Book::factory()->count(5)->create([
            'published_date' => '2024-01-01',
        ]);

        $this->assertNotTriggeredNPlusOne(function () {
            $this->get('/books');
        });
    }

    private function assertNotTriggeredNPlusOne(callable $callback)
    {
        \DB::enableQueryLog();
        $callback();
        $queries = \DB::getQueryLog();
        $this->assertTrue(count($queries) < 10, 'N+1 の疑いがあります');
    }
}
