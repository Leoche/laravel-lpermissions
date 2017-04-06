<?php
namespace Leoche\LPermissions\Middleware;

use Closure;

class CheckPermission
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->hasPermission($request)) {
            if ($request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'error' => [
                    'status_code' => 401,
                    'code'        => 'INSUFFICIENT_PERMISSIONS',
                    'description' => 'You are not authorized to access this resource.'
                    ],
                    ], 401);
            }
            return abort(401, 'You are not authorized to access this resource.');
        }
        return $next($request);
    }
}
