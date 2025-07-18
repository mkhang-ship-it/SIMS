<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Chat;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chats = $user->chats()
            ->with([
                'users:id,name,email,avatar',
                'latestMessage.sender:id,name',
                'creator:id,name'
            ])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->unreadBy($user->id);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required_if:type,text|string|max:1000',
            'type' => 'required|in:text,image,file',
            'reply_to_id' => 'nullable|exists:messages,id',
            'file' => 'required_if:type,image,file|file|max:10240', // 10MB max
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        // Check access
        if (!$chat->users->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $messageData = [
                'chat_id' => $request->chat_id,
                'sender_id' => Auth::id(),
                'type' => $request->type,
                'reply_to_id' => $request->reply_to_id,
            ];

            // Handle file upload
            if ($request->hasFile('file') && in_array($request->type, ['image', 'file'])) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('chat_files', $fileName, 'public');

                $messageData['file_path'] = $filePath;
                $messageData['file_name'] = $file->getClientOriginalName();
                $messageData['file_size'] = $file->getSize();
                $messageData['content'] = $request->content ?: $file->getClientOriginalName();
            } else {
                $messageData['content'] = $request->content;
            }

            // Create message
            $message = Message::create($messageData);

            // Load relationships
            $message->load(['sender:id,name,avatar', 'replyTo.sender:id,name']);

            // Update chat's updated_at timestamp
            $chat->touch();

            // Mark message as read by sender
            $message->markAsReadBy(Auth::id());

            DB::commit();

            // Broadcast event asynchronously
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcast error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'chat_id' => $request->chat_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Send message error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    public function getMessages($chat, Request $request)
    {
        $chatModel = Chat::findOrFail($chat);

        // Check access
        if (!$chatModel->users->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 50);

        $messages = Message::where('chat_id', $chat)
            ->with([
                'sender:id,name,avatar',
                'replyTo.sender:id,name',
                'reactions.user:id,name',
                'readReceipts.user:id,name'
            ])
            ->orderBy('created_at', 'asc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // Mark messages as read
        $unreadMessages = $messages->where('sender_id', '!=', Auth::id());
        foreach ($unreadMessages as $message) {
            $message->markAsReadBy(Auth::id());
        }

        // Add file URLs for messages with files
        foreach ($messages as $message) {
            if ($message->file_path) {
                $message->file_url = asset('storage/' . $message->file_path);
                $message->human_file_size = $this->formatFileSize($message->file_size);
            }
        }

        return response()->json([
            'messages' => $messages,
            'has_more' => $messages->count() === $limit
        ]);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function editMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::findOrFail($request->message_id);

        if (!$message->canEdit()) {
            return response()->json(['error' => 'Cannot edit this message'], 403);
        }

        $message->update([
            'content' => $request->content,
            'edited_at' => now()
        ]);

        $message->load('sender:id,name,avatar');

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function reactToMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'reaction_type' => 'required|in:like,love,haha,wow,sad,angry',
        ]);

        $message = Message::findOrFail($request->message_id);
        $userId = Auth::id();

        // Check if user has access to this chat
        if (!$message->chat->users->contains($userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Remove existing reaction or add new one
            $existingReaction = MessageReaction::where('message_id', $request->message_id)
                ->where('user_id', $userId)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->reaction_type === $request->reaction_type) {
                    // Remove reaction if same type
                    $existingReaction->delete();
                    $action = 'removed';
                } else {
                    // Update reaction type
                    $existingReaction->update(['reaction_type' => $request->reaction_type]);
                    $action = 'updated';
                }
            } else {
                // Add new reaction
                MessageReaction::create([
                    'message_id' => $request->message_id,
                    'user_id' => $userId,
                    'reaction_type' => $request->reaction_type
                ]);
                $action = 'added';
            }

            // Get updated reactions
            $reactions = MessageReaction::where('message_id', $request->message_id)
                ->with('user:id,name')
                ->get()
                ->groupBy('reaction_type');

            return response()->json([
                'success' => true,
                'action' => $action,
                'reactions' => $reactions
            ]);
        } catch (\Exception $e) {
            Log::error('React to message error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to react to message'], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'message_id' => 'required|exists:messages,id',
            ]);

            $message = Message::findOrFail($request->message_id);

            // Check access
            if (!$message->chat->users->contains(Auth::id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $message->markAsReadBy(Auth::id());

            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violations gracefully
            if ($e->getCode() === '23000') {
                // Message is already marked as read, return success
                return response()->json(['success' => true]);
            }

            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark message as read'], 500);
        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark message as read'], 500);
        }
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:2',
            'user_ids.*' => 'uuid|exists:users,id',
            'avatar' => 'nullable|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $chatData = [
                'is_group' => true,
                'name' => $request->name,
                'created_by' => Auth::id(),
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $avatar->getClientOriginalName();
                $avatarPath = $avatar->storeAs('chat_avatars', $avatarName, 'public');
                $chatData['avatar'] = $avatarPath;
            }

            $chat = Chat::create($chatData);

            // Add users to chat (creator is admin by default)
            $userIds = array_unique(array_merge($request->user_ids, [Auth::id()]));

            $chatUsers = [];
            foreach ($userIds as $userId) {
                $chatUsers[$userId] = [
                    'is_admin' => $userId === Auth::id(),
                    'joined_at' => now()
                ];
            }

            $chat->users()->attach($chatUsers);

            $chat->load('users:id,name,email,avatar');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'chat' => $chat
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create group error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create group'], 500);
        }
    }

    public function startPrivateChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        $userId = $request->user_id;
        $authId = Auth::id();

        if ($userId === $authId) {
            return response()->json(['error' => 'Cannot start chat with yourself'], 400);
        }

        // Check if private chat already exists
        $chat = Chat::where('is_group', false)
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('users', function ($q) use ($authId) {
                $q->where('user_id', $authId);
            })
            ->first();

        if (!$chat) {
            try {
                DB::beginTransaction();

                $chat = Chat::create([
                    'is_group' => false,
                    'created_by' => $authId,
                ]);

                $chat->users()->attach([
                    $authId => ['joined_at' => now()],
                    $userId => ['joined_at' => now()]
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Start private chat error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to start private chat'], 500);
            }
        }

        $chat->load('users:id,name,email,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Private chat ready',
            'chat' => $chat
        ]);
    }

    public function deleteMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|uuid|exists:messages,id',
        ]);

        $message = Message::findOrFail($request->message_id);

        if (!$message->canDelete()) {
            return response()->json(['error' => 'Cannot delete this message'], 403);
        }

        try {
            // Delete file if exists
            if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
                Storage::disk('public')->delete($message->file_path);
            }

            $message->delete();

            return response()->json(['success' => true, 'message' => 'Message deleted']);
        } catch (\Exception $e) {
            Log::error('Delete message error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete message'], 500);
        }
    }

    public function searchMessages(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|uuid|exists:chats,id',
            'keyword' => 'required|string',
        ]);

        $chat = Chat::findOrFail($request->chat_id);

        if (!$chat->users->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::where('chat_id', $request->chat_id)
            ->where('content', 'like', '%' . $request->keyword . '%')
            ->with('sender:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function getUsers()
    {
        $users = User::select('id', 'name', 'email', 'avatar')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return response()->json(['users' => $users]);
    }

    public function updateChatSettings(Request $request, $chatId)
    {
        $request->validate([
            'name' => 'required_if:is_group,true|string|max:255',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $chat = Chat::findOrFail($chatId);

        // Check if user is admin
        if ($chat->is_group && !$chat->isAdmin(Auth::id())) {
            return response()->json(['error' => 'Only admins can update group settings'], 403);
        }

        try {
            $updateData = [];

            if ($request->has('name') && $chat->is_group) {
                $updateData['name'] = $request->name;
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($chat->avatar && Storage::disk('public')->exists($chat->avatar)) {
                    Storage::disk('public')->delete($chat->avatar);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $avatar->getClientOriginalName();
                $avatarPath = $avatar->storeAs('chat_avatars', $avatarName, 'public');
                $updateData['avatar'] = $avatarPath;
            }

            $chat->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Chat settings updated',
                'chat' => $chat
            ]);
        } catch (\Exception $e) {
            Log::error('Update chat settings error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update chat settings'], 500);
        }
    }

    public function leaveChat(Request $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);
        $userId = Auth::id();

        if (!$chat->users->contains($userId)) {
            return response()->json(['error' => 'You are not a member of this chat'], 403);
        }

        try {
            DB::beginTransaction();

            if ($chat->is_group) {
                // Remove user from group
                $chat->users()->detach($userId);

                // If creator leaves, transfer ownership to another admin or first member
                if ($chat->created_by === $userId) {
                    $newCreator = $chat->users()->wherePivot('is_admin', true)->first()
                        ?? $chat->users()->first();

                    if ($newCreator) {
                        $chat->update(['created_by' => $newCreator->id]);
                        // Make sure new creator is admin
                        $chat->users()->updateExistingPivot($newCreator->id, ['is_admin' => true]);
                    }
                }

                // If no members left, delete the chat
                if ($chat->users()->count() === 0) {
                    $chat->delete();
                }
            } else {
                return response()->json(['error' => 'Cannot leave private chat'], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Left chat successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave chat error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to leave chat'], 500);
        }
    }

    public function addMembersToGroup(Request $request, $chatId)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'uuid|exists:users,id',
        ]);

        $chat = Chat::findOrFail($chatId);

        if (!$chat->is_group) {
            return response()->json(['error' => 'Can only add members to group chats'], 400);
        }

        if (!$chat->isAdmin(Auth::id())) {
            return response()->json(['error' => 'Only admins can add members'], 403);
        }

        try {
            DB::beginTransaction();

            $existingUserIds = $chat->users()->pluck('user_id')->toArray();
            $newUserIds = array_diff($request->user_ids, $existingUserIds);

            if (empty($newUserIds)) {
                return response()->json(['error' => 'All selected users are already members'], 400);
            }

            $chatUsers = [];
            foreach ($newUserIds as $userId) {
                $chatUsers[$userId] = [
                    'is_admin' => false,
                    'joined_at' => now()
                ];
            }

            $chat->users()->attach($chatUsers);

            DB::commit();

            $chat->load('users:id,name,email,avatar');

            return response()->json([
                'success' => true,
                'message' => 'Members added successfully',
                'chat' => $chat
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add members error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add members'], 500);
        }
    }

    public function auth(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 403);
        }
        return \Illuminate\Support\Facades\Broadcast::auth($request);
    }
}