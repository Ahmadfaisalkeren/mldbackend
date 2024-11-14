<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Transactions;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class TransactionService.
 */
class TransactionService
{
    public function getOngoingTransactions()
    {
        $transactions = Transactions::with('user')->where('status', 'Checkout')->get();

        return $transactions;
    }
    public function getTransactions()
    {
        $transactions = Transactions::with('user')->get();

        return $transactions;
    }

    public function getTotalTransactions()
    {
        $totalTransactions = Transactions::count();

        return $totalTransactions;
    }

    public function proceedToCheckout(array $transactionData)
    {
        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $checkoutDate = Carbon::now();
            $expectedReturnDate = $this->calculateExpectedReturnDate($checkoutDate);

            $transaction = Transactions::create([
                'transaction_code' => $this->generateTransactionCode(),
                'borrower_name' => $transactionData['borrower_name'],
                'user_id' => $userId,
                'checkout_date' => $checkoutDate,
                'expected_return_date' => $expectedReturnDate,
                'status' => 'Checkout',
            ]);

            $cartItems = Cart::where('user_id', $userId)->get();

            foreach ($cartItems as $cartItem) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $cartItem->item_id,
                    'quantity' => $cartItem->quantity,
                ]);

                $cartItem->delete();
            }

            DB::commit();
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to proceed to checkout: " . $e->getMessage());
        }
    }

    private function calculateExpectedReturnDate($checkoutDate)
    {
        return Carbon::parse($checkoutDate)->addDays(7)->toDateString();
    }

    private function generateTransactionCode()
    {
        $count = Transactions::count() + 1;
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);

        return 'TRX-' . $formattedNumber;
    }

    public function getTransactionsDetails($transactionId)
    {
        $transaction = Transactions::with(['transactionDetails.item', 'user'])->find($transactionId);

        if (!$transaction) {
            throw new Exception("Transaction not found");
        }

        return $transaction;
    }

    public function returnItems($transactionId)
    {
        $transaction = Transactions::findOrFail($transactionId);

        // Optional: Check if transaction status is eligible for return
        if ($transaction->status !== 'Checkout') {
            throw new Exception("Transaction cannot be returned because it is not in a 'Checkout' status.");
        }

        DB::beginTransaction();
        try {
            $transaction->actual_return_date = Carbon::now();
            $transaction->status = 'Returned';

            $transaction->save();
            DB::commit();

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update return status: " . $e->getMessage());
        }
    }
}
