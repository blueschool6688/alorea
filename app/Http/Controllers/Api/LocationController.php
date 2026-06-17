<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Provinces;
use App\Models\Ward;
use App\Models\Wards;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends ApiController
{

    public function getProvinces(): JsonResponse
    {
        try {
            $provinces = ProvinceS::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $provinces,
                'message' => 'Provinces retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve provinces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wards by province code
     */
    public function getWardsByProvince(string $provinceCode): JsonResponse
    {
        try {
            $province = Provinces::where('province_code', $provinceCode)->first();
            if (!$province) {
                return response()->json([
                    'success' => $province,
                    'message' => 'Province not found'
                ], 404);
            }

            $wards = Wards::where('province_code', $provinceCode)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $wards,
                'message' => 'Wards retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve wards',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
