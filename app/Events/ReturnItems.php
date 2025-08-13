<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReturnItems implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $returnItems;

    /**
     * Create a new event instance.
     */
    public function __construct($returnItems)
    {
        $this->returnItems = $returnItems->load(['transactionDetails', 'user']);

        $details = $this->returnItems->transactionDetails;
        $firstItemName = $this->returnItems->transactionDetails->first()?->item->name ?? 'sebuah item';
        $otherCount = $details->count() - 1;

        $message = $this->returnItems->borrower_name . ' baru saja mengembalikan ' . $firstItemName;

        if ($otherCount > 0) {
            $message .= ' dan ' . $otherCount . ' item lainnya';
        }

        $message .= ' yang ditangani oleh ' . $returnItems->user->name;

        Notification::create([
            'user_id' => $returnItems->user_id,
            'message' => $message
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('return-items');
    }

    public function broadcastWith()
    {
        return [
            'returnItems' => $this->returnItems,
        ];
    }
}
