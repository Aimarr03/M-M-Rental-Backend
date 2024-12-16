<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
* @OA\Post(
*      path="/api/user/register",
*      tags={"Auth"},
*      summary="Register a new user",
*      description="This endpoint registers a new user by accepting the user's name, email, password, phone number, and address. The system will create a new user record in the database with a default role of 2 (User).",
*      operationId="registerUser",
*      @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*            required={"name", "email", "password", "phone_number", "alamat"},
*            @OA\Property(property="name", type="string", example="John Doe", description="Full name of the user"),
*            @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com", description="User's email address"),
*            @OA\Property(property="password", type="string", format="password", example="password123", description="User's password (minimum length 6 characters)"),
*            @OA\Property(property="phone_number", type="string", example="1234567890", description="User's phone number"),
*            @OA\Property(property="alamat", type="string", example="123 Street Name, City, Country", description="User's address"),
*        ),
*    ),
*     @OA\Response(
*          response=201,
*          description="User created successfully",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 201, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="User created successfully", description="Success message"),
*               @OA\Property(property="data", type="object", description="Created user data",
*                   @OA\Property(property="id", type="integer", description="User ID"),
*                   @OA\Property(property="nama_user", type="string", description="User full name"),
*                   @OA\Property(property="email", type="string", description="User email"),
*                   @OA\Property(property="phone_number", type="string", description="User phone number"),
*                   @OA\Property(property="role_id", type="integer", description="User role ID"),
*                   @OA\Property(property="alamat", type="string", description="User address")
*               ),
*          ),
*     ),
*     @OA\Response(
*          response=400,
*          description="Validation Failed",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}, description="Status"),
*               @OA\Property(property="message", type="string", example="Validation Failed", description="Validation error message"),
*               @OA\Property(property="data", type="object", description="Validation errors")
*          ),
*     ),
*     @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status"),
*               @OA\Property(property="message", type="string", example="Failed to create user", description="Error message"),
*               @OA\Property(property="data", type="null", description="No additional data")
*          ),
*     ),
* )
*/

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Validation Failed',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = UserModel::create([
            'nama_user' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role_id' => 2,
            'alamat' => $request->alamat,
        ]);

        if ($user) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_CREATED,
                    'is_success' => true,
                ],
                'message' => 'User created successfully',
                'data' => $user,
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'is_success' => false,
            ],
            'message' => 'Failed to create user',
            'data' => null,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
