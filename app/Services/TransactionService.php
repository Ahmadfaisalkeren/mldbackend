<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Items;
use App\Models\Transactions;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
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
        $transactions = Transactions::with('user')->orderBy('created_at', 'desc')->get();

        return $transactions;
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

                $item = Items::find($cartItem->item_id);

                if (!$item) {
                    throw new Exception("Item not found");
                }

                if ($item->stock < $cartItem->quantity) {
                    throw new Exception("Insufficient stock for item: " . $item->name);
                }

                $item->stock -= $cartItem->quantity;
                $item->save();

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

        if ($transaction->status !== 'Checkout') {
            throw new Exception("Transaction cannot be returned because it is not in a 'Checkout' status.");
        }

        DB::beginTransaction();

        try {

            $transactionDetails = TransactionDetail::where('transaction_id', $transactionId)->get();

            foreach ($transactionDetails as $detail) {
                $item = Items::find($detail->item_id);

                if (!$item) {
                    throw new Exception("Item not found");
                }

                $item->stock += $detail->quantity;
                $item->save();
            }

            $transaction->actual_return_date = Carbon::now();
            $transaction->status = 'Returned';

            $transaction->save();

            DB::commit();

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to return items: " . $e->getMessage());
        }
    }

    public function generateFilteredTransactionsPDF($startDate, $endDate)
    {
        $transactions = Transactions::with('user')
            ->whereBetween('checkout_date', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('transactions/transaction_pdf', compact('transactions'))->render();

        collect(glob(storage_path('app/public/reports/transactions_report_*.pdf')))
            ->each(fn($file) => @unlink($file));

        $filename = 'transactions_report_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = storage_path("app/public/reports/{$filename}");

        Browsershot::html($html)
            ->setOption('executablePath', 'C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->addChromiumArguments([
                '--disable-dev-shm-usage',
                '--no-sandbox',
            ])
            ->format('A4')
            ->waitUntilNetworkIdle()
            ->showBackground()
            ->save($pdfPath);

        return $pdfPath;
    }

    public function generateAllTransactionsPDF()
    {
        $transactions = Transactions::with('user')->orderBy('created_at', 'desc')->get();

        $html = view('transactions/transaction_pdf', compact('transactions'))->render();

        collect(glob(storage_path('app/public/reports/transactions_report_*.pdf')))
            ->each(fn($file) => @unlink($file));

        $filename = 'transactions_report_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = storage_path("app/public/reports/{$filename}");

        Browsershot::html($html)
            ->setOption('executablePath', 'C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->addChromiumArguments([
                '--disable-dev-shm-usage',
                '--no-sandbox',
            ])
            ->format('A4')
            ->waitUntilNetworkIdle()
            ->showBackground()
            ->save($pdfPath);

        return $pdfPath;
    }
}
