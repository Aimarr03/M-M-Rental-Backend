<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
* @OA\Post(
*      path="/api/user/login",
*      tags={"Auth"},
*      summary="Login",
*      operationId="login",
*      description="Digunakan untuk login ke dalam aplikasi",
*      @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*            required={"email", "password"},
*            @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com", description="Email"),
*            @OA\Property(property="password", type="string", format="password", example="password", description="Password"),
*        ),
*    ),
*     @OA\Response(
*          response=200,
*          description="Success",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status"),
*               @OA\Property(property="message", type="string", example="Success", description="Message"),
*               @OA\Property(property="data", type="object", description="Data"),
*          ),
*   ),
*)

* @OA\Post(
*      path="/api/user/logout",
*      tags={"Auth"},
*      summary="Logout",
*      operationId="logout",
*      description="Digunakan untuk logout dari aplikasi",
*      @OA\Response(
*          response=200,
*          description="Successfully logged out",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status"),
*               @OA\Property(property="message", type="string", example="successfully logged out", description="Message"),
*               @OA\Property(property="data", type="null", description="Data"),
*          ),
*   ),
* )
*
* @OA\Get(
*      path="/api/user/current-user",
*      tags={"User"},
*      summary="Get current logged in user",
*      description="This endpoint retrieves the current user who is logged in to the application based on the provided Bearer token. It returns user details such as ID, email, and role, or an error message if the user is not authenticated or the token is invalid/expired.",
*      operationId="getCurrentUser",
*      security={{"bearerAuth": {}}},
*      @OA\Response(
*          response=200,
*          description="User retrieved successfully",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status"),
*               @OA\Property(property="message", type="string", example="User retrieved successfully", description="Message"),
*               @OA\Property(property="data", type="object", description="User data",
*                   @OA\Property(property="id", type="integer", description="User ID"),
*                   @OA\Property(property="email", type="string", format="email", description="User email"),
*                   @OA\Property(property="nama_user", type="string", description="User name"),
*                   @OA\Property(property="phone_number", type="string", description="User phone number"),
*                   @OA\Property(property="role_id", type="integer", description="User role ID"),
*                   @OA\Property(property="alamat", type="string", description="User address")
*               ),
*          ),
*          @OA\Response(
*              response=401,
*              description="Unauthorized: User not found or token expired",
*              @OA\JsonContent(
*                  @OA\Property(property="status", type="object", example={"code": 401, "is_success": false}, description="Status"),
*                  @OA\Property(property="message", type="string", example="Unauthorized: User not found", description="Message"),
*                  @OA\Property(property="data", type="null", description="Data")
*              ),
*          ),
*          @OA\Response(
*              response=500,
*              description="Internal Server Error",
*              @OA\JsonContent(
*                  @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status"),
*                  @OA\Property(property="message", type="string", example="An error occurred while parsing the token", description="Message"),
*                  @OA\Property(property="data", type="null", description="Data")
*              ),
*          ),
*   )
* )
*/


class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => [
                    "code" => Response::HTTP_BAD_REQUEST,
                    "is_success" => false,
                ],
                "message" => "Validation Failed",
                "data" => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                "status" => [
                    "code" => Response::HTTP_BAD_REQUEST,
                    "is_success" => false,
                ],
                "message" => "Email atau Password Salah",
                "data" => null,
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            "status" => [
                "code" => Response::HTTP_OK,
                "is_success" => true,
            ],
            "message" => "Success",
            "data" => [
                "token" => $token,
                "token_type" => "bearer",
                "user" => auth()->guard('api')->user()
            ],
        ], Response::HTTP_OK);
    }

    public function logout(Request $request) {
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        if ($removeToken) {
            return response()->json([
                "status" => [
                    "code" => 200,
                    "is_success" => true,
                ],
                "message" => "successfully logged out",
                "data" => null,
            ]);
        }
    }

    public function refresh() {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    public function me(Request $request) {
        $user = $request->auth_user;

        return response()->json([
            "status" => [
                "code" => Response::HTTP_OK,
                "is_success" => true,
            ],
            "message" => "User retrieved successfully",
            "data" => $user,
        ], Response::HTTP_OK);
    }

    public function respondWithToken($token) {
        return response()->json([
            "status" => [
                "code" => 200,
                "is_success" => true,
            ],
            "message" => "Success",
            "data" => [
                "token" => $token,
                "token_type" => "bearer",
                "expires_in" => JWTAuth::factory()->getTTL() * 60,
                "user" => Auth::user(),
            ],
        ]);
    }
}
