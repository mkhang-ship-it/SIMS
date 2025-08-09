<?php
// RefreshPermissions Command - Tối ưu hóa lại toàn bộ logic backup/restore permissions
// Updated: Tối ưu hóa logic backup/restore để đảm bảo tất cả permissions được giữ nguyên
// Updated: Sửa lỗi phân biệt permissions giữa admin (central) và club (branch) để tránh nhầm lẫn khi restore
// Updated: Thêm tính năng xóa hết tất cả bản ghi cũ trước khi refresh permissions (giữ nguyên roles để tránh lỗi foreign key)
// Updated: Sửa lỗi backup/restore permissions bằng cách lưu module_code và permission_name thay vì permission_id
// Created: Command để refresh permissions từ seeders mà không làm mất phân quyền đã gán cho user

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserClubRole;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\ModuleClubSeeder;
use Illuminate\Support\Facades\DB;

class RefreshPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:refresh {--type=all : Type of permissions to refresh (admin, club, all)} {--clear-all : Clear all existing records before refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh permissions from seeders while preserving existing user and role permissions';

    /**
     * Backup data structure
     *
     * @var array
     */
    private $backupData = [
        'admin_roles' => [],
        'club_roles' => [],
        'admin_users' => [],
        'club_users' => []
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $clearAll = $this->option('clear-all');

        $this->info('Starting permission refresh process...');

        try {
            // Step 1: Backup all existing permissions
            $this->backupAllPermissions();

            // Step 2: Clear existing data if requested
            if ($clearAll) {
                $this->clearExistingData();
            }

            // Step 3: Refresh permissions based on type
            if ($type === 'all' || $type === 'admin') {
                $this->refreshAdminPermissions();
            }

            if ($type === 'all' || $type === 'club') {
                $this->refreshClubPermissions();
            }

            // Step 4: Restore all permissions
            $this->restoreAllPermissions();

            $this->info('Permission refresh completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error during permission refresh: ' . $e->getMessage());
            $this->error('Please check the logs for more details.');
            return 1;
        }

        return 0;
    }

    /**
     * Backup all existing permissions (roles and users)
     */
    private function backupAllPermissions()
    {
        $this->info('Backing up all existing permissions...');

        // Backup admin roles and their permissions
        $adminRoles = Role::with(['permissions.module'])->where('type', 'central')->get();
        foreach ($adminRoles as $role) {
            $this->backupData['admin_roles'][$role->id] = [
                'role_name' => $role->name,
                'permissions' => $this->extractPermissions($role->permissions)
            ];
        }

        // Backup club roles and their permissions
        $clubRoles = Role::with(['permissions.module'])->where('type', 'branch')->get();
        foreach ($clubRoles as $role) {
            $this->backupData['club_roles'][$role->id] = [
                'role_name' => $role->name,
                'permissions' => $this->extractPermissions($role->permissions)
            ];
        }

        // Backup admin users and their role permissions
        $adminUsers = User::with(['role.permissions.module'])->whereHas('role', function ($query) {
            $query->where('type', 'central');
        })->get();

        foreach ($adminUsers as $user) {
            if ($user->role) {
                $this->backupData['admin_users'][$user->id] = [
                    'role_id' => $user->role_id,
                    'permissions' => $this->extractPermissions($user->role->permissions)
                ];
            }
        }

        // Backup club users and their role permissions
        $clubUserRoles = UserClubRole::with(['role.permissions.module'])->get();
        foreach ($clubUserRoles as $userClubRole) {
            if ($userClubRole->role) {
                $key = $userClubRole->user_id . '_' . $userClubRole->club_id;
                $this->backupData['club_users'][$key] = [
                    'user_id' => $userClubRole->user_id,
                    'club_id' => $userClubRole->club_id,
                    'role_id' => $userClubRole->role_id,
                    'permissions' => $this->extractPermissions($userClubRole->role->permissions)
                ];
            }
        }

        $this->info('Backup completed successfully.');
    }

    /**
     * Extract permissions from collection to standardized format
     */
    private function extractPermissions($permissions)
    {
        $extracted = [];
        foreach ($permissions as $permission) {
            if ($permission->module) {
                $extracted[] = [
                    'module_code' => $permission->module->module_code,
                    'permission_name' => $permission->name,
                    'display_name' => $permission->display_name
                ];
            }
        }
        return $extracted;
    }

