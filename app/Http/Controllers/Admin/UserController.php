<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin;
use Illuminate\Http\Request;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Resource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with(['role', 'region'])->filter($request->all());
        $users = $request->has('per_page') && $request->per_page <= -1
            ? $users->get()
            : $users->paginate($request->per_page ?? 20)->withQueryString();

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Admin\CreateUserRequest $request)
    {
        $user = User::create(array_merge($request->all(), [
            'status' => User::STATUS_ACTIVE,
        ]));

        return new UserResource($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['role', 'region'])->findOrFail($id);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Admin\UpdateUserRequest $request, $id)
    {
        $user = $request->user;

        $data = $request->all();

        // Unset password to prevent updating to empty password
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
                return new ErrorResource(__('Anda tidak diizinkan membuat super admin'), 400);
            }
        }

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->institutionRequests()->exists()) {
            return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
        }
        if ($user->evaluations()->exists()) {
            return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
        }
        if (ActivityLog::where('causer_id', $user->id)->whereIn('subject_type', [
            App\Models\InstrumentComponent::class, App\Models\InstrumentAspect::class,
        ])->where('description', 'created')->exists()) {
            return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
        }

        $user->delete();

        return new Resource();
    }
}
