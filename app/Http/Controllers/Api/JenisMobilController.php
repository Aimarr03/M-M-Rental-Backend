<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisMobilModel;
use Illuminate\Http\Response;


/**
 * @OA\Get(
 *     path="/api/car/categories",
 *     summary="Get all vehicle categories",
 *     description="This endpoint is used to get all vehicle categories that are available in the database.",
 *     tags={"Car"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved all vehicle types",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="is_success", type="boolean", example=true)
 *             ),
 *             @OA\Property(property="message", type="string", example="Success get all vehicle types."),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="jenis_kendaraan", type="string", example="Sedan")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=401),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="Unauthorized access"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="An error occurred while fetching vehicle types."),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     )
 * )
 */

class JenisMobilController extends Controller
{
    function getAllVehicleType() {
        $vehTypes = JenisMobilModel::select('id', 'jenis')->get();

        $response = $vehTypes->map(function ($vehType) {
            return [
                'id' => $vehType->id,
                'jenis_kendaraan' => $vehType->jenis,
            ];
        });

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success get all vehicle types.',
            'data' => $response,
        ], Response::HTTP_OK);
    }
}
