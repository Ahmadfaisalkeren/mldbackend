<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Http\Requests\Transaction\StoreTransactionRequest;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $transactions = $this->transactionService->getTransactions();

        return response()->json([
            'status' => 200,
            'message' => "Transactions Fetched Successfully",
            'transactions' => $transactions,
        ], 200);
    }

    public function getOngoingTransactions()
    {
        $transactions = $this->transactionService->getOngoingTransactions();

        return response()->json([
            'status' => 200,
            'message' => "Ongoing Transactions Fetched Successfully",
            'transactions' => $transactions,
        ], 200);
    }

    public function totalTransactions()
    {
        $totalTransactions = $this->transactionService->getTotalTransactions();

        return response()->json([
            'status' => 200,
            'message' => "Total Transactions Fetched Successfully",
            'totalTransactions' => $totalTransactions,
        ], 200);
    }

    public function checkout(StoreTransactionRequest $request)
    {
        try {
            $transaction = $this->transactionService->proceedToCheckout($request->validated());
            return response()->json([
                'status' => 200,
                'message' => 'Transaction Checkout Successfully',
                'transaction' => $transaction
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Transaction Checkout Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showTransactionDetails($transactionId)
    {
        $transaction = $this->transactionService->getTransactionsDetails($transactionId);

        return response()->json([
            'status' => 200,
            'message' => 'Transaction Details Fetched Successfully',
            'transaction' => $transaction
        ], 200);
    }

    public function returnItems($transactionId)
    {
        try {
            $transaction = $this->transactionService->returnItems($transactionId);
            return response()->json([
                'status' => 200,
                'message' => 'Transaction Return Successfully',
                'transaction' => $transaction
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Transaction Return Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateTransactionsPDF(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (($startDate && $endDate) && (!Carbon::parse($startDate)->isValid() || !Carbon::parse($endDate)->isValid())) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid date format.',
            ], 400);
        }

        try {
            if ($startDate && $endDate) {
                $pdfPath = $this->transactionService->generateFilteredTransactionsPDF($startDate, $endDate);
            } else {
                $pdfPath = $this->transactionService->generateAllTransactionsPDF();
            }

            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="transactions_report.pdf"',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error generating PDF.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
