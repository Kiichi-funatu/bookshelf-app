<?php

namespace Tests\Feature\Review;

use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストはいいね操作できずログインへリダイレクトされる()
    {
        $review = Review::factory()->create();

        $response = $this->post(route('reviews.like', $review));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 未いいねの場合はいいねが追加される()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.like', $review));

        $response->assertRedirect();

        $this->assertDatabaseHas('review_like', [
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 既にいいね済みの場合はいいねが解除される()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 事前にいいねしておく
        $review->likes()->attach($user->id);

        $response = $this->actingAs($user)->post(route('reviews.like', $review));

        $response->assertRedirect();

        $this->assertDatabaseMissing('review_like', [
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 二重いいねが起きない()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 1回目（いいね追加）
        $this->actingAs($user)->post(route('reviews.like', $review));

        // 2回目（いいね解除）
        $this->actingAs($user)->post(route('reviews.like', $review));

        // 3回目（再度いいね追加）
        $this->actingAs($user)->post(route('reviews.like', $review));

        // 最終的に1件だけ存在すること
        $this->assertDatabaseHas('review_like', [
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 存在しないIDの場合は404が返る()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.like', 999999));

        $response->assertNotFound();
    }
}
