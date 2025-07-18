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
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $queue = 'high'; // High priority for real-time

    public function __construct(Message $message)
    {
        $this->message = $message;

        // Load relationships
        $this->message->load([
            'sender:id,name,avatar',
            'replyTo.sender:id,name',
            'reactions.user:id,name',
            'chat:id,is_group,name'
        ]);

        // Add computed properties
        $this->message->sender_name = $this->message->sender->name;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->toArray()
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    // Handle broadcast failures
    public function failed(\Exception $exception)
    {
        Log::error('MessageSent broadcast failed: ' . $exception->getMessage(), [
            'message_id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'exception' => $exception->getTraceAsString()
        ]);

        // Could implement retry logic or fallback mechanism here
        // For example, save to a failed_broadcasts table for later retry
    }
}
