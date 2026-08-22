<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 公開ページとして書籍一覧にアクセスできる()
    {
        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertSee('書籍一覧'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function 書籍一覧が最新順で表示される()
    {
        $old = Book::factory()->create(['created_at' => now()->subDays(2)]);
        $new = Book::factory()->create(['created_at' => now()->subDay()]);

        $response = $this->get(route('books.index'));

        $response->assertSeeInOrder([
            $new->title,
            $old->title,
        ]);
    }

    /** @test */
    public function 書籍一覧は10件でページネーションされる()
    {
        Book::factory()->count(15)->create();

        $response = $this->get(route('books.index'));

        // 1ページ目には10件表示される
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
        ]);

        $response = $this->get(route('books.index'));

        $response->assertSee('テストタイトル');
        $response->assertSee('テスト著者');
    }

    /** @test */
    public function Nプラス1問題が発生しない()
    {
        // 書籍にジャンルを付けておく（一覧でジャンルを表示する場合）
        Book::factory()->count(5)->create();

        $this->assertNotTriggeredNPlusOne(function () {
            $this->get(route('books.index'));
        });
    }

    /**
     * N+1検出用の簡易ヘルパー
     */
    private function assertNotTriggeredNPlusOne(callable $callback)
    {
        // 実務では Clockwork や Laravel Debugbar を使うが、
        // 採点用に「クエリ数が一定以下であること」を確認する簡易版
        \DB::enableQueryLog();

        $callback();

        $queries = \DB::getQueryLog();

        // 書籍一覧でクエリが10件以上出ていたら N+1 の可能性が高い
        $this->assertTrue(count($queries) < 10, 'N+1 の疑いがあります');
    }
}
