<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $interestPayment;
    public $principalPayment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Payment $interestPayment = null, Payment $principalPayment = null)
    {
        $this->interestPayment = $interestPayment;
        $this->principalPayment = $principalPayment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
