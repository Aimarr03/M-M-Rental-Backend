<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
* @OA\Get(
*      path="/api/car/all",
*      tags={"Car"},
*      summary="Get all cars",
*      description="Retrieve all cars from the database. If no cars are found, a 204 No Content response will be returned. If data is found, a 200 OK response with the car data is returned.",
*      operationId="getAllCars",
*      security={{"bearerAuth": {}}},
*      @OA\Response(
*          response=200,
*          description="Success",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Success", description="Success message"),
*               @OA\Property(property="data", type="array", description="List of cars",
*                   @OA\Items(
*                       type="object",
*                       @OA\Property(property="id", type="integer", description="Car ID"),
*                       @OA\Property(property="model", type="string", description="Car model"),
*                       @OA\Property(property="make", type="string", description="Car make"),
*                       @OA\Property(property="year", type="integer", description="Year of manufacture"),
*                       @OA\Property(property="status_id", type="integer", description="Status ID of the car")
*                   )
*               ),
*          ),
*     ),
*     @OA\Response(
*          response=204,
*          description="No content found",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 204, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Data not found", description="Message indicating no data"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*     ),
*     @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="An error occurred", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*     ),
* )
*

* @OA\Get(
*      path="/api/car/{id}",
*      tags={"Car"},
*      summary="Get car by ID",
*      description="Retrieve a car by its ID. If no car is found with the provided ID, a 404 Not Found response will be returned. If the car is found, a 200 OK response with the car data is returned.",
*      operationId="getCarByID",
*      security={{"bearerAuth": {}}},
*      @OA\Parameter(
*          name="id",
*          in="path",
*          required=true,
*          description="The ID of the car to retrieve",
*          @OA\Schema(type="integer")
*      ),
*      @OA\Response(
*          response=200,
*          description="Success",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Success", description="Success message"),
*               @OA\Property(property="data", type="object", description="Car data",
*                   @OA\Property(property="id", type="integer", description="Car ID"),
*                   @OA\Property(property="model", type="string", description="Car model"),
*                   @OA\Property(property="make", type="string", description="Car make"),
*                   @OA\Property(property="year", type="integer", description="Year of manufacture"),
*                   @OA\Property(property="status_id", type="integer", description="Status ID of the car")
*               )
*          ),
*     ),
*     @OA\Response(
*          response=404,
*          description="Not Found",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 404, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Data not found", description="Message indicating no data was found"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*     ),
*     @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="An error occurred", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*     ),
* )
*

* @OA\Post(
*      path="/api/admin/car/add",
*      tags={"Admin"},
*      summary="Add a new car",
*      description="This endpoint allows an admin to add a new car to the system. It requires authentication and the user must have an admin role.",
*      operationId="addNewCar",
*      security={{"bearerAuth": {}}},
*      @OA\RequestBody(
*          required=true,
*          @OA\JsonContent(
*              required={"nama_mobil", "tahun", "plat_nomor", "id_jenis", "kapasitas_penumpang", "harga_sewa", "status_id", "transmisi"},
*              @OA\Property(property="nama_mobil", type="string", example="Toyota Corolla", description="Car model name"),
*              @OA\Property(property="tahun", type="integer", example=2020, description="Car manufacturing year"),
*              @OA\Property(property="plat_nomor", type="string", example="B1234XYZ", description="Car plate number"),
*              @OA\Property(property="id_jenis", type="integer", example=1, description="Car type ID (references the 'jenis_mobil' table)"),
*              @OA\Property(property="kapasitas_penumpang", type="integer", example=5, description="Passenger capacity"),
*              @OA\Property(property="harga_sewa", type="integer", example=100000, description="Car rental price per day"),
*              @OA\Property(property="foto", type="string", example="image_url.jpg", description="Car image URL (optional)"),
*              @OA\Property(property="status_id", type="integer", example=1, description="Car status ID (references the 'status' table)"),
*              @OA\Property(property="transmisi", type="string", example="automatic", description="Transmission type, either 'manual' or 'automatic'"),
*          ),
*      ),
*      @OA\Response(
*          response=201,
*          description="New car added successfully",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 201, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="New car added successfully", description="Success message"),
*               @OA\Property(property="data", type="object", description="Added car data",
*                   @OA\Property(property="id", type="integer", description="Car ID"),
*                   @OA\Property(property="nama_mobil", type="string", description="Car model name"),
*                   @OA\Property(property="tahun", type="integer", description="Car manufacturing year"),
*                   @OA\Property(property="plat_nomor", type="string", description="Car plate number"),
*                   @OA\Property(property="id_jenis", type="integer", description="Car type ID"),
*                   @OA\Property(property="kapasitas_penumpang", type="integer", description="Passenger capacity"),
*                   @OA\Property(property="harga_sewa", type="integer", description="Car rental price per day"),
*                   @OA\Property(property="foto", type="string", description="Car image URL"),
*                   @OA\Property(property="status_id", type="integer", description="Car status ID"),
*                   @OA\Property(property="transmisi", type="string", description="Transmission type"),
*               )
*          ),
*      ),
*      @OA\Response(
*          response=400,
*          description="Validation Failed",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Validation Failed", description="Validation error message"),
*               @OA\Property(property="data", type="object", description="Validation errors")
*          ),
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 403, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Access denied: Only admins can add cars", description="Permission error message"),
*               @OA\Property(property="data", type="null", description="No data")
*          ),
*      ),
*      @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Failed to add new car", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*      ),
* )
*

