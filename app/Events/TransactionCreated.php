<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction->load(['transactionDetails', 'user']);

        $details = $this->transaction->transactionDetails;
        $firstItemName = $this->transaction->transactionDetails->first()?->item->name ?? 'sebuah item';
        $otherCount = $details->count() - 1;

        $message = $this->transaction->borrower_name . ' baru saja menyewa ' . $firstItemName;

        if ($otherCount > 0) {
            $message .= ' dan ' . $otherCount . ' item lainnya';
        }

        $message .= ' yang ditangani oleh ' . $transaction->user->name;

        Notification::create([
            'user_id' => $transaction->user_id,
            'message' => $message,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('transaction-created');
    }

    public function broadcastWith()
    {
        return [
            'transaction' => $this->transaction,
        ];
    }
}
