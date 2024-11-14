<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = [];
    protected $fillable = [
        'category_name'
    ];

    public function items()
    {
        return $this->hasMany(Items::class, 'category_id', 'id');
    }
}