* @OA\Patch(
*      path="/api/admin/car/update/{id}",
*      tags={"Admin"},
*      summary="Update car details",
*      description="This endpoint allows an admin to update the details of a car by its ID. It requires authentication and the user must have an admin role.",
*      operationId="updateCar",
*      security={{"bearerAuth": {}}},
*      @OA\Parameter(
*          name="id",
*          in="path",
*          required=true,
*          description="ID of the car to update (UUID format)",
*          @OA\Schema(type="string", format="uuid"),
*      ),
*      @OA\RequestBody(
*          required=false,
*          @OA\JsonContent(
*              required={},
*              @OA\Property(property="nama_mobil", type="string", example="Honda Civic", description="Car model name"),
*              @OA\Property(property="tahun", type="integer", example=2021, description="Car manufacturing year"),
*              @OA\Property(property="plat_nomor", type="string", example="B1234ABC", description="Car plate number"),
*              @OA\Property(property="id_jenis", type="integer", example=2, description="Car type ID (references the 'jenis_mobil' table)"),
*              @OA\Property(property="kapasitas_penumpang", type="integer", example=4, description="Passenger capacity"),
*              @OA\Property(property="harga_sewa", type="integer", example=120000, description="Car rental price per day"),
*              @OA\Property(property="foto", type="string", example="image_url_updated.jpg", description="Updated car image URL (optional)"),
*              @OA\Property(property="status_id", type="integer", example=2, description="Car status ID (references the 'status' table)"),
*              @OA\Property(property="transmisi", type="string", example="manual", description="Transmission type, either 'manual' or 'automatic'"),
*          ),
*      ),
*      @OA\Response(
*          response=200,
*          description="Car updated successfully",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Car updated successfully", description="Success message"),
*               @OA\Property(property="data", type="object", description="Updated car data",
*                   @OA\Property(property="id", type="string", description="Car ID (UUID format)"),
*                   @OA\Property(property="nama_mobil", type="string", description="Car model name"),
*                   @OA\Property(property="tahun", type="integer", description="Car manufacturing year"),
*                   @OA\Property(property="plat_nomor", type="string", description="Car plate number"),
*                   @OA\Property(property="id_jenis", type="integer", description="Car type ID"),
*                   @OA\Property(property="kapasitas_penumpang", type="integer", description="Passenger capacity"),
*                   @OA\Property(property="harga_sewa", type="integer", description="Car rental price per day"),
*                   @OA\Property(property="foto", type="string", description="Car image URL"),
*                   @OA\Property(property="status_id", type="integer", description="Car status ID"),
*                   @OA\Property(property="transmisi", type="string", description="Transmission type"),
*               )
*          ),
*      ),
*      @OA\Response(
*          response=400,
*          description="Validation Failed or Invalid ID format",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Invalid ID format or Validation Failed", description="Error message"),
*               @OA\Property(property="data", type="object", description="Validation errors or ID format error")
*          ),
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 403, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Access denied: Only admins can update cars", description="Permission error message"),
*               @OA\Property(property="data", type="null", description="No data")
*          ),
*      ),
*      @OA\Response(
*          response=404,
*          description="Car not found",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 404, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="There is no car with this ID", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data")
*          ),
*      ),
*      @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Failed to update car", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*      ),
* )
*

