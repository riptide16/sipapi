<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::all();

        return new PermissionCollection($permissions);
    }

    public function show(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        return new PermissionResource($permission);
    }
}
