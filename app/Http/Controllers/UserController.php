<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('type', 'central')->orderBy('created_at', 'desc')->get();

        $roles = Role::where('type', 'central')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'central',
                'status' => $request->status,
                'role_id' => $request->role_id,
            ]);

            DB::commit();

            return redirect()->route('backend.users.index')->with('success', 'Người dùng đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo người dùng: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'status' => 'required|in:active,inactive',
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'type' => 'central',
                'status' => $request->status,
                'role_id' => $request->role_id,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed'
                ]);
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            DB::commit();

            return redirect()->route('backend.users.index')->with('success', 'Thông tin người dùng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật người dùng: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Check if user is trying to delete themselves
            if ($user->id === Auth::id()) {
                return redirect()->back()->with('error', 'Không thể xóa tài khoản của chính mình!');
            }

            $user->bankAccount()->delete();
            $user->address()->delete();

            // Delete user sessions
            DB::table('sessions')->where('user_id', $user->id)->delete();

            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('backend.users.index')->with('success', 'Người dùng đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa người dùng: ' . $e->getMessage());
        }
    }
}