* @OA\Get(
*      path="/api/cars/category/{id_kategori}",
*      tags={"Car"},
*      summary="Get cars by category",
*      description="This endpoint allows users to retrieve cars that belong to a specific category by providing the category ID.",
*      operationId="getCarsByCategory",
*      security={{"bearerAuth": {}}},
*      @OA\Parameter(
*          name="id_kategori",
*          in="path",
*          required=true,
*          description="ID of the car category (integer, references 'jenis_mobil' table)",
*          @OA\Schema(type="integer"),
*      ),
*      @OA\Response(
*          response=200,
*          description="List of cars in the specified category",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Success", description="Success message"),
*               @OA\Property(property="data", type="array", description="List of cars",
*                   @OA\Items(
*                       type="object",
*                       @OA\Property(property="id", type="string", description="Car ID (UUID format)"),
*                       @OA\Property(property="nama_mobil", type="string", description="Car model name"),
*                       @OA\Property(property="tahun", type="integer", description="Car manufacturing year"),
*                       @OA\Property(property="plat_nomor", type="string", description="Car plate number"),
*                       @OA\Property(property="id_jenis", type="integer", description="Car type ID"),
*                       @OA\Property(property="kapasitas_penumpang", type="integer", description="Passenger capacity"),
*                       @OA\Property(property="harga_sewa", type="integer", description="Car rental price per day"),
*                       @OA\Property(property="foto", type="string", description="Car image URL"),
*                       @OA\Property(property="status_id", type="integer", description="Car status ID"),
*                       @OA\Property(property="transmisi", type="string", description="Transmission type"),
*                   )
*               )
*          ),
*      ),
*      @OA\Response(
*          response=400,
*          description="Invalid category ID",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Invalid category ID", description="Error message"),
*               @OA\Property(property="data", type="object", description="Validation errors for the category ID")
*          ),
*      ),
*      @OA\Response(
*          response=404,
*          description="No cars found in this category",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 404, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="There is no car in this category", description="Error message"),
*               @OA\Property(property="data", type="null", description="No cars in the specified category")
*          ),
*      ),
*      @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Failed to fetch cars by category", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*      ),
* )
*

* @OA\Get(
*      path="/api/cars/status/{status_id}",
*      tags={"Car"},
*      summary="Get cars by status ID",
*      description="This endpoint allows users to retrieve cars that are assigned a specific status ID.",
*      operationId="getCarsByStatusID",
*      security={{"bearerAuth": {}}},
*      @OA\Parameter(
*          name="status_id",
*          in="path",
*          required=true,
*          description="ID of the car status (integer, references 'status' table)",
*          @OA\Schema(type="integer"),
*      ),
*      @OA\Response(
*          response=200,
*          description="List of cars with the specified status",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Success", description="Success message"),
*               @OA\Property(property="data", type="array", description="List of cars",
*                   @OA\Items(
*                       type="object",
*                       @OA\Property(property="id", type="string", description="Car ID (UUID format)"),
*                       @OA\Property(property="nama_mobil", type="string", description="Car model name"),
*                       @OA\Property(property="tahun", type="integer", description="Car manufacturing year"),
*                       @OA\Property(property="plat_nomor", type="string", description="Car plate number"),
*                       @OA\Property(property="id_jenis", type="integer", description="Car type ID"),
*                       @OA\Property(property="kapasitas_penumpang", type="integer", description="Passenger capacity"),
*                       @OA\Property(property="harga_sewa", type="integer", description="Car rental price per day"),
*                       @OA\Property(property="foto", type="string", description="Car image URL"),
*                       @OA\Property(property="status_id", type="integer", description="Car status ID"),
*                       @OA\Property(property="transmisi", type="string", description="Transmission type"),
*                   )
*               )
*          ),
*      ),
*      @OA\Response(
*          response=400,
*          description="Invalid status ID",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Invalid status ID", description="Error message"),
*               @OA\Property(property="data", type="object", description="Validation errors for the status ID")
*          ),
*      ),
*      @OA\Response(
*          response=404,
*          description="No cars found with the specified status",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 404, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Currently there is no car with this status", description="Error message"),
*               @OA\Property(property="data", type="null", description="No cars with the specified status")
*          ),
*      ),
*      @OA\Response(
*          response=500,
*          description="Internal Server Error",
*          @OA\JsonContent(
*               @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}, description="Status of the request"),
*               @OA\Property(property="message", type="string", example="Failed to fetch cars by status", description="Error message"),
*               @OA\Property(property="data", type="null", description="No data returned")
*          ),
*      ),
* )
*

* @OA\Delete(
*     path="/api/admin/car/delete/{id}",
*     tags={"Admin"},
*     summary="Delete a car by ID",
*     description="This endpoint allows an admin to delete a car using its ID. Only admins can access this endpoint.",
*     operationId="deleteCar",
*     security={{"bearerAuth": {}}},
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="The ID of the car to be deleted (UUID format)",
*         @OA\Schema(type="string"),
*     ),
*     @OA\Response(
*         response=200,
*         description="Car deleted successfully",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="object", example={"code": 200, "is_success": true}),
*             @OA\Property(property="message", type="string", example="Car deleted successfully"),
*             @OA\Property(property="data", type="null")
*         ),
*     ),
*     @OA\Response(
*         response=400,
*         description="Invalid ID format",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="object", example={"code": 400, "is_success": false}),
*             @OA\Property(property="message", type="string", example="Invalid ID format"),
*             @OA\Property(property="data", type="object")
*         ),
*     ),
*     @OA\Response(
*         response=404,
*         description="Car not found",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="object", example={"code": 404, "is_success": false}),
*             @OA\Property(property="message", type="string", example="There is no car with this ID"),
*             @OA\Property(property="data", type="null")
*         ),
*     ),
*     @OA\Response(
*         response=403,
*         description="Forbidden - Only admins can access this endpoint",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="object", example={"code": 403, "is_success": false}),
*             @OA\Property(property="message", type="string", example="Unauthorized access, only admins can delete cars"),
*             @OA\Property(property="data", type="null")
*         ),
*     ),
*     @OA\Response(
*         response=500,
*         description="Internal Server Error",
*         @OA\JsonContent(
*             @OA\Property(property="status", type="object", example={"code": 500, "is_success": false}),
*             @OA\Property(property="message", type="string", example="Failed to delete car"),
*             @OA\Property(property="data", type="null")
*         ),
*     ),
* )
*/


