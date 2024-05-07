<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;
use App\Models\UserVerificationToken;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);
        $token = UserVerificationToken::find($request->token);
        if (!$token || !$token->isValid()) {
            return new ErrorResource(
                __('errors.' . $errCode = 'ERR4200'),
                400,
                $errCode
            );
        }

        $user = $token->user;
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return new UserResource($user);
    }
}
