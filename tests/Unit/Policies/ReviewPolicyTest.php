<?php

namespace Tests\Unit\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\ReviewPolicy;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
    /** @test */
    public function レビュー一覧は誰でも閲覧できる()
    {
        $user = User::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    /** @test */
    public function レビュー詳細は誰でも閲覧できる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertTrue($policy->view($user, $review));
    }

    /** @test */
    public function レビュー作成はログインユーザーなら許可される()
    {
        $user = User::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertTrue($policy->create($user));
    }

    /** @test */
    public function レビュー作成はゲストの場合は拒否される()
    {
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->create(null));
    }

    /** @test */
    public function レビュー編集は投稿者本人なら許可される()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create();
        $policy = new ReviewPolicy();

        $this->assertTrue($policy->update($owner, $review));
    }

    /** @test */
    public function レビュー編集はゲストの場合は拒否される()
    {
        $review = Review::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->update(null, $review));
    }

    /** @test */
    public function レビュー編集は他人の場合は拒否される()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($owner)->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->update($other, $review));
    }

    /** @test */
    public function レビュー削除は投稿者本人なら許可される()
    {
        $owner = User::factory()->create();
        $review = Review::factory()->for($owner)->create();
        $policy = new ReviewPolicy();

        $this->assertTrue($policy->delete($owner, $review));
    }

    /** @test */
    public function レビュー削除はゲストの場合は拒否される()
    {
        $review = Review::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->delete(null, $review));
    }

    /** @test */
    public function レビュー削除は他人の場合は拒否される()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->for($owner)->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->delete($other, $review));
    }

    /** @test */
    public function レビュー復元は常に拒否される()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->restore($user, $review));
    }

    /** @test */
    public function レビュー強制削除は常に拒否される()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $policy = new ReviewPolicy();

        $this->assertFalse($policy->forceDelete($user, $review));
    }
}
