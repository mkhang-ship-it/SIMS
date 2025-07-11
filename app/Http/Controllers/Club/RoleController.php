<?php
// RoleController - Updated to support new unique constraint on name+club_id
// Fixed store and update methods to properly handle club_id and validation

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $clubId = Auth::user()->club_id;
        $roles = Role::with(['permissions.module', 'users'])->where('type', 'branch')->where('club_id', $clubId)->orderBy('created_at', 'desc')->get();

        return view('clubs.roles.index', compact('roles'));
    }

    public function create()
    {
        $modules = Module::with('permissions')->orderBy('name')->where('type', 'branch')->get();
        $permissions = Permission::with('module')->orderBy('module_id')->orderBy('name')->get();

        return view('clubs.roles.create', compact('modules', 'permissions'));
    }

    public function store(Request $request)
    {
        $clubId = Auth::user()->club_id;

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($clubId) {
                    return $query->where('club_id', $clubId);
                })
            ],
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_leader' => 'required|string|in:leader,member',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'type' => 'branch',
            'club_id' => $clubId,
            'status' => $request->status,
            'is_leader' => $request->is_leader,
            'is_default' => false,
            'is_admin' => false,
        ]);

        // Attach permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('backend.club.roles.index')->with('success', 'Vai trò đã được tạo thành công!');
    }

    public function edit(Role $role)
    {
        $modules = Module::with('permissions')->orderBy('name')->where('type', 'branch')->get();
        $permissions = Permission::with('module')->orderBy('module_id')->orderBy('name')->get();
        $role->load(['permissions.module', 'users']);


        return view('clubs.roles.edit', compact('role', 'modules', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $clubId = Auth::user()->club_id;

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($clubId) {
                    return $query->where('club_id', $clubId);
                })->ignore($role->id)
            ],
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_leader' => 'required|string|in:leader,member',
        ]);

        $role->update([
            'name' => $request->name,
            'status' => $request->status,
            'is_leader' => $request->is_leader,
        ]);

        // Sync permissions
        $permissions = $request->has('permissions') ? $request->permissions : [];
        $role->permissions()->sync($permissions);

        return redirect()->route('backend.club.roles.index')->with('success', 'Vai trò đã được cập nhật thành công!');
    }

    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('backend.club.roles.index')->with('error', 'Không thể xóa vai trò này vì đã có người dùng sử dụng!');
        }

        if ($role->is_default) {
            return redirect()->route('backend.club.roles.index')->with('error', 'Không thể xóa vai trò mặc định!');
        }

        // Detach all permissions
        $role->permissions()->detach();

        $role->delete();

        return redirect()->route('backend.club.roles.index')->with('success', 'Vai trò đã được xóa thành công!');
    }
}
