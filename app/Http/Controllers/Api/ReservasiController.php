<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use App\Models\ReservasiModel;
use App\Models\UserModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Post(
 *     path="/user/reservation/new",
 *     summary="Create a new car reservation",
 *     description="This endpoint creates a new reservation for a car to be rented by the user",
 *     tags={"Reservation"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"car_id", "start_date", "end_date"},
 *             @OA\Property(property="car_id", type="string", format="uuid", description="UUID of the car to be reserved"),
 *             @OA\Property(property="start_date", type="string", format="date", description="Start date of the reservation (YYYY-MM-DD)"),
 *             @OA\Property(property="end_date", type="string", format="date", description="End date of the reservation (YYYY-MM-DD)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Reservation created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=201),
 *                 @OA\Property(property="is_success", type="boolean", example=true)
 *             ),
 *             @OA\Property(property="message", type="string", example="Reservation created successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="reservation_id", type="integer", example=1),
 *                 @OA\Property(property="start_date", type="string", format="date", example="2024-12-01"),
 *                 @OA\Property(property="end_date", type="string", format="date", example="2024-12-05"),
 *                 @OA\Property(property="total_price", type="number", format="float", example=1000000),
 *                 @OA\Property(property="status", type="string", example="pending"),
 *                 @OA\Property(property="car", type="object",
 *                     @OA\Property(property="name", type="string", example="Toyota Avanza"),
 *                     @OA\Property(property="year", type="integer", example=2021),
 *                     @OA\Property(property="jenis", type="string", example="SUV")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid data or bad request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=400),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="Invalid data provided"),
 *             @OA\Property(property="data", type="object",
 *                 additionalProperties=true,
 *                 description="Validation errors"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=500),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="Failed to create reservation"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/user/reservations",
 *     summary="Get user's reservation history",
 *     description="This endpoint retrieves all reservations history made by current logged-in user",
 *     tags={"Reservation"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User reservation history retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="is_success", type="boolean", example=true)
 *             ),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="reservation_id", type="integer", example=1),
 *                     @OA\Property(property="start_date", type="string", format="date", example="2024-12-01"),
 *                     @OA\Property(property="end_date", type="string", format="date", example="2024-12-05"),
 *                     @OA\Property(property="total_price", type="number", format="float", example=1000000),
 *                     @OA\Property(property="status", type="string", example="completed"),
 *                     @OA\Property(property="car", type="object",
 *                         @OA\Property(property="name", type="string", example="Toyota Avanza"),
 *                         @OA\Property(property="year", type="integer", example=2021),
 *                         @OA\Property(property="jenis", type="string", example="SUV")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No reservation found for the user",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="No reservation found related to this user"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/admin/reservations/all",
 *     summary="Get all reservations (Admin Only)",
 *     description="This endpoint retrieves all reservations made by users. This endpoint is only accessible by Admin",
 *     tags={"Admin"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="All reservations retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=200),
 *                 @OA\Property(property="is_success", type="boolean", example=true)
 *             ),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="user_id", type="integer", example=1),
 *                     @OA\Property(property="reservation_id", type="integer", example=101),
 *                     @OA\Property(property="user_name", type="string", example="John Doe"),
 *                     @OA\Property(property="user_email", type="string", example="john.doe@example.com"),
 *                     @OA\Property(property="user_address", type="string", example="123 Main St"),
 *                     @OA\Property(property="user_phone_number", type="string", example="+628123456789"),
 *                     @OA\Property(property="start_date", type="string", format="date", example="2024-12-01"),
 *                     @OA\Property(property="end_date", type="string", format="date", example="2024-12-05"),
 *                     @OA\Property(property="total_price", type="number", format="float", example=1500000),
 *                     @OA\Property(property="status", type="string", example="completed"),
 *                     @OA\Property(property="car", type="object",
 *                         @OA\Property(property="car_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
 *                         @OA\Property(property="name", type="string", example="Toyota Avanza"),
 *                         @OA\Property(property="year", type="integer", example=2021),
 *                         @OA\Property(property="jenis", type="string", example="SUV")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No reservations found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=404),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="No reservation found"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized access",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="object",
 *                 @OA\Property(property="code", type="integer", example=403),
 *                 @OA\Property(property="is_success", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="message", type="string", example="Unauthorized access"),
 *             @OA\Property(property="data", type="null", nullable=true)
 *         )
 *     )
 * )
 */

