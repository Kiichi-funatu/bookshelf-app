<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ReadingPlanStatus;

class ReadingPlan extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'due_date',
        'status', // planned / completed / expired
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => \App\Enums\ReadingPlanStatus::class,
        'due_date' => 'date',
        'completed_at' => 'date',
    ];

    /**
     * ユーザー（多対1）
     *
     * @return BelongsTo<User, ReadingPlan>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 書籍（多対1）
     *
     * @return BelongsTo<Book, ReadingPlan>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * 期日（アクセサ）
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getTargetDateAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->due_date;
    }
}
