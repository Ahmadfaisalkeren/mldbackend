<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'message',
        'is_read',
    ];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