    /**
     * Clear existing data (modules, permissions, role_permissions, user_club_roles)
     */
    private function clearExistingData()
    {
        $this->info('Clearing existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all related tables but keep roles
        DB::table('role_permissions')->truncate();
        DB::table('user_club_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('modules')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Existing data cleared successfully.');
    }

    /**
     * Refresh admin permissions
     */
    private function refreshAdminPermissions()
    {
        $this->info('Refreshing admin permissions...');

        // Clear existing admin modules and permissions
        Module::where('type', 'central')->delete();

        // Run ModuleSeeder to recreate admin modules and permissions
        $seeder = new ModuleSeeder();
        $seeder->run();

        $this->info('Admin permissions refreshed.');
    }

    /**
     * Refresh club permissions
     */
    private function refreshClubPermissions()
    {
        $this->info('Refreshing club permissions...');

        // Clear existing club modules and permissions
        Module::where('type', 'branch')->delete();

        // Run ModuleClubSeeder to recreate club modules and permissions
        $seeder = new ModuleClubSeeder();
        $seeder->run();

        $this->info('Club permissions refreshed.');
    }

    /**
     * Restore all permissions from backup
     */
    private function restoreAllPermissions()
    {
        $this->info('Restoring all permissions from backup...');

        // Step 1: Restore role permissions first
        $this->restoreRolePermissions();

        // Step 2: Restore user role assignments
        $this->restoreUserRoleAssignments();

        $this->info('All permissions restored successfully.');
    }

    /**
     * Restore role permissions
     */
    private function restoreRolePermissions()
    {
        // Restore admin role permissions
        foreach ($this->backupData['admin_roles'] as $roleId => $roleData) {
            $role = Role::find($roleId);
            if ($role) {
                $permissionIds = $this->findPermissionIds($roleData['permissions'], 'central');
                if (!empty($permissionIds)) {
                    $role->permissions()->sync($permissionIds);
                    $this->info("Restored {$roleData['role_name']} admin role permissions: " . count($permissionIds) . " permissions");
                }
            }
        }

        // Restore club role permissions
        foreach ($this->backupData['club_roles'] as $roleId => $roleData) {
            $role = Role::find($roleId);
            if ($role) {
                $permissionIds = $this->findPermissionIds($roleData['permissions'], 'branch');
                if (!empty($permissionIds)) {
                    $role->permissions()->sync($permissionIds);
                    $this->info("Restored {$roleData['role_name']} club role permissions: " . count($permissionIds) . " permissions");
                }
            }
        }
    }

    /**
     * Restore user role assignments
     */
    private function restoreUserRoleAssignments()
    {
        // Restore admin user role assignments
        foreach ($this->backupData['admin_users'] as $userId => $userData) {
            $user = User::find($userId);
            if ($user && $user->role) {
                $permissionIds = $this->findPermissionIds($userData['permissions'], 'central');
                if (!empty($permissionIds)) {
                    $user->role->permissions()->sync($permissionIds);
                    $this->info("Restored admin user {$userId} permissions: " . count($permissionIds) . " permissions");
                }
            }
        }

        // Restore club user role assignments
        foreach ($this->backupData['club_users'] as $key => $userData) {
            $userClubRole = UserClubRole::where('user_id', $userData['user_id'])
                ->where('club_id', $userData['club_id'])
                ->first();

            if ($userClubRole && $userClubRole->role) {
                $permissionIds = $this->findPermissionIds($userData['permissions'], 'branch');
                if (!empty($permissionIds)) {
                    $userClubRole->role->permissions()->sync($permissionIds);
                    $this->info("Restored club user {$userData['user_id']} in club {$userData['club_id']} permissions: " . count($permissionIds) . " permissions");
                }
            }
        }
    }

    /**
     * Find permission IDs by module code and permission name with type filter
     */
    private function findPermissionIds($permissions, $type)
    {
        $permissionIds = [];

        foreach ($permissions as $permission) {
            $foundPermission = Permission::whereHas('module', function ($query) use ($permission, $type) {
                $query->where('module_code', $permission['module_code'])
                    ->where('type', $type);
            })->where('name', $permission['permission_name'])->first();

            if ($foundPermission) {
                $permissionIds[] = $foundPermission->id;
            } else {
                $this->warn("Permission not found: {$permission['module_code']}.{$permission['permission_name']} (type: {$type})");
            }
        }

        return $permissionIds;
    }
}
