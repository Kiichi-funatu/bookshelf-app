<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewLike extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'review_id',
    ];

    /**
     * ユーザー（多対1）
     *
     * @return BelongsTo<User, ReviewLike>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * レビュー（多対1）
     *
     * @return BelongsTo<Review, ReviewLike>
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
