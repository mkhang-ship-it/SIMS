<?php
// RoleController - Updated to use separate pages for create and edit instead of modals
// Handles CRUD operations for roles with module-based permission management

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions.module', 'users'])->where('type', 'central')->orderBy('created_at', 'desc')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $modules = Module::with('permissions')->orderBy('name')->where('type', 'central')->get();
        $permissions = Permission::with('module')->orderBy('module_id')->orderBy('name')->get();

        return view('roles.create', compact('modules', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Attach permissions if provided
        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('backend.roles.index')->with('success', 'Vai trò đã được tạo thành công!');
    }

    public function edit(Role $role)
    {
        $modules = Module::with('permissions')->orderBy('name')->where('type', 'central')->get();
        $permissions = Permission::with('module')->orderBy('module_id')->orderBy('name')->get();
        $role->load(['permissions.module', 'users']);


        return view('roles.edit', compact('role', 'modules', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Sync permissions
        $permissions = $request->has('permissions') ? $request->permissions : [];
        $role->permissions()->sync($permissions);

        return redirect()->route('backend.roles.index')->with('success', 'Vai trò đã được cập nhật thành công!');
    }

    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('backend.roles.index')->with('error', 'Không thể xóa vai trò này vì đã có người dùng sử dụng!');
        }

        if ($role->is_default) {
            return redirect()->route('backend.roles.index')->with('error', 'Không thể xóa vai trò mặc định!');
        }

        // Detach all permissions
        $role->permissions()->detach();

        $role->delete();

        return redirect()->route('backend.roles.index')->with('success', 'Vai trò đã được xóa thành công!');
    }
}
