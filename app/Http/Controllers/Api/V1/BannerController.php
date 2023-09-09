<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function get_banners(): JsonResponse
    {
        try {
            return response()->json(Banner::active()->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
