<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Builder;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chats = $user->chats()->with(['users', 'messages.sender'])->get();

        return view('chat.index', compact('chats'));
    }

    public function fetchMessages($chatId)
    {
        $chat = Chat::with(['messages.sender'])->findOrFail($chatId);

        return response()->json($chat->messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|uuid|exists:chats,id',
            'content' => 'nullable|string',
            'type' => 'required|in:text,image,file',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'chat_id' => $request->chat_id,
            'content' => $request->content,
            'type' => $request->type,
            'file_path' => $filePath,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'Message sent', 'message' => $message]);
    }

    /**
     * Tạo nhóm chat mới
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:2',
            'user_ids.*' => 'uuid|exists:users,id',
        ]);

        $chat = Chat::create([
            'is_group' => true,
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);
        $chat->users()->attach(array_merge($request->user_ids, [Auth::id()]));
        $chat->load('users');
        return response()->json(['status' => 'Group created', 'chat' => $chat]);
    }

    /**
     * Chọn user để nhắn tin (tạo chat riêng nếu chưa có)
     */
    public function startPrivateChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);
        $userId = $request->user_id;
        $authId = Auth::id();
        $chat = Chat::where('is_group', false)
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('users', function ($q) use ($authId) {
                $q->where('user_id', $authId);
            })
            ->first();
        if (!$chat) {
            $chat = Chat::create([
                'is_group' => false,
                'created_by' => $authId,
            ]);
            $chat->users()->attach([$authId, $userId]);
        }
        $chat->load('users');
        return response()->json(['status' => 'Private chat ready', 'chat' => $chat]);
    }

    /**
     * Xóa tin nhắn (soft delete)
     */
    public function deleteMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|uuid|exists:messages,id',
        ]);
        $message = Message::where('id', $request->message_id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
        $message->delete();
        return response()->json(['status' => 'Message deleted']);
    }

    /**
     * Tìm kiếm đoạn chat theo từ khóa
     */
    public function searchMessages(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|uuid|exists:chats,id',
            'keyword' => 'required|string',
        ]);
        $messages = Message::where('chat_id', $request->chat_id)
            ->where(function (Builder $q) use ($request) {
                $q->where('content', 'like', '%' . $request->keyword . '%');
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['messages' => $messages]);
    }
}
