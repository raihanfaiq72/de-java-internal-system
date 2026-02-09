<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserOfficeRole;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification to users who have access to a specific permission/module.
     *
     * @param string $permissionName The name of the permission (e.g., 'sales.index')
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $url Target URL
     * @param string $type Notification type (info, success, warning, error)
     * @return void
     */
    public static function notifyByPermission($permissionName, $title, $message, $url = '#', $type = 'info', $data = [])
    {
        // 1. Find the permission ID
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            return;
        }

        // 2. Find roles that have this permission
        $roleIds = RolePermission::where('permission_id', $permission->id)
            ->pluck('role_id')
            ->toArray();

        if (empty($roleIds)) {
            return;
        }

        // 3. Find users who have these roles
        $userIds = UserOfficeRole::whereIn('role_id', $roleIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (empty($userIds)) {
            return;
        }

        // 4. Get User models
        $users = \App\Models\User::whereIn('id', $userIds)->get();

        // 5. Send notification
        Notification::send($users, new SystemNotification($title, $message, $url, $type, $data));
    }
}
