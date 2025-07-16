<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Private chat channel authorization
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Check if user is member of this chat
    return \App\Models\Chat::where('id', $chatId)
        ->whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
});

// User presence channel (optional)
Broadcast::channel('presence.chat.{chatId}', function ($user, $chatId) {
    if (\App\Models\Chat::where('id', $chatId)
        ->whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? 'default.jpg'
        ];
    }
});

