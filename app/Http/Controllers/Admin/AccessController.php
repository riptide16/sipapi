<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function savePermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $request->validate([
            'permission_ids' => 'required',
        ]);

        $permissionIds = explode(',', $request->permission_ids);
        $this->permissionValidator($permissionIds);

        // Merge in browse role permission if super admin
        if ($role->isSuperAdmin()) {
            $permissionIds[] = Permission::where('key', 'browse_roles')->first()->id;
        }
        
        $role->permissions()->sync($permissionIds);

        return new RoleResource($role->load('permissions'));
    }

    protected function permissionValidator($permissionIds)
    {
        foreach ($permissionIds as $permissionId) {
            app('validator')->make(
                ['permission_ids' => $permissionId],
                ['permission_ids' => 'required|exists:permissions,id'],
                [],
                ['permission_ids' => 'permission ' . $permissionId]
            )->validate();
        }
    }
}
