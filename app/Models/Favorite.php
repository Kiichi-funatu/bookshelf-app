<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
    ];

    /**
     * ユーザー（多対1）
     *
     * @return BelongsTo<User, Favorite>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * 書籍（多対1）
     *
     * @return BelongsTo<Book, Favorite>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
