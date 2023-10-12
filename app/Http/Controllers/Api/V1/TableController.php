<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\AddOn;
use App\Model\Notification;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\ProductByBranch;
use App\Model\Table;
use App\Model\TableOrder;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CentralLogics\translate;

class TableController extends Controller
{
    public function __construct(
        private Table           $table,
        private Order           $order,
        private OrderDetail     $order_detail,
        private TableOrder      $table_order,
        private Notification    $notification,
        private Product         $product,
        private ProductByBranch $product_by_branch
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        //update daily stock
        Helpers::update_daily_product_stock();

        $tables = $this->table->where('is_active', 1)->paginate(Helpers::getPagination());
        return response()->json($tables, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function place_order(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'table_id' => 'required',
            'branch_id' => 'required',
            'delivery_time' => 'required',
            'delivery_date' => 'required',
            'number_of_people' => 'required',
            'payment_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        //update daily stock
        Helpers::update_daily_product_stock();

        try {
            $order = $this->order;
            $order->id = 100000 + $this->order->all()->count() + 1;
            $order->user_id = $request->id;
            $order->order_amount = Helpers::set_price($request['order_amount']);
            $order->coupon_discount_amount = Helpers::set_price($request->coupon_discount_amount);
            $order->coupon_discount_title = $request->coupon_discount_title == 0 ? null : 'coupon_discount_title';
            $order->payment_method = $request->payment_method;
            $order->payment_status = $request->payment_status;
            $order->order_status = 'confirmed';
            $order->coupon_code = $request['coupon_code'];
            $order->transaction_reference = $request->transaction_reference ?? null;
            $order->order_note = $request['order_note'];
            $order->order_type = 'dine_in';
            $order->branch_id = $request['branch_id'];
            $order->checked = 1;

            $order->delivery_date = Carbon::now()->format('Y-m-d');
            $order->delivery_time = Carbon::now()->format('H:i:s');

            $order->preparation_time = Helpers::get_business_settings('default_preparation_time') ?? 0;

            $order->table_id = $request['table_id'];
            $order->number_of_people = $request['number_of_people'];

            $order->created_at = now();
            $order->updated_at = now();

            $token_check = $this->table_order->where(['table_id' => $request->table_id, 'branch_table_token' => $request->branch_table_token, 'branch_table_token_is_expired' => '0'])->first();

            if (isset($token_check)) {
                $order->table_order_id = $token_check->id;

                if ($request->payment_status == 'paid') {
                    $check_unpaid_orders = $this->order->where(['table_order_id' => $token_check->id])->get();
                    foreach ($check_unpaid_orders as $check_unpaid_order) {
                        $check_unpaid_order->payment_status = 'paid';
                        $check_unpaid_order->save();
                    }
                }
            } else {
                $table_order = $this->table_order;
                $table_order->table_id = $request->table_id;
                $table_order->branch_table_token = Str::random(50);
                $table_order->branch_table_token_is_expired = 0;
                $table_order->save();

                $order->table_order_id = $table_order->id;
            }

            $order->save();

            $total_addon_tax = 0;

            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);

                //new variation price calculation
                $branch_product = $this->product_by_branch->where(['product_id' => $c['product_id'], 'branch_id' => $request['branch_id']])->first();

                //daily and fixed stock quantity validation
                if($branch_product->stock_type == 'daily' || $branch_product->stock_type == 'fixed' ){
                    $available_stock = $branch_product->stock - $branch_product->sold_quantity;
                    if ($available_stock < $c['quantity']){
                        return response()->json(['errors' => [['code' => 'stock', 'message' => translate('stock limit exceeded')]]], 403);
                    }
                }

                $discount_data = [];

                if ($branch_product) {
                    $branch_product_variations = $branch_product->variations;
                    $variations = [];
                    if (count($branch_product_variations)) {
                        $variation_data = Helpers::get_varient($branch_product_variations, $c['variations']);
                        $price = $branch_product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
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

                $order_d = [
                    'order_id' => $order->id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => $discount_on_product,
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    //'variation' => array_key_exists('variation', $c) ? json_encode($c['variation']) : json_encode([]),
                    'variation' => json_encode($variations),
                    'add_on_ids' => json_encode($c['add_on_ids']),
                    'add_on_qtys' => json_encode($c['add_on_qtys']),
                    'add_on_prices' => json_encode($add_on_prices),
                    'add_on_taxes' => json_encode($add_on_taxes),
                    'add_on_tax_amount' => $total_addon_tax,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $this->order_detail->insert($order_d);

                //update product popularity point
                $this->product->find($c['product_id'])->increment('popularity_count');

                //daily and fixed stock quantity update
                if($branch_product->stock_type == 'daily' || $branch_product->stock_type == 'fixed' ){
                    $branch_product->sold_quantity += $c['quantity'];
                    $branch_product->save();
                }
            }

            //send notification to kitchen
            $notification = $this->notification;
            $notification->title = "You have a new order from Table - (Order Confirmed). ";
            $notification->description = $order->id;
            $notification->status = 1;

            try {
                Helpers::send_push_notif_to_topic($notification, "kitchen-{$order->branch_id}", 'general');
                Toastr::success(translate('Notification sent successfully!'));
            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification failed!'));
            }
            $token = $this->table_order->where('id', $order['table_order_id'])->first();

            return response()->json([
                'message' => translate('order_placed_successfully!!'),
                'order_id' => $order->id,
                'branch_table_token' => $token->branch_table_token,
            ], 200);


        } catch (\Exception $e) {
            return response()->json([$e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'branch_table_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $branch_table_token = $this->table_order->where(['branch_table_token' => $request->branch_table_token])->first();

        if (isset($branch_table_token)) {
            $order = $this->order->where(['id' => $request->order_id, 'table_order_id' => $branch_table_token->id])->first();
            $details = $this->order_detail->where(['order_id' => $order->id])->get();
            $details = isset($details) ? Helpers::order_details_formatter($details) : null;

            return response()->json([
                'order' => $order,
                'details' => $details
            ], 200);
        }

        return response()->json(['message' => 'No data found']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function table_order_list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_table_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $token_check = $this->table_order->where(['branch_table_token' => $request->branch_table_token, 'branch_table_token_is_expired' => '0'])->first();
        if (isset($token_check)) {
            $order = $this->order->where(['table_order_id' => $token_check->id])->get();
            return response()->json(['order' => $order], 200);
        }

        return response()->json(['message' => 'no data found']);
    }
}
