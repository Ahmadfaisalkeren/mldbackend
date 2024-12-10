<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transactions';
    protected $guarded = [];
    protected $fillable = [
        'transaction_code',
        'borrower_name',
        'user_id',
        'checkout_date',
        'expected_return_date',
        'actual_return_date',
        'status',
    ];

    protected $casts = [
        'checkout_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }
}
