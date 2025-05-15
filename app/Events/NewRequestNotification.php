<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRequestNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('requests')
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'user_name' => $this->notification->user->first_name . ' ' . $this->notification->user->last_name,
                'user_image' => $this->notification->user->profile_picture,
                'type' => $this->notification->request->type,
                'date' => $this->notification->request->date,
                'created_at' => $this->notification->created_at->diffForHumans()
            ]
        ];
    }
}
