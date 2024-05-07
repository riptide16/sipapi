<?php

namespace App\Http\Middleware;

use Closure;
use Str;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CheckForAnyPermission extends CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$keys)
    {
        if (empty($keys)) {
            $keys = [$this->guessKeyFromRoute($request)];
        }

        $user = $request->user();
        foreach ($keys as $key) {
            // if the key starts with crud, then guess the key with specified resource
            if (Str::startsWith($key, 'crud=')) {
                $key = $this->guessKeyFromRoute($request, Str::replaceFirst('crud=', '', $key));
            }

            if ($user->ableTo($key)) {
                return $next($request);
            }
        }
        
        throw new AuthorizationException();
    }
}
