<?php

namespace App\Http\Middleware;

use App\Models\RoleModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth()->guard('api')->user();

        $roleName = RoleModel::find($user->role_id)->role_name;

        if(!$user || $roleName != $role) {
            return response()->json([
                "status" => [
                    "code" => 401,
                    "is_success" => false,
                ],
                "message" => "You are not authorized to access this endpoint",
                "data" => null,
            ]);
        }

        return $next($request);
    }
}
