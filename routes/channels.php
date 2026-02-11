<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\DeliveryOrder;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('delivery-order.{id}', function ($user, $id) {
    // Check if user belongs to the same office as the delivery order
    // Or if the user is the driver
    $order = DeliveryOrder::find($id);
    if (!$order) return false;
    
    return $user->office_id === $order->office_id;
});
