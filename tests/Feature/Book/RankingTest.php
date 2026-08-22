<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 公開ページとしてランキングにアクセスできる()
    {
        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertSee('ランキング'); // Blade のタイトルに合わせる
    }

    /** @test */
    public function 平均評価が高い順に並ぶ()
    {
        // Book A → 平均 5
        $bookA = Book::factory()->create(['title' => '本A']);
        Review::factory()->for($bookA)->create(['rating' => 5]);

        // Book B → 平均 3
        $bookB = Book::factory()->create(['title' => '本B']);
        Review::factory()->for($bookB)->create(['rating' => 3]);

        $response = $this->get(route('ranking.index'));

        $response->assertSeeInOrder([
            '本A',
            '本B',
        ]);
    }

    /** @test */
    public function ランキングは上位10件に絞られる()
    {
        // 15件作成（レビュー平均は全て5）
        $books = Book::factory()->count(15)->create();
        foreach ($books as $book) {
            Review::factory()->for($book)->create(['rating' => 5]);
        }

        $response = $this->get(route('ranking.index'));

        // 2ページ目が存在する（paginate 10）
        $response->assertSee('?page=2');
    }
}
