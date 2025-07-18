<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Items;
use App\Models\Transactions;

/**
 * Class HomeService.
 */
class HomeService
{
    public function getTotals()
    {
        return [
            'totalCategories' => Category::count(),
            'totalItems' => Items::count(),
            'totalTransactions' => Transactions::count()
        ];
    }
}
