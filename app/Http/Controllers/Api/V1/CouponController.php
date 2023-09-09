<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function __construct(
        private Coupon $coupon,
        private Order  $order

    )
    {
    }


    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $coupon = $this->coupon->active()->get();
            return response()->json($coupon, 200);

        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function apply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $coupon = $this->coupon->active()->where(['code' => $request['code']])->first();

            if (isset($coupon)) {
                //first order coupon type
                if ($coupon['coupon_type'] == 'first_order') {
                    $total = $this->order->where(['user_id' => $request->user()->id])->count();
                    if ($total == 0) {
                        return response()->json($coupon, 200);
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 401);
                    }
                }

                //default coupon type
                if ($coupon['limit'] == null) {
                    return response()->json($coupon, 200);
                } else {
                    $total = $this->order->where(['user_id' => $request->user()->id, 'coupon_code' => $request['code']])->count();
                    if ($total < $coupon['limit']) {
                        return response()->json($coupon, 200);
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('coupon_limit_over')]
                            ]
                        ], 401);
                    }
                }

            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('coupon_not_found')]
                    ]
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }
}
