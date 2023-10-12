<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\AddOn;
use App\Model\Branch;
use App\Model\Category;
use App\Model\CustomerAddress;
use App\Model\Notification;
use App\Model\Product;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ProductByBranch;
use App\Model\Table;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use function App\CentralLogics\translate;

class POSController extends Controller
{
    public function __construct(
        private Category        $category,
        private Order           $order,
        private User            $user,
        private Table           $table,
        private Product         $product,
        private Branch          $branch,
        private ProductByBranch $product_by_Branch,
    )
    {
    }


    /**
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
        $category = $request->query('category_id', 0);
        $categories = $this->category->active()->get();
        $keyword = $request->keyword;
        $key = explode(' ', $keyword);
        $selected_customer = $this->user->where('id', session('customer_id'))->first();
        $selected_table = $this->table->where('id', session('table_id'))->first();

        $products = $this->product
            ->with('product_by_branch')
            ->with(['product_by_branch' => function ($q) {
                $q->where(['is_available' => 1, 'branch_id' => auth('branch')->id()]);
            }])
            ->whereHas('product_by_branch', function ($q) {
                $q->where(['is_available' => 1, 'branch_id' => auth('branch')->id()]);
            })
            ->when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [['id' => (string)$request['category_id']]]);
            })
            ->when($keyword, function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->active()
            ->latest()
            ->paginate(Helpers::getPagination());

        $branch = $this->branch->find(auth('branch')->id());
        $tables = $this->table->where(['branch_id' => auth('branch')->id()])->get();

        return view('branch-views.pos.index', compact('categories', 'products', 'category', 'keyword', 'branch', 'tables', 'selected_table', 'selected_customer'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quick_view(Request $request): JsonResponse
    {
        $product = $this->product->with('product_by_branch')->findOrFail($request->product_id);

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos._quick-view-data', compact('product'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function variant_price(Request $request): array
    {
        $product = $this->product->find($request->id);
        $price = $product->price;
        $addon_price = 0;

        if ($request['addon_id']) {
            foreach ($request['addon_id'] as $id) {
                $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
            }
        }

        $branch_product = $this->product_by_Branch->where(['product_id' => $request->id, 'branch_id' => auth('branch')->id()])->first();

        if (isset($branch_product)) {
            $branch_product_variations = $branch_product->variations;
            $discount_data = [
                'discount_type' => $branch_product['discount_type'],
                'discount' => $branch_product['discount']
            ];

            if ($request->variations && count($branch_product_variations)) {
                $price_total = $branch_product['price'] + Helpers::new_variation_price($branch_product_variations, $request->variations);
                $price = $price_total - Helpers::discount_calculate($discount_data, $price_total);
            } else {
                $price = $branch_product['price'] - Helpers::discount_calculate($discount_data, $branch_product['price']);
            }
        }
        return array('price' => Helpers::set_symbol(($price * $request->quantity) + $addon_price));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_customers(Request $request): JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = $this->user
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->whereNotNull(['f_name', 'l_name', 'phone'])
            ->limit(8)
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        $data[] = (object)['id' => false, 'text' => translate('walk_in_customer')];

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_tax(Request $request): RedirectResponse
    {
        if ($request->tax < 0) {
            Toastr::error(translate('Tax_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->tax > 100) {
            Toastr::error(translate('Tax_can_not_be_more_than_100_percent'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);

        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_discount(Request $request): RedirectResponse
    {
        if (session()->has('cart')) {
            if (count(session()->get('cart')) < 1) {
                Toastr::error(translate('cart_empty_warning'));
                return back();
            }
        } else {
            Toastr::error(translate('cart_empty_warning'));
            return back();
        }

        if ($request->type == 'percent' && $request->discount < 0) {
            Toastr::error(translate('Extra_discount_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->type == 'percent' && $request->discount > 100) {
            Toastr::error(translate('Extra_discount_can_not_be_more_than_100_percent'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['extra_discount_type'] = $request->type;
        $cart['extra_discount'] = $request->discount;

        $request->session()->put('cart', $cart);
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $addon_price = 0;
        $variation_price = 0;
        $addon_total_tax = 0;

        $branch_product = $this->product_by_Branch->where(['product_id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        $branch_product_price = 0;
        $discount_data = [];

        if (isset($branch_product)) {
            $branch_product_variations = $branch_product->variations;

            if ($request->variations && count($branch_product_variations)) {
                foreach ($request->variations as $key => $value) {

                    if ($value['required'] == 'on' && isset($value['values']) == false) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select items from') . ' ' . $value['name'],
                        ]);
                    }
                    if (isset($value['values']) && $value['min'] != 0 && $value['min'] > count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select minimum ') . $value['min'] . translate(' For ') . $value['name'] . '.',
                        ]);
                    }
                    if (isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])) {
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select maximum ') . $value['max'] . translate(' For ') . $value['name'] . '.',
                        ]);
                    }
                }
                $variation_data = Helpers::get_varient($branch_product_variations, $request->variations);
                $variation_price = $variation_data['price'];
                $variations = $request->variations;

            }

            $branch_product_price = $branch_product['price'];
            $discount_data = [
                'discount_type' => $branch_product['discount_type'],
                'discount' => $branch_product['discount']
            ];

        }
        $price = $branch_product_price + $variation_price;
        $data['variation_price'] = $variation_price;

        $discount_on_product = Helpers::discount_calculate($discount_data, $price);

        $data['variations'] = $variations;
        $data['variant'] = $str;

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = $discount_on_product;
        $data['image'] = $product->image;
        $data['add_ons'] = [];
        $data['add_on_qtys'] = [];
        $data['add_on_prices'] = [];
        $data['add_on_tax'] = [];

        if ($request['addon_id']) {
            foreach ($request['addon_id'] as $id) {
                $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                $data['add_on_qtys'][] = $request['addon-quantity' . $id];

                $add_on = AddOn::find($id);
                $data['add_on_prices'][] = $add_on['price'];
                $add_on_tax = ($add_on['price'] * $add_on['tax']/100);
                $addon_total_tax += (($add_on['price'] * $add_on['tax']/100) * $request['addon-quantity' . $id]);
                $data['add_on_tax'][] = $add_on_tax;
            }
            $data['add_ons'] = $request['addon_id'];
        }

        $data['addon_price'] = $addon_price;
        $data['addon_total_tax'] = $addon_total_tax;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function place_order(Request $request): RedirectResponse
    {
        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) < 1) {
                Toastr::error(translate('cart_empty_warning'));
                return back();
            }
        } else {
            Toastr::error(translate('cart_empty_warning'));
            return back();
        }

        $order_type = session()->has('order_type') ? session()->get('order_type') : 'take_away';

        if ($order_type == 'dine_in'){
            if (!session()->has('table_id')){
                Toastr::error(translate('please select a table number'));
                return back();
            }
            if (!session()->has('people_number')){
                Toastr::error(translate('please enter people number'));
                return back();
            }

            $table = Table::find(session('table_id'));
            if (isset($table) && session('people_number') > $table->capacity  || session('people_number') < 1 ) {
                Toastr::error(translate('enter valid people number between 1 to '. $table->capacity));
                return back();
            }
        }

        $delivery_charge = 0;
        // store customer address for home delivery
        if ($order_type == 'home_delivery'){
            if (!session()->has('customer_id')){
                Toastr::error(translate('please select a customer'));
                return back();
            }

            if (!session()->has('address')){
                Toastr::error(translate('please select a delivery address'));
                return back();
            }

            $address_data = session()->get('address');
            $distance = $address_data['distance'] ?? 0;
            $delivery_type = Helpers::get_business_settings('delivery_management');
            if ($delivery_type['status'] == 1){
                $delivery_charge = Helpers::get_delivery_charge($distance);
            }else{
                $delivery_charge = Helpers::get_business_settings('delivery_charge');
            }

            $address = [
                'address_type' => 'Home',
                'contact_person_name' => $address_data['contact_person_name'],
                'contact_person_number' => $address_data['contact_person_number'],
                'address' => $address_data['address'],
                'floor' => $address_data['floor'],
                'road' => $address_data['road'],
                'house' => $address_data['house'],
                'longitude' => (string)$address_data['longitude'],
                'latitude' => (string)$address_data['latitude'],
                'user_id' => session()->get('customer_id'),
                'is_guest' => 0,
            ];
            $customer_address = CustomerAddress::create($address);
        }

        $cart = $request->session()->get('cart');
        $total_tax_amount = 0;
        $total_addon_price = 0;
        $total_addon_tax = 0;
        $product_price = 0;
        $order_details = [];

        $order_id = 100000 + $this->order->all()->count() + 1;
        if ($this->order->find($order_id)) {
            $order_id = $this->order->orderBy('id', 'DESC')->first()->id + 1;
        }

        $order = $this->order;
        $order->id = $order_id;

        $order->user_id = session()->get('customer_id') ?? null;
        $order->coupon_discount_title = $request->coupon_discount_title == 0 ? null : 'coupon_discount_title';
        $order->payment_status = ($order_type == 'take_away') ? 'paid' : (($order_type == 'dine_in' && $request->type != 'pay_after_eating') ? 'paid' : 'unpaid');
        $order->order_status = $order_type == 'take_away' ? 'delivered' : 'confirmed' ;
        $order->order_type = ($order_type == 'take_away') ? 'pos' : (($order_type == 'dine_in') ? 'dine_in' : (($order_type == 'home_delivery') ? 'delivery' : null));
        $order->coupon_code = $request->coupon_code ?? null;
        $order->payment_method = $request->type;
        $order->transaction_reference = $request->transaction_reference ?? null;
        $order->delivery_charge = $delivery_charge;
        $order->delivery_address_id = $order_type == 'home_delivery' ? $customer_address->id : null;
        $order->delivery_date = Carbon::now()->format('Y-m-d');
        $order->delivery_time = Carbon::now()->format('H:i:s');
        $order->order_note = null;
        $order->checked = 1;
        $order->created_at = now();
        $order->updated_at = now();

        $total_product_main_price = 0;

        // check if discount is more than total price
        $total_price_for_discount_validation = 0;

        foreach ($cart as $c) {
            if (is_array($c)) {
                $discount_on_product = 0;
                $discount = 0;
                $product_subtotal = ($c['price']) * $c['quantity'];
                $discount_on_product += ($c['discount'] * $c['quantity']);

                $total_price_for_discount_validation += $c['price'];

                $product = $this->product->find($c['id']);
                if ($product) {
                    $price = $c['price'];

                    $product = Helpers::product_data_formatting($product);
                    $addon_data = Helpers::calculate_addon_price(AddOn::whereIn('id', $c['add_ons'])->get(), $c['add_on_qtys']);

                    //*** addon quantity integer casting ***
                    array_walk($c['add_on_qtys'], function (&$add_on_qtys) {
                        $add_on_qtys = (int)$add_on_qtys;
                    });
                    //***end***

                    $branch_product = $this->product_by_Branch->where(['product_id' => $c['id'], 'branch_id' => auth('branch')->id()])->first();

                    $discount_data = [];
                    if (isset($branch_product)) {
                        $variation_data = Helpers::get_varient($branch_product->variations, $c['variations']);
                        $discount_data = [
                            'discount_type' => $branch_product['discount_type'],
                            'discount' => $branch_product['discount']
                        ];
                    }

                    $discount = Helpers::discount_calculate($discount_data, $price);
                    $variations = $variation_data['variations'];

                    $or_d = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => Helpers::tax_calculate($product, $price),
                        'discount_on_product' => $discount,
                        'discount_type' => 'discount_on_product',
                        //'variant' => json_encode($c['variant']),
                        'variation' => json_encode($variations),
                        'add_on_ids' => json_encode($addon_data['addons']),
                        'add_on_qtys' => json_encode($c['add_on_qtys']),
                        'add_on_prices' => json_encode($c['add_on_prices']),
                        'add_on_taxes' => json_encode($c['add_on_tax']),
                        'add_on_tax_amount' => $c['addon_total_tax'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $total_tax_amount += $or_d['tax_amount'] * $c['quantity'];
                    $total_addon_price += $addon_data['total_add_on_price'];

                    $total_addon_tax += $c['addon_total_tax'];

                    $product_price += $product_subtotal - $discount_on_product;
                    $total_product_main_price += $product_subtotal;
                    $order_details[] = $or_d;
                }
            }
        }

        $total_price = $product_price + $total_addon_price;
        if (isset($cart['extra_discount'])) {
            $extra_discount = $cart['extra_discount_type'] == 'percent' && $cart['extra_discount'] > 0 ? (($total_product_main_price * $cart['extra_discount']) / 100) : $cart['extra_discount'];
            $total_price -= $extra_discount;
        }
        if (isset($cart['extra_discount']) && $cart['extra_discount_type'] == 'amount') {
            if ($cart['extra_discount'] > $total_price_for_discount_validation) {
                Toastr::error(translate('discount_can_not_be_more_total_product_price'));
                return back();
            }
        }
        $tax = isset($cart['tax']) ? $cart['tax'] : 0;
        $total_tax_amount = ($tax > 0) ? (($total_price * $tax) / 100) : $total_tax_amount;
        try {
            $order->extra_discount = $extra_discount ?? 0;
            $order->total_tax_amount = $total_tax_amount;
            $order->order_amount = $total_price + $total_tax_amount + $order->delivery_charge+$total_addon_tax;

            $order->coupon_discount_amount = 0.00;
            $order->branch_id = auth('branch')->id();
            $order->table_id = session()->get('table_id');
            $order->number_of_people = session()->get('people_number');

            $order->save();

            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }
            OrderDetail::insert($order_details);

            session()->forget('cart');
            session(['last_order' => $order->id]);

            session()->forget('customer_id');
            session()->forget('branch_id');
            session()->forget('table_id');
            session()->forget('people_number');
            session()->forget('address');
            session()->forget('order_type');

            Toastr::success(translate('order_placed_successfully'));

            //send notification to kitchen
            if ($order->order_type == 'dine_in') {
                $notification = new Notification;
                $notification->title = "You have a new order from POS - (Order Confirmed). ";
                $notification->description = $order->id;
                $notification->status = 1;

                try {
                    Helpers::send_push_notif_to_topic($notification, "kitchen-{$order->branch_id}", 'general');
                    Toastr::success(translate('Notification sent successfully!'));
                } catch (\Exception $e) {
                    Toastr::warning(translate('Push notification failed!'));
                }
            }

            //send notification to customer for home delivery
            if ($order->order_type == 'delivery'){
                $value = Helpers::order_status_update_message('confirmed');
                $customer = $this->user->find($order->user_id);
                $fcm_token = $customer?->fcm_token;

                if ($value && isset($fcm_token)) {
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
                    Mail::to($customer->email)->send(new \App\Mail\OrderPlaced($order_id));
                }
            }

            return back();
        } catch (\Exception $e) {
            info($e);
        }

        //Toastr::warning(translate('failed_to_place_order'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function cart_items(): Renderable
    {
        return view('branch-views.pos._cart');
    }

    /**
     * @return JsonResponse
     */
    public function emptyCart(): JsonResponse
    {
        session()->forget('cart');
        Session::forget('table_id');
        Session::forget('customer_id');
        Session::forget('people_number');
        session()->forget('address');
        session()->forget('order_type');

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function order_list(Request $request): Renderable
    {
        $from = $request->from;
        $to = $request->to;
        $query_param = [];
        $search = $request['search'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);
        $query = $this->order->pos()->with(['customer', 'branch'])->where('branch_id', auth('branch')->id());

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $orders = $query->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('branch-views.pos.order.list', compact('orders', 'search', 'from', 'to'));
    }

    /**
     * @param $id
     * @return Renderable|RedirectResponse
     */
    public function order_details($id): Renderable|RedirectResponse
    {
        $order = $this->order->with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        if (isset($order)) {
            return view('branch-views.pos.order.order-view', compact('order'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function generate_invoice($id): JsonResponse
    {
        $order = $this->order->where('id', $id)->first();

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.order.invoice', compact('order'))->render(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function clear_session_data(): RedirectResponse
    {
        session()->forget('customer_id');
        session()->forget('branch_id');
        session()->forget('table_id');
        session()->forget('people_number');
        session()->forget('address');
        session()->forget('order_type');
        Toastr::success(translate('clear data successfully'));

        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function customer_store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        $user_phone = $this->user->where('phone', $request->phone)->first();
        if (isset($user_phone)){
            Toastr::error(translate('The phone is already taken'));
            return back();
        }

        $user_email = $this->user->where('email', $request->email)->first();
        if (isset($user_email)){
            Toastr::error(translate('The email is already taken'));
            return back();
        }

        $this->user->create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('password'),
        ]);

        Toastr::success(translate('customer added successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_keys(Request $request): JsonResponse
    {
        session()->put($request['key'], $request['value']);
        return response()->json($request['key'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function session_destroy(Request $request): JsonResponse
    {
        Session::forget('cart');
        Session::forget('table_id');
        Session::forget('customer_id');
        Session::forget('people_number');
        session()->forget('address');
        session()->forget('order_type');

        return response()->json();
    }

    public function addDeliveryInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 200);
        }

        $branch_id = auth('branch')->id();
        $branch = $this->branch->find($branch_id);
        $origin_lat = $branch['latitude'];
        $origin_lng = $branch['longitude'];
        $destination_lat = $request['latitude'];
        $destination_lng = $request['longitude'];

        $api_key = Helpers::get_business_settings('map_api_server_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin_lat . ',' . $origin_lng . '&destinations=' . $destination_lat . ',' . $destination_lng . '&key=' . $api_key);

        //return $response->json();
        $data = json_decode($response, true);
        $distance_value = $data['rows'][0]['elements'][0]['distance']['value'];
        $distance = $distance_value/1000;

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'Home',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'distance' => $distance,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('admin-views.pos._address', compact('address'))->render(),
        ]);
    }

    public function get_distance(Request $request): mixed
    {
        $request->validate([
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
        ]);

        $api_key = Helpers::get_business_settings('map_api_server_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request['origin_lat'] . ',' . $request['origin_lng'] . '&destinations=' . $request['destination_lat'] . ',' . $request['destination_lng'] . '&key=' . $api_key);

        return $response->json();
    }

    public function order_type_store(Request $request)
    {
        session()->put('order_type', $request['order_type']);
        return response()->json($request['order_type'], 200);
    }



}
