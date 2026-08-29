<?php

namespace Tests\Unit\Policies;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    /** @test */
    public function 書籍一覧は誰でも閲覧できる()
    {
        $user = User::factory()->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    /** @test */
    public function 書籍詳細は誰でも閲覧できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->view($user, $book));
    }

    /** @test */
    public function 書籍作成はログインユーザーなら許可される()
    {
        $user = User::factory()->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->create($user));
    }

    /** @test */
    public function 書籍編集は作成者本人のみ許可される()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->update($owner, $book));
    }

    /** @test */
    public function 書籍編集は他人の場合は拒否される()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $policy = new BookPolicy();

        $this->assertFalse($policy->update($other, $book));
    }

    /** @test */
    public function 書籍削除は作成者本人のみ許可される()
    {
        $owner = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $policy = new BookPolicy();

        $this->assertTrue($policy->delete($owner, $book));
    }

    /** @test */
    public function 書籍削除は他人の場合は拒否される()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $policy = new BookPolicy();

        $this->assertFalse($policy->delete($other, $book));
    }

    /** @test */
    public function 書籍復元は常に拒否される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy();

        $this->assertFalse($policy->restore($user, $book));
    }

    /** @test */
    public function 書籍強制削除は常に拒否される()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $policy = new BookPolicy();

        $this->assertFalse($policy->forceDelete($user, $book));
    }
}
