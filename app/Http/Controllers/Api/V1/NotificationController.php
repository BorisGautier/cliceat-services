<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function get_notifications(): JsonResponse
    {
        try {
            return response()->json(Notification::active()->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
