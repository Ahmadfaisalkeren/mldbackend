<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $guarded = [];
    protected $casts = [
        'images' => 'array',
    ];
    protected $fillable = [
        'item_code',
        'name',
        'category_id',
        'description',
        'size',
        'stock',
        'images',
        'qrcode',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'item_id', 'id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
