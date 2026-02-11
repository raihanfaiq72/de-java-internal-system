<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $deliveryOrderId;
    public $driverId;
    public $latitude;
    public $longitude;

    /**
     * Create a new event instance.
     */
    public function __construct($deliveryOrderId, $driverId, $latitude, $longitude)
    {
        $this->deliveryOrderId = $deliveryOrderId;
        $this->driverId = $driverId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('delivery-order.' . $this->deliveryOrderId),
        ];
    }

    public function broadcastAs()
    {
        return 'driver.location.updated';
    }
}
