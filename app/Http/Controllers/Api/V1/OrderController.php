<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Model\AddOn;
use App\Model\CustomerAddress;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\ProductByBranch;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    public function __construct(
        private User            $user,
        private Order           $order,
        private OrderDetail     $order_detail,
        private ProductByBranch $product_by_branch,
        private Product         $product,

    )
    {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function track_order(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = $this->order->where(['id' => $request['order_id'], 'user_id' => $request->user()->id])->first();
        if (!isset($order)) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('Order not found!')]
                ]
            ], 404);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function place_order(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'payment_method' => 'required',
            'order_type' => 'required',
            'delivery_address_id' => 'required',
            'branch_id' => 'required',
            'delivery_time' => 'required',
            'delivery_date' => 'required',
            'distance' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->payment_method == 'wallet_payment' && Helpers::get_business_settings('wallet_status', false) != 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'payment_method', 'message' => translate('customer_wallet_status_is_disable')]
                ]
            ], 203);
        }

        $customer = $this->user->find($request->user()->id);

        if ($request->payment_method == 'wallet_payment' && $customer->wallet_balance < $request['order_amount']) {
            return response()->json([
                'errors' => [
                    ['code' => 'payment_method', 'message' => translate('you_do_not_have_sufficient_balance_in_wallet')]
                ]
            ], 203);
        }

        //order scheduling
        $preparation_time = Helpers::get_business_settings('default_preparation_time') ?? 0;
        if ($request['delivery_time'] == 'now') {
            $del_date = Carbon::now()->format('Y-m-d');
            $del_time = Carbon::now()->add($preparation_time, 'minute')->format('H:i:s');
        } else {
            $del_date = $request['delivery_date'];
            $del_time = Carbon::parse($request['delivery_time'])->add($preparation_time, 'minute')->format('H:i:s');
        }

        try {
            $order_id = 100000 + $this->order->all()->count() + 1;
            $or = [
                'id' => $order_id,
                'user_id' => $request->user()->id,
                'order_amount' => Helpers::set_price($request['order_amount']),
                'coupon_discount_amount' => Helpers::set_price($request->coupon_discount_amount),
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'payment_status' => ($request->payment_method == 'cash_on_delivery') ? 'unpaid' : 'paid',
                'order_status' => ($request->payment_method == 'cash_on_delivery') ? 'pending' : 'confirmed',
                'coupon_code' => $request['coupon_code'],
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference ?? null,
                'order_note' => $request['order_note'],
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'delivery_address_id' => $request->delivery_address_id,
                'delivery_date' => $del_date,
                'delivery_time' => $del_time,
                'delivery_address' => json_encode(CustomerAddress::find($request->delivery_address_id) ?? null),
                'delivery_charge' => $request['order_type'] != 'take_away' ? Helpers::get_delivery_charge($request['distance']) : 0,
                'preparation_time' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $total_tax_amount = 0;

            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);

                $branch_product = $this->product_by_branch->where(['product_id' => $c['product_id'], 'branch_id' => $request['branch_id']])->first();

                $discount_data = [];

                if ($branch_product) {
                    $branch_product_variations = $branch_product->variations;
                    $variations = [];
                    if (count($branch_product_variations)) {
                        $variation_data = Helpers::get_varient($branch_product_variations, $c['variations']);
                        $price = $branch_product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $branch_product['price'];
                    }
                    $discount_data = [
                        'discount_type' => $branch_product['discount_type'],
                        'discount' => $branch_product['discount'],
                    ];
                } else {
                    $product_variations = json_decode($product->variations, true);
                    $variations = [];
                    if (count($product_variations)) {
                        $variation_data = Helpers::get_varient($product_variations, $c['variations']);
                        $price = $product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
                    }
                    $discount_data = [
                        'discount_type' => $product['discount_type'],
                        'discount' => $product['discount'],
                    ];
                }

                $discount_on_product = Helpers::discount_calculate($discount_data, $price);

                /*calculation for addon and addon tax start*/
                $add_on_quantities = $c['add_on_qtys'];
                $add_on_prices = [];
                $add_on_taxes = [];

                foreach($c['add_on_ids'] as $key =>$id){
                    $addon = AddOn::find($id);
                    $add_on_prices[] = $addon['price'];
                    $add_on_taxes[] = ($addon['price']*$addon['tax'])/100;
                }

                $total_addon_tax = array_reduce(
                    array_map(function ($a, $b) {
                        return $a * $b;
                    }, $add_on_quantities, $add_on_taxes),
                    function ($carry, $item) {
                        return $carry + $item;
                    },
                    0
                );
                /*calculation for addon and addon tax end*/

                $or_d = [
                    'order_id' => $order_id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => $discount_on_product,
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    'variation' => json_encode($variations),
                    'add_on_ids' => json_encode($c['add_on_ids']),
                    'add_on_qtys' => json_encode($c['add_on_qtys']),
                    'add_on_prices' => json_encode($add_on_prices),
                    'add_on_taxes' => json_encode($add_on_taxes),
                    'add_on_tax_amount' => $total_addon_tax,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $total_tax_amount += $or_d['tax_amount'] * $c['quantity'];
                $this->order_detail->insert($or_d);

                $this->product->find($c['product_id'])->increment('popularity_count');
            }

            $or['total_tax_amount'] = $total_tax_amount;

            $o_id = $this->order->insertGetId($or);

            if ($request->payment_method == 'wallet_payment') {
                $amount = $or['order_amount'] + $or['delivery_charge'];
                CustomerLogic::create_wallet_transaction($or['user_id'], $amount, 'order_place', $or['id']);
            }

            $fcm_token = $request->user()->cm_firebase_token;
            $value = Helpers::order_status_update_message(($request->payment_method == 'cash_on_delivery') ? 'pending' : 'confirmed');
            try {
                //send push notification
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order_id,
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }

                //send email
                $emailServices = Helpers::get_business_settings('mail_config');
                if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                    Mail::to($request->user()->email)->send(new \App\Mail\OrderPlaced($order_id));
                }

            } catch (\Exception $e) {

            }

            if ($or['order_status'] == 'confirmed') {
                $data = [
                    'title' => translate('You have a new order - (Order Confirmed).'),
                    'description' => $order_id,
                    'order_id' => $order_id,
                    'image' => '',
                ];

                try {
                    Helpers::send_push_notif_to_topic($data, "kitchen-{$or['branch_id']}", 'general');

                } catch (\Exception $e) {
                    Toastr::warning(translate('Push notification failed!'));
                }
            }

            return response()->json([
                'message' => translate('order_success'),
                'order_id' => $order_id
            ], 200);

        } catch (\Exception $e) {
            return response()->json([$e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_list(Request $request): JsonResponse
    {
        $orders = $this->order->with(['customer', 'delivery_man.rating'])
            ->withCount('details')
            ->where(['user_id' => $request->user()->id])->get();

        $orders->map(function ($data) {
            $data['deliveryman_review_count'] = DMReview::where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();

            //is product available
            $order_id = $data->id;
            $order_details = $this->order_detail->where('order_id', $order_id)->first();
            $product_id = null;
            $product = null;
            if (isset($order_details))
                $product_id = $order_details->product_id;

            if (isset($product_id))
                $product = $this->product->find($product_id);

            $data['is_product_available'] = isset($product) ? 1 : 0;


            return $data;
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (integer)$data->details_count;
            return $data;
        }), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = $this->order_detail->with(['order'])
            ->withCount(['reviews'])
            ->where(['order_id' => $request['order_id']])
            ->whereHas('order', function ($q) use ($request){
                $q->where([ 'user_id' => $request->user()->id ]);
            })
            ->get();

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('Order not found!')]
                ]
            ], 404);
        }

        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel_order(Request $request): JsonResponse
    {
        if ($this->order->where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled'
            ]);
            return response()->json(['message' => translate('order_canceled')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('no_data_found')]
            ]
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_payment_method(Request $request): JsonResponse
    {
        if ($this->order->where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method']
            ]);
            return response()->json(['message' => translate('payment_method_updated')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('no_data_found')]
            ]
        ], 401);
    }
}
