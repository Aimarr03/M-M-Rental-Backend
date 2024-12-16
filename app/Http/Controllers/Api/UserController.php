<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Patch(
 *     path="/user/update",
 *     summary="Update user profile",
 *     description="This endpoint is used to update user profile, like username, phone number, address, and email",
 *     tags={"User"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="nama_user", type="string", maxLength=255, example="John Doe"),
 *             @OA\Property(property="phone_number", type="string", maxLength=15, example="+628123456789"),
 *             @OA\Property(property="alamat", type="string", maxLength=255, example="123 Main St"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="is_success", type="boolean", example=true)
 *             ),
 *             @OA\Property(property="message", type="string", example="User updated successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nama_user", type="string", example="John Doe"),
 *                 @OA\Property(property="phone_number", type="string", example="+628123456789"),
 *                 @OA\Property(property="alamat", type="string", example="123 Main St"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-15T12:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data provided or no data provided to update",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=400),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="Invalid data provided"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\AdditionalProperties(type="string", example="The email field must be a valid email.")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No user found with that ID",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="No user found with that ID"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     )
 * )
 */

class UserController extends Controller
{
    function updateProfile(Request $request) {
        $user = $request->auth_user;

        $allowedField = ['nama_user', 'phone_number', 'alamat', 'email'];
        $updateData = $request->only($allowedField);

        if (empty($updateData)) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'No data provided to update',
                'data' => null,
            ], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            'nama_user' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:15',
            'alamat' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:user,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Invalid data provided',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = UserModel::find($user->id);
        if(!$user) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'No user found with that ID',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->all());

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'User updated successfully',
            'data' => $user,
        ], Response::HTTP_OK);
    }
}
