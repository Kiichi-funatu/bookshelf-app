<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'due_date',
        'status', // planned / completed / expired
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 書籍
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
