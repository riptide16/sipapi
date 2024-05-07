<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Rules\Alphaspace;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Resource;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin()) {
            $roles = Role::where('name', '<>', Role::SUPER_ADMIN)->get();
        } else {
            $roles = Role::all();
        }

        return new RoleCollection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => ['required', new Alphaspace(), 'min:3', 'max:191'],
        ]);
        $data = $request->all();

        $name = Str::slug($request->display_name, '_');
        if (Role::where('name', $name)->exists()) {
            return new ErrorResource(['display_name' => ['Name already exists.']], 422);
        }

        $role = new Role($data);
        $role->name = $name;
        $role->save();

        return new RoleResource($role, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'display_name' => ['sometimes', new Alphaspace(), 'min:3', 'max:191'],
        ]);

        $role->update($request->all());

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->isAdmin()) {
            return new ErrorResource(__('errors.remove_admin'), 400, 'ERR4300');
        }

        $role->permissions()->sync([]);
        $role->delete();

        return new Resource();
    }
}
