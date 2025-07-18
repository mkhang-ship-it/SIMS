<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $reaction;
    public $user;

    public function __construct(Message $message, $reaction, $user)
    {
        $this->message = $message;
        $this->reaction = $reaction;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'reaction' => $this->reaction,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar
            ]
        ];
    }

    public function broadcastAs()
    {
        return 'MessageReactionAdded';
    }
}
