<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user->isActive()) {
            return new ErrorResource(
                ['email' => [__('validation.exists', ['attribute' => 'email'])]],
                422,
                'ERR4022'
            );
        }

        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? new Resource([], 200, __($status))
            : new ErrorResource(__($status), 400, 'ERR4000');
    }

    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ]);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? new Resource([], 200, __($status))
            : new ErrorResource(__($status), 400, 'ERR4000');
    }
}
