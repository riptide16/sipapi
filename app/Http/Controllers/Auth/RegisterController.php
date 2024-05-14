<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $role = Role::where('name', $request->get('role_name'))->first();
        $user = User::create(array_merge(
            $request->all(),
            [
                'role_id' => $role->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'activated_at' => now(),
            ],
        ));
        return $user;

        //event(new Registered($user));

        return new UserResource($user, 201);
    }
}
