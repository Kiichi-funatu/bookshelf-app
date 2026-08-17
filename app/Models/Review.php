<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Review extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'user_id',
        'rating',
        'comment',
    ];

    /**
     * 書籍（多対1）
     *
     * @return BelongsTo<Book, Review>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * 投稿者（多対1）
     *
     * @return BelongsTo<User, Review>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * いいねしたユーザー一覧（多対多）
     *
     * @return BelongsToMany<User>
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_likes');
    }

    /**
     * 自分がいいねしているか判定
     *
     * @param User $user
     * @return bool
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * いいね（多対多）
     *
     * @return BelongsToMany<User>
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_likes');
    }
}
