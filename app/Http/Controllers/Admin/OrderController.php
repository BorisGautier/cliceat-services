<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\CustomerAddress;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\TableOrder;
use App\Models\OrderPartialPayment;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use DateTime;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class OrderController extends Controller
{
    public function __construct(
        private Order           $order,
        private TableOrder      $table_order,
        private CustomerAddress $customer_address,
        private OrderLogic      $order_logic,
        private User            $user,
        private BusinessSetting $business_setting,
        private DeliveryMan     $delivery_man
    )
    {}


    /**
     * @param Request $request
     * @param $status
     * @return Renderable
     */
    public function list(Request $request, $status): Renderable
    {
        //update daily stock
        Helpers::update_daily_product_stock();

        $query_param = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->order->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            })
                ->when($from && $to, function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                });
            $query_param = ['search' => $request['search']];
        }
        else {
            if (!session()->has('branch_filter')) {
                session()->put('branch_filter', 0);
            }
            $this->order->where(['checked' => 0])->update(['checked' => 1]);

            //all branch
            if (session('branch_filter') == 0) {
                if ($status == 'schedule') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->schedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });

                } elseif ($status != 'all') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['order_status' => $status])
                        ->notSchedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });

                } else {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });
                }
            } //selected branch
            else {
                if ($status == 'schedule') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where('branch_id', session('branch_filter'))
                        ->schedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });

                } elseif ($status != 'all') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['order_status' => $status, 'branch_id' => session('branch_filter')])
                        ->notSchedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });

                } else {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['branch_id' => session('branch_filter')])
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });
                }
            }
            $query_param = ['branch' => $request->branch, 'from' => $request->from, 'to' => $request->to];
        }

        $order_count = [
            'pending' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'pending'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'confirmed' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'confirmed'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'processing' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'processing'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'out_for_delivery' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'out_for_delivery'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'delivered' => $this->order
                ->notPos()
                ->notDineIn()
                ->where(['order_status' => 'delivered'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'canceled' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'canceled'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'returned' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'returned'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'failed' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'failed'])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
        ];

        $orders = $query->notPos()->notDineIn()->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.order.list', compact('orders', 'status', 'search', 'from', 'to', 'order_count'));
    }

    /**
     * @param $id
     * @return Renderable|RedirectResponse
     */
    public function details($id): Renderable|RedirectResponse
    {
        $order = $this->order->with(['details', 'customer', 'delivery_address', 'branch', 'delivery_man', 'order_partial_payments'])
            ->where(['id' => $id])
            ->first();

        if (!isset($order)) {
            Toastr::info(translate('No order found!'));
            return back();
        }

        $delivery_man = $this->delivery_man->where(['is_active'=>1])
            ->where(function($query) use ($order) {
                $query->where('branch_id', $order->branch_id)
                    ->orWhere('branch_id', 0);
            })
            ->get();

        //remaining delivery time
        $delivery_date_time = $order['delivery_date'] . ' ' . $order['delivery_time'];
        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remaining_time;

        return view('admin-views.order.order-view', compact('order', 'delivery_man'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $orders = $this->order
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            })->get();

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);

        if (in_array($order->order_status, ['delivered', 'failed'])) {
            Toastr::warning(translate('you_can_not_change_the_status_of_a_completed_order'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'], ['cash_on_delivery', 'wallet'])) {
            Toastr::warning(translate('add_your_payment_reference_first'));
            return back();
        }

        if (($request->order_status == 'delivered' || $request->order_status == 'out_for_delivery') && $order['delivery_man_id'] == null && $order['order_type'] != 'take_away') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }
        if ($request->order_status == 'completed' && $order->payment_status != 'paid') {
            Toastr::warning(translate('Please update payment status first!'));
            return back();
        }

        if ($request->order_status == 'delivered') {
            if ($order->is_guest == 0){
                if ($order->user_id) CustomerLogic::create_loyalty_point_transaction($order->user_id, $order->id, $order->order_amount, 'order_place');

                if ($order->transaction == null) {
                    $ol = $this->order_logic->create_transaction($order, 'admin');
                }

                $user = $this->user->find($order->user_id);
                $is_first_order = $this->order->where(['user_id' => $user->id, 'order_status' => 'delivered'])->count('id');
                $referred_by_user = $this->user->find($user->refer_by);

                if ($is_first_order < 2 && isset($user->refer_by) && isset($referred_by_user)) {
                    if ($this->business_setting->where('key', 'ref_earning_status')->first()->value == 1) {
                        CustomerLogic::referral_earning_wallet_transaction($order->user_id, 'referral_order_place', $referred_by_user->id);
                    }
                }
            }

            //partials payment transaction
            if ($order['payment_method'] == 'cash_on_delivery'){
                $partial_data = OrderPartialPayment::where(['order_id' => $order->id])->first();
                if ($partial_data){
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $order['id'];
                    $partial->paid_with = 'cash_on_delivery';
                    $partial->paid_amount = $partial_data->due_amount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }
        }

        $order->order_status = $request->order_status;
        $order->save();

        $message = Helpers::order_status_update_message($request->order_status);
        $local = $order->customer ? $order->customer->language_code : 'en';

        if ($local != 'en'){
            $status_key = Helpers::order_status_message_key($request->order_status);
            $translated_message = $this->business_setting->with('translations')->where(['key' => $status_key])->first();
            if (isset($translated_message->translations)){
                foreach ($translated_message->translations as $translation){
                    if ($local == $translation->locale){
                        $message = $translation->value;
                    }
                }
            }
        }
        $restaurant_name = Helpers::get_business_settings('restaurant_name');
        $delivery_man_name = $order->delivery_man ? $order->delivery_man->f_name. ' '. $order->delivery_man->l_name : '';
        $customer_name = $order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '';

        $value = Helpers::text_variable_data_format(value:$message, user_name: $customer_name, restaurant_name: $restaurant_name, delivery_man_name: $delivery_man_name, order_id: $order->id);

        $fcm_token = null;
        if (isset($order->customer)) {
            $fcm_token = $order->customer->cm_firebase_token;
        }

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order_status',
                ];
                if (isset($fcm_token)) {
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }

            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification send failed for Customer!'));
        }

        //delivery man notification
        if ($request->order_status == 'processing' || $request->order_status == 'out_for_delivery') {
            if (isset($order->delivery_man)) {
                $fcm_token = $order->delivery_man->fcm_token;
            }

            $value = translate('One of your order is on processing');
            $out_for_delivery_value = translate('One of your order is out for delivery');
            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $request->order_status == 'processing' ? $value : $out_for_delivery_value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    if (isset($fcm_token)) {
                        Helpers::send_push_notif_to_device($fcm_token, $data);
                    }
                }
            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification failed for DeliveryMan!'));
            }
        }

        //kitchen order notification
        if ($request->order_status == 'confirmed') {
            $data = [
                'title' => translate('You have a new order - (Order Confirmed).'),
                'description' => $order->id,
                'order_id' => $order->id,
                'image' => '',
            ];

            try {
                Helpers::send_push_notif_to_topic($data, "kitchen-{$order->branch_id}", 'general');

            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification failed!'));
            }
        }
        $table_order = $this->table_order->where(['id' => $order->table_order_id])->first();

        if ($request->order_status == 'completed' && $order->payment_status == 'paid') {
            if (isset($table_order->id)) {
                $orders = $this->order->where(['table_order_id' => $table_order->id])->get();
                $status = 1;
                foreach ($orders as $order) {
                    if ($order->order_status != 'completed') {
                        $status = 0;
                        break;
                    }
                }

                if ($status == 1) {
                    $table_order->branch_table_token_is_expired = 1;
                    $table_order->save();
                }
            }
        }

        if ($request->order_status == 'canceled') {

            if (isset($table_order->id)) {
                $orders = $this->order->where(['table_order_id' => $table_order->id])->get();
                $status = 1;
                foreach ($orders as $order) {
                    if ($order->order_status != 'canceled') {
                        $status = 0;
                        break;
                    }
                }

                if ($status == 1) {
                    $table_order->branch_table_token_is_expired = 1;
                    $table_order->save();
                }
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function preparation_time(Request $request, $id): RedirectResponse
    {
        $order = $this->order->with(['customer'])->find($id);
        $delivery_date_time = $order['delivery_date'] . ' ' . $order['delivery_time'];

        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');

        //if delivery time is not over
        if (strtotime(date('Y-m-d H:i:s')) < strtotime($remaining_time)) {
            $delivery_time = new DateTime($remaining_time); //time when preparation will be over
            $current_time = new DateTime(); // time now
            $interval = $delivery_time->diff($current_time);
            $remainingMinutes = $interval->i;
            $remainingMinutes += $interval->days * 24 * 60;
            $remainingMinutes += $interval->h * 60;
            //$order->preparation_time += ($request->extra_minute - $remainingMinutes);
            $order->preparation_time = 0;
        } else {
            //if delivery time is over
            $delivery_time = new DateTime($remaining_time);
            $current_time = new DateTime();
            $interval = $delivery_time->diff($current_time);
            $diffInMinutes = $interval->i;
            $diffInMinutes += $interval->days * 24 * 60;
            $diffInMinutes += $interval->h * 60;
            //$order->preparation_time += $diffInMinutes + $request->extra_minute;
            $order->preparation_time = 0;
        }

        $new_delivery_date_time = Carbon::now()->addMinutes($request->extra_minute);
        $order->delivery_date = $new_delivery_date_time->format('Y-m-d');
        $order->delivery_time = $new_delivery_date_time->format('H:i:s');

        $order->save();

        //notification send
        if ($order->is_guest == 0){
            $customer = $order->customer;

            $message = Helpers::order_status_update_message('customer_notify_message_for_time_change');
            $local = $order->customer ? $order->customer->language_code : 'en';

            if ($local != 'en'){
                $translated_message = $this->business_setting->with('translations')->where(['key' => 'customer_notify_message_for_time_change'])->first();
                if (isset($translated_message->translations)){
                    foreach ($translated_message->translations as $translation){
                        if ($local == $translation->locale){
                            $message = $translation->value;
                        }
                    }
                }
            }
            $restaurant_name = Helpers::get_business_settings('restaurant_name');
            $delivery_man_name = $order->delivery_man ? $order->delivery_man->f_name. ' '. $order->delivery_man->l_name : '';
            $customer_name = $order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '';

            $value = Helpers::text_variable_data_format(value:$message, user_name: $customer_name, restaurant_name: $restaurant_name, delivery_man_name: $delivery_man_name, order_id: $order->id);

            try {
                if ($value) {
                    $fcm_token = null;
                    $fcm_token = $customer?->cm_firebase_token;

                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                } else {
                    throw new \Exception(translate('failed'));
                }

            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification send failed for Customer!'));
            }
        }

        Toastr::success(translate('Order preparation time updated'));
        return back();
    }


    /**
     * @param $order_id
     * @param $delivery_man_id
     * @return JsonResponse
     */
    public function add_delivery_man($order_id, $delivery_man_id): JsonResponse
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }

        $order = $this->order->find($order_id);
        if ($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $customer_fcm_token = null;
        if (isset($order->customer)) {
            $customer_fcm_token = $order->customer->cm_firebase_token;
        }

        $message = Helpers::order_status_update_message('del_assign');
        $local = $order->delivery_man ? $order->delivery_man->language_code : 'en';

        if ($local != 'en'){
            $translated_message = $this->business_setting->with('translations')->where(['key' => 'delivery_boy_assign_message'])->first();
            if (isset($translated_message->translations)){
                foreach ($translated_message->translations as $translation){
                    if ($local == $translation->locale){
                        $message = $translation->value;
                    }
                }
            }
        }
        $restaurant_name = Helpers::get_business_settings('restaurant_name');
        $delivery_man_name = $order->delivery_man ? $order->delivery_man->f_name. ' '. $order->delivery_man->l_name : '';
        $customer_name = $order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '';

        $value = Helpers::text_variable_data_format(value:$message, user_name: $customer_name, restaurant_name: $restaurant_name, delivery_man_name: $delivery_man_name, order_id: $order->id);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order_id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);

                //send notification to customer
                if (isset($order->customer) && $customer_fcm_token) {
                    $local = $order->customer->language_code ?? 'en';
                    $notify_message = Helpers::order_status_update_message('customer_notify_message');
                    if ($local != 'en'){
                        $translated_message = $this->business_setting->with('translations')->where(['key' => 'customer_notify_message'])->first();
                        if (isset($translated_message->translations)){
                            foreach ($translated_message->translations as $translation){
                                if ($local == $translation->locale){
                                    $notify_message = $translation->value;
                                }
                            }
                        }
                    }

                    $data['description'] = Helpers::text_variable_data_format(value:$notify_message, user_name: $customer_name, restaurant_name: $restaurant_name, delivery_man_name: $delivery_man_name, order_id: $order->id);
                    Helpers::send_push_notif_to_device($customer_fcm_token, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for DeliveryMan!'));
        }

        return response()->json(['status' => true], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function payment_status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery' && $order['order_type'] != 'dine_in' && $order['payment_method'] != 'cash') {
            Toastr::warning(translate('Add your payment reference code first!'));
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();

        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update_shipping(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required|min:5|max:20',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($id) {
            $this->customer_address->where('id', $id)->update($address);
            Toastr::success(translate('Address updated!'));

        } else {
            $address = $this->customer_address;
            $address->contact_person_name = $request->input('contact_person_name');
            $address->contact_person_number = $request->input('contact_person_number');
            $address->address_type = $request->input('address_type');
            $address->address = $request->input('address');
            $address->longitude = $request->input('longitude');
            $address->latitude = $request->input('latitude');
            $address->user_id = $request->input('user_id');
            $address->house = $request->house;
            $address->floor = $request->floor;
            $address->address = $request->address;
            $address->save();
            $this->order->where('id', $request->input('order_id'))->update(['delivery_address_id' => $address->id]);
            Toastr::success(translate('Address added!'));
        }

        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function generate_invoice($id): Renderable
    {
        $order = $this->order->with(['order_partial_payments'])->where('id', $id)->first();
        return view('admin-views.order.invoice', compact('order'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function add_payment_ref_code(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function branch_filter($id): RedirectResponse
    {
        session()->put('branch_filter', $id);
        return back();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export_data(): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $orders = $this->order->all();
        return (new FastExcel($orders))->download('orders.xlsx');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string|RedirectResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export_excel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string|RedirectResponse
    {
        $status = $request->status;
        $query_param = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->order->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            })
                ->when($from && $to, function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                });
        } else {
            if (session()->has('branch_filter') == false) {
                session()->put('branch_filter', 0);
            }

            //all branch
            if (session('branch_filter') == 0) {
                if ($status == 'schedule') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->schedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });;

                } elseif ($status != 'all') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['order_status' => $status])
                        ->notSchedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });;

                } else {

                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });;
                }
            } //selected branch
            else {
                if ($status == 'schedule') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where('branch_id', session('branch_filter'))
                        ->schedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });;

                } elseif ($status != 'all') {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['order_status' => $status, 'branch_id' => session('branch_filter')])
                        ->notSchedule()
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });

                } else {
                    $query = $this->order
                        ->with(['customer', 'branch'])
                        ->where(['branch_id' => session('branch_filter')])
                        ->when($from && $to, function ($query) use ($from, $to) {
                            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                        });
                }
            }
        }

        $orders = $query->notPos()->notDineIn()->latest()->get();
        if ($orders->count() < 1) {
            Toastr::warning('No Data Available');
            return back();
        }

        $data = array();
        foreach ($orders as $key => $order) {
            $data[] = array(
                'SL' => ++$key,
                'Order ID' => $order->id,
                'Order Date' => date('d M Y h:m A', strtotime($order['created_at'])),
                'Customer Info' => $order['user_id'] == null ? 'Walk in Customer' : ($order->customer == null ? 'Customer Unavailable' : $order->customer['f_name'] . ' ' . $order->customer['l_name']),
                'Branch' => $order->branch ? $order->branch->name : 'Branch Deleted',
                'Total Amount' => Helpers::set_symbol($order['order_amount']),
                'Payment Status' => $order->payment_status == 'paid' ? 'Paid' : 'Unpaid',
                'Order Status' => $order['order_status'] == 'pending' ? 'Pending' : ($order['order_status'] == 'confirmed' ? 'Confirmed' : ($order['order_status'] == 'processing' ? 'Processing' : ($order['order_status'] == 'delivered' ? 'Delivered' : ($order['order_status'] == 'picked_up' ? 'Out For Delivery' : str_replace('_', ' ', $order['order_status']))))),
            );
        }

        return (new FastExcel($data))->download('Order_List.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax_change_delivery_time_date(Request $request): JsonResponse
    {
        $order = $this->order->where('id', $request->order_id)->first();
        if (!$order) {
            return response()->json(['status' => false]);
        }
        $order->delivery_date = $request->input('delivery_date') ?? $order->delivery_date;
        $order->delivery_time = $request->input('delivery_time') ?? $order->delivery_time;
        $order->save();

        return response()->json(['status' => true]);
    }

}
