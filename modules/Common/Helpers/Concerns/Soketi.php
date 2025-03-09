<?php

namespace Dragonite\Common\Helpers\Concerns;

use Pusher\Pusher;

trait Soketi
{
    public function sendNotification(array $data, string $channel, string $event = 'notification'): void
    {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
        $pusher->trigger($channel, $event, $data);
    }
}
