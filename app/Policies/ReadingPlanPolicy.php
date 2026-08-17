<?php

namespace App\Policies;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReadingPlanPolicy
{
    /**
     * 読書計画一覧の閲覧権限（ログイン必須）
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * 読書計画の閲覧権限（本人のみ）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function view(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    /**
     * 読書計画の作成権限（ログイン必須）
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 読書計画の更新権限（本人のみ）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function update(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    /**
     * 読書計画の削除権限（本人のみ）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function delete(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    /**
     * 読了ボタン（complete）の権限（本人のみ）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function complete(User $user, ReadingPlan $plan): bool
    {
        return $user->id === $plan->user_id;
    }

    /**
     * 読書計画の復元（未使用のため false）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function restore(User $user, ReadingPlan $readingPlan): bool
    {
        return false;
    }

    /**
     * 読書計画の強制削除（未使用のため false）
     *
     * @param User $user
     * @param ReadingPlan $plan
     * @return bool
     */
    public function forceDelete(User $user, ReadingPlan $readingPlan): bool
    {
        return false;
    }
}
