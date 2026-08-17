<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * レビュー一覧の閲覧権限（誰でも閲覧可能）
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * レビュー詳細の閲覧権限（誰でも閲覧可能）
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function view(User $user, Review $review): bool
    {
        return true;
    }

    /**
     * レビュー作成権限（ログイン必須）
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
         return $user !== null;
    }

    /**
     * レビュー編集権限（投稿者本人のみ）
     *
     * @param User|null $user
     * @param Review $review
     * @return bool
     */
    public function update(?User $user, Review $review): bool
    {
        return $user !== null && $user->id === $review->user_id;
    }

    /**
     * レビュー削除権限（投稿者本人のみ）
     *
     * @param User|null $user
     * @param Review $review
     * @return bool
     */
    public function delete(?User $user, Review $review): bool
    {
        return $user !== null && $user->id === $review->user_id;
    }

    /**
     * レビュー復元（未使用のため false）
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    /**
     * レビュー強制削除（未使用のため false）
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }
}
