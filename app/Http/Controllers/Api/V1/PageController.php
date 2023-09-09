<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\BusinessSetting;

class PageController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
    )
    {
    }


    public function index(): JsonResponse
    {
        $return_page = $this->business_setting->where(['key' => 'return_page'])->first();
        $refund_page = $this->business_setting->where(['key' => 'refund_page'])->first();
        $cancellation_page = $this->business_setting->where(['key' => 'cancellation_page'])->first();

        return response()->json([
            'return_page' => isset($return_page) ? json_decode($return_page->value, true) : null,
            'refund_page' => isset($refund_page) ? json_decode($refund_page->value, true) : null,
            'cancellation_page' => isset($cancellation_page) ? json_decode($cancellation_page->value, true) : null,
        ]);
    }

}
