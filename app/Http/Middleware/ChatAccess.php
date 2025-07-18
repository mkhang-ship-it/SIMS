<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Chat;

class ChatAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $chatId = $request->route('chat') ?? $request->route('chatId') ?? $request->input('chat_id');
        
        if ($chatId) {
            $chat = Chat::find($chatId);
            
            if (!$chat) {
                return response()->json(['error' => 'Chat not found'], 404);
            }
            
            // Check if user is a member of this chat
            if (!$chat->users->contains(auth()->id())) {
                return response()->json(['error' => 'Unauthorized access to chat'], 403);
            }
            
            // Add chat to request for easy access in controllers
            $request->merge(['currentChat' => $chat]);
        }

        return $next($request);
    }
}
