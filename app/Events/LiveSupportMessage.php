<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveSupportMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        // Create secure private channels based on conversation participants
        return [
            new PrivateChannel('live-support.user.' . $this->data['user_id']),
            new PrivateChannel('live-support.admin.' . $this->data['topic_id'])
        ];
    }

    public function broadcastAs()
    {
        return 'live-support-message';
    }

    public function broadcastWith()
    {
        return [
            'data' => $this->data
        ];
    }
}
