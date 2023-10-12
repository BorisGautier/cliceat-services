<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OfflinePaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflinePaymentMethodController extends Controller
{
    public function __construct(
        private OfflinePaymentMethod $offline_payment_method
    ){}

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $methods = $this->offline_payment_method->latest()->active()->get();
        return response()->json($methods, 200);
    }
}
