<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\CarModel;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    function GetListKendaraan(){
        try {
            $daftar_kendaraan = CarModel::get();
            return response()->json($daftar_kendaraan, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    function GetKendaraan($id){
        try {
            // Fetch the car data by ID
            $kendaraan = CarModel::find($id);
    
            // Check if the car exists
            if (!$kendaraan) {
                return response()->json(['error' => 'Kendaraan not found'], 404);
            }
    
            // Return the car data
            return response()->json($kendaraan, 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
