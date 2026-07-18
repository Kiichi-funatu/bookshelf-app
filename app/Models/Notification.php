<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // 既読チェック
    public function isRead()
    {
        return $this->read_at !== null;
    }
}
