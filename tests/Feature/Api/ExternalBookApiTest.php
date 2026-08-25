<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalBookApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 外部APIのレスポンスをHttpFakeで差し替えてテストできる()
    {
        // 外部APIの偽レスポンス
        Http::fake([
            'https://example.com/api/books' => Http::response([
                ['id' => 1, 'title' => 'Fake Book'],
                ['id' => 2, 'title' => 'Another Fake Book'],
            ], 200)
        ]);

        // 実際のAPIルートを叩く
        $response = $this->get('/api/external-books');

        // 偽レスポンスが返ってくることを確認
        $response->assertStatus(200);
        $response->assertJson([
            ['id' => 1, 'title' => 'Fake Book'],
            ['id' => 2, 'title' => 'Another Fake Book'],
        ]);
    }
}
