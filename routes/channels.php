<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Public channels for kitchen/bar dashboards
Broadcast::channel('orders.kitchen', function () {
    return true;
});

Broadcast::channel('orders.bar', function () {
    return true;
});


