<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function getTotals()
    {
        $totals = $this->homeService->getTotals();

        return response()->json([
            'status' => 200,
            'message' => 'Total Fetched Successfully',
            'totals' => $totals
        ]);
    }
}
