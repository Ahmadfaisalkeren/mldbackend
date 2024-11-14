<?php

namespace App\Models;

use App\Models\User;
use App\Models\Items;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
