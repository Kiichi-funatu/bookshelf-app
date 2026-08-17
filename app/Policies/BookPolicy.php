<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * 書籍一覧の閲覧権限（誰でも閲覧可能）
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * 書籍詳細の閲覧権限（誰でも閲覧可能）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function view(User $user, Book $book): bool
    {
        return true;
    }

   /**
     * 書籍作成権限（ログイン必須）
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 書籍編集権限（作成者のみ）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function update(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }

    /**
     * 書籍削除権限（作成者のみ）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function delete(User $user, Book $book): bool
    {
        return $book->user_id === $user->id;
    }

    /**
     * 書籍復元（未使用のため false）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function restore(User $user, Book $book): bool
    {
        return false;
    }

    /**
     * 書籍強制削除（未使用のため false）
     *
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return false;
    }
}
