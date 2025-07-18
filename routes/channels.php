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
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
});

// User presence channel (for online status)
Broadcast::channel('presence.users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar ?? 'default.jpg',
        'email' => $user->email
    ];
});

// Chat-specific presence channel (for typing indicators)
Broadcast::channel('presence.chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Models\Chat::where('id', $chatId)
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->first();

    if ($chat) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? 'default.jpg'
        ];
    }
});

// General notification channel for each user
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

