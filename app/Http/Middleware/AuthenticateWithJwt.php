<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthenticateWithJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = auth()->guard('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => [
                        'code' => Response::HTTP_UNAUTHORIZED,
                        'is_success' => false,
                    ],
                    'message' => 'Unauthorized: User not found',
                    'data' => null,
                ], Response::HTTP_UNAUTHORIZED);
            }

            $request->merge(['auth_user' => $user]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'is_success' => false,
                ],
                'message' => 'Unauthorized: Token has expired',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'is_success' => false,
                ],
                'message' => 'Unauthorized: Token is invalid',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'is_success' => false,
                ],
                'message' => 'An error occurred while parsing the token',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
