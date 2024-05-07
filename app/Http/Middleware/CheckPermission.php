<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Str;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  $keys
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

            $user->canOrFail($key);
        }
        
        return $next($request);
    }

    protected function guessKeyFromRoute(Request $request, $resource = null)
    {
        $exploded = explode('.', $request->route()->action['as']);
        $action = $exploded[count($exploded)-1];
        $resource = $resource ?? $exploded[count($exploded)-2];

        return $this->translateAction($action) . '_' . $resource;
    }

    protected function translateAction($action)
    {
        $dict = [
            'index' => 'browse',
            'show' => 'read',
            'store' => 'add',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        return isset($dict[$action]) ? $dict[$action] : $action;
    }
}
