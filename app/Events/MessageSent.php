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
    public $queue = 'high'; // Ưu tiên cao cho real-time

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->message->load('sender');
        $this->message->sender_name = $this->message->sender->name;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message
        ];
    }

    // Tự động retry nếu broadcast fail
    public function failed(\Exception $exception)
    {
        Log::error('MessageSent broadcast failed: ' . $exception->getMessage());

        // Có thể thêm logic retry hoặc fallback mechanism
        // Ví dụ: lưu vào queue để retry sau
    }
}
