<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Model\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;

class SystemController extends Controller
{
    public function __construct(
        private Order $order
    )
    {
    }


    /**
     * @return JsonResponse
     */
    public function restaurant_data(): JsonResponse
    {
        $new_order = $this->order->where(['branch_id' => auth('branch')->id(), 'checked' => 0])->count();

        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }

    /**
     * @return Renderable
     */
    public function settings(): Renderable
    {
        return view('branch-views.settings');
    }
}
