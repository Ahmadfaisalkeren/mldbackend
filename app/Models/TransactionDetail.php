<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = 'transaction_details';
    protected $guarded = [];
    protected $fillable = [
        'transaction_id',
        'item_id',
        'quantity',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class);
    }

    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