class ReservasiController extends Controller
{
    function newReservation(Request $request) {
        $user = $request->auth_user;

        $validator = Validator::make($request->all(), [
            'car_id' => 'required|uuid|exists:car,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
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

        $car = CarModel::find($request->car_id);

        if ($car->status_id != 1) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Car is not available for reservation',
                'data' => null,
            ], Response::HTTP_BAD_REQUEST);
        }

        $start_date = new DateTime($request->start_date);
        $end_date = new DateTime($request->end_date);

        if ($start_date > $end_date) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Start date must be before end date',
                'data' => null,
            ], Response::HTTP_BAD_REQUEST);
        }

        $durasi_sewa = $start_date->diff($end_date)->days;

        $total_harga = $car->harga_sewa * $durasi_sewa;

        $reservasi = ReservasiModel::create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_harga' => $total_harga,
            'status' => 'pending',
        ]);

        if (!$reservasi) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'is_success' => false,
                ],
                'message' => 'Failed to create reservation',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [
            'reservation_id' => $reservasi->id,
            'start_date' => $reservasi->start_date,
            'end_date' => $reservasi->end_date,
            'total_price' => $reservasi->total_harga,
            'status' => $reservasi->status,
            'car' => [
                'name' => $reservasi->car->nama_mobil,
                'year' => $reservasi->car->tahun,
                'jenis' => $reservasi->car->jenis->jenis,
            ]
        ];

        return response()->json([
            'status' => [
                'code' => Response::HTTP_CREATED,
                'is_success' => true,
            ],
            'message' => 'Reservation created successfully',
            'data' => $response,
        ], Response::HTTP_CREATED);
    }

    function getUserReservationHistory (Request $request) {
        $user = $request->auth_user;

        $reservasions = ReservasiModel::with([
            'car' => function($query) {
                $query->with(['jenis']);
            }
        ])->where('user_id', $user->id)->get();

        if (count($reservasions) == 0) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'No reservation found related to this user',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $response = $reservasions->map(function ($reservasi) {
            return [
                'reservation_id' => $reservasi->id,
                'start_date' => $reservasi->start_date,
                'end_date' => $reservasi->end_date,
                'total_price' => $reservasi->total_harga,
                'status' => $reservasi->status,
                'car' => [
                    'name' => $reservasi->car->nama_mobil,
                    'year' => $reservasi->car->tahun,
                    'jenis' => $reservasi->car->jenis->jenis,
                ]
            ];
        });

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $response,
        ], Response::HTTP_OK);
    }

    function getAllReservations () {
        $reservasions = ReservasiModel::with([
            'car' => function($query) {
                $query->with(['jenis']);
            }
        ])->get();

        if (count($reservasions) == 0) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'No reservation found',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $response = $reservasions->map(function ($reservasi) {
            return [
                'user_id' => $reservasi->user_id,
                'reservation_id' => $reservasi->id,
                'user_name' => $reservasi->user->nama_user,
                'user_email' => $reservasi->user->email,
                'user_address' => $reservasi->user->alamat,
                'user_phone_number' => $reservasi->user->phone_number,
                'start_date' => $reservasi->start_date,
                'end_date' => $reservasi->end_date,
                'total_price' => $reservasi->total_harga,
                'status' => $reservasi->status,
                'car' => [
                    'car_id' => $reservasi->car->id,
                    'name' => $reservasi->car->nama_mobil,
                    'year' => $reservasi->car->tahun,
                    'jenis' => $reservasi->car->jenis->jenis,
                ]
            ];
        });

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $response,
        ], Response::HTTP_OK);
    }
}