class CarController extends Controller
{
    function getAll() {
        $cars = CarModel::all();

        if (!$cars) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NO_CONTENT,
                    'is_success' => true,
                ],
                'message' => 'Data not found',
                'data' => null,
            ], Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $cars,
        ], Response::HTTP_OK);
    }

    function getByID($id) {
        $car = CarModel::find($id);

        if (!$car) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'Data not found',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $car,
        ], Response::HTTP_OK);
    }

    function addNew(Request $request) {
        $validator = Validator::make($request->all(), [
            'nama_mobil' => 'required|string',
            'tahun' => 'required|integer',
            'plat_nomor' => 'required|string',
            'id_jenis' => 'required|integer|exists:jenis_mobil,id',
            'kapasitas_penumpang' => 'required|integer',
            'harga_sewa' => 'required|integer',
            'status_id' => 'required|integer|exists:status,id',
            'transmisi' => 'required|string|in:manual,automatic',
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

        $car = CarModel::create([
            'nama_mobil' => $request->nama_mobil,
            'tahun' => $request->tahun,
            'plat_nomor' => $request->plat_nomor,
            'id_jenis' => $request->id_jenis,
            'kapasitas_penumpang' => $request->kapasitas_penumpang,
            'harga_sewa' => $request->harga_sewa,
            'foto' => $request->foto,
            'status_id' => $request->status_id,
            'transmisi' => $request->transmisi,
        ]);

        if (!$car) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'is_success' => false,
                ],
                'message' => 'Failed to add new car',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_CREATED,
                'is_success' => true,
            ],
            'message' => 'New car added successfully',
            'data' => $car,
        ], Response::HTTP_CREATED);
    }

    function update(Request $request, $id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Invalid ID format',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (count($request->all()) == 0) {
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
            'nama_mobil' => 'sometimes|required|string',
            'tahun' => 'sometimes|required|integer',
            'plat_nomor' => 'sometimes|required|string',
            'id_jenis' => 'sometimes|required|integer|exists:jenis_mobil,id',
            'kapasitas_penumpang' => 'sometimes|required|integer',
            'harga_sewa' => 'sometimes|required|integer',
            'status_id' => 'sometimes|required|integer|exists:status,id',
            'transmisi' => 'sometimes|required|string|in:manual,automatic',
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

        $car = CarModel::find($id);

        if (!$car) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'There is no car with this ID',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $car->update($request->all());

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Car updated successfully',
            'data' => $car,
        ], Response::HTTP_OK);
    }

    function getCarsByCategory($id_kategori) {
        $validator = Validator::make(['id_kategori' => $id_kategori], [
            'id_kategori' => 'required|integer|exists:jenis_mobil,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Invalid category ID',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $cars = CarModel::where('id_jenis', $id_kategori)->get();

        if (count($cars) == 0) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_OK,
                    'is_success' => true,
                ],
                'message' => 'There is no car in this category',
                'data' => null,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $cars,
        ], Response::HTTP_OK);
    }

    function getCarsByStatusID($status_id) {
        $validator = Validator::make(['status_id' => $status_id], [
            'status_id' => 'required|integer|exists:status,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Invalid status ID',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $cars = CarModel::where('status_id', $status_id)->get();

        if (count($cars) == 0) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_OK,
                    'is_success' => true,
                ],
                'message' => 'Currently there is no car with this status',
                'data' => null,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Success',
            'data' => $cars,
        ], Response::HTTP_OK);
    }

    function deleteCar($id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'is_success' => false,
                ],
                'message' => 'Invalid ID format',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $car = CarModel::find($id);

        if (!$car) {
            return response()->json([
                'status' => [
                    'code' => Response::HTTP_NOT_FOUND,
                    'is_success' => false,
                ],
                'message' => 'There is no car with this ID',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $car->delete();

        return response()->json([
            'status' => [
                'code' => Response::HTTP_OK,
                'is_success' => true,
            ],
            'message' => 'Car deleted successfully',
            'data' => null,
        ], Response::HTTP_OK);
    }
}
