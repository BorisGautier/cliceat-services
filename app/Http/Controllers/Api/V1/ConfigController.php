<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Model\TimeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class ConfigController extends Controller
{
    private $map_key;
    public function __construct(
        private Currency        $currency,
        private Branch          $branch,
        private TimeSchedule    $time_schedule,
        private BusinessSetting $business_setting,
    ){
        $this->map_key = Helpers::get_business_settings('map_api_client_key');
    }


    /**
     * @return JsonResponse
     */
    public function configuration(): JsonResponse
    {
        $dp = json_decode($this->business_setting->where(['key' => 'digital_payment'])->first()->value, true);

        //addon settings publish status
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $active_addon_payment_lists = $published_status == 1 ? $this->getPaymentMethods() : $this->getDefaultPaymentMethods();

        $digital_payment_status = $this->business_setting->where(['key' => 'digital_payment'])->first()->value;
        $digital_payment_status_value = json_decode($digital_payment_status, true);

        $digital_payment_infos = array(
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'plugin_payment_gateways' =>  $published_status ? "true" : "false",
            'default_payment_gateways' =>  $published_status ? "false" : "true"
        );

        $currency_symbol = $this->currency->where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode($this->business_setting->where(['key' => 'cash_on_delivery'])->first()->value, true);

        $dm_config = Helpers::get_business_settings('delivery_management');
        $delivery_management = array(
            "status" => (int)$dm_config['status'],
            "min_shipping_charge" => (float)$dm_config['min_shipping_charge'],
            "shipping_per_km" => (float)$dm_config['shipping_per_km'],
        );
        $play_store_config = Helpers::get_business_settings('play_store_config');
        $app_store_config = Helpers::get_business_settings('app_store_config');

        //schedule time
        $schedules = $this->time_schedule->select('day', 'opening_time', 'closing_time')->get();
        $branch_promotion = $this->branch->with('branch_promotion')->where(['branch_promotion_status' => 1])->get();

        $google = $this->business_setting->where(['key' => 'google_social_login'])->first()->value ?? 0;
        $facebook = $this->business_setting->where(['key' => 'facebook_social_login'])->first()->value ?? 0;

        //addon settings publish status
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $cookies_config = Helpers::get_business_settings('cookies');
        $cookies_management = array(
            "status" => (int)$cookies_config['status'],
            "text" => $cookies_config['text'],
        );

        $offline_payment = json_decode($this->business_setting->where(['key' => 'offline_payment'])->first()->value, true);
        $apple = Helpers::get_business_settings('apple_login');
        $apple_login = array(
            'login_medium' => $apple['login_medium'],
            'status' => (integer)$apple['status'],
            'client_id' => $apple['client_id']
        );

        return response()->json([
            'restaurant_name' => $this->business_setting->where(['key' => 'restaurant_name'])->first()->value,
            'restaurant_open_time' => $this->business_setting->where(['key' => 'restaurant_open_time'])->first()->value,
            'restaurant_close_time' => $this->business_setting->where(['key' => 'restaurant_close_time'])->first()->value,
            'restaurant_schedule_time' => $schedules,
            'restaurant_logo' => $this->business_setting->where(['key' => 'logo'])->first()->value,
            'restaurant_address' => $this->business_setting->where(['key' => 'address'])->first()->value,
            'restaurant_phone' => $this->business_setting->where(['key' => 'phone'])->first()->value,
            'restaurant_email' => $this->business_setting->where(['key' => 'email_address'])->first()->value,
            'restaurant_location_coverage' => $this->branch->where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float)$this->business_setting->where(['key' => 'minimum_order_value'])->first()->value,

            'base_urls' => [
                'product_image_url' => asset('storage/product'),
                'customer_image_url' => asset('storage/profile'),
                'banner_image_url' => asset('storage/banner'),
                'category_image_url' => asset('storage/category'),
                'category_banner_image_url' => asset('storage/category/banner'),
                'review_image_url' => asset('storage/review'),
                'notification_image_url' => asset('storage/notification'),
                'restaurant_image_url' => asset('storage/restaurant'),
                'delivery_man_image_url' => asset('storage/delivery-man'),
                'chat_image_url' => asset('storage/conversation'),
                'promotional_url' => asset('storage/promotion'),
                'kitchen_image_url' => asset('storage/kitchen'),
                'branch_image_url' => asset('storage/branch'),
                'gateway_image_url' => asset('storage/payment_modules/gateway_image'),
                'payment_image_url' => asset('assets/admin/img/payment'),
            ],
            'currency_symbol' => $currency_symbol,
            'delivery_charge' => (float)$this->business_setting->where(['key' => 'delivery_charge'])->first()->value,
            'delivery_management' => $delivery_management,
            'branches' => $this->branch->all(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status', 'image', 'cover_image']),
            'terms_and_conditions' => $this->business_setting->where(['key' => 'terms_and_conditions'])->first()->value,
            'privacy_policy' => $this->business_setting->where(['key' => 'privacy_policy'])->first()->value,
            'about_us' => $this->business_setting->where(['key' => 'about_us'])->first()->value,
            'email_verification' => (boolean)Helpers::get_business_settings('email_verification') ?? 0,
            'phone_verification' => (boolean)Helpers::get_business_settings('phone_verification') ?? 0,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'country' => Helpers::get_business_settings('country') ?? 'BD',
            'self_pickup' => (boolean)Helpers::get_business_settings('self_pickup') ?? 1,
            'delivery' => (boolean)Helpers::get_business_settings('delivery') ?? 1,
            'play_store_config' => [
                "status" => isset($play_store_config) ? (boolean)$play_store_config['status'] : false,
                "link" => isset($play_store_config) ? $play_store_config['link'] : null,
                "min_version" => isset($play_store_config) && array_key_exists('min_version', $app_store_config) ? $play_store_config['min_version'] : null
            ],
            'app_store_config' => [
                "status" => isset($app_store_config) ? (boolean)$app_store_config['status'] : false,
                "link" => isset($app_store_config) ? $app_store_config['link'] : null,
                "min_version" => isset($app_store_config) && array_key_exists('min_version', $app_store_config) ? $app_store_config['min_version'] : null
            ],
            'social_media_link' => SocialMedia::orderBy('id', 'desc')->active()->get(),
            'software_version' => (string)env('SOFTWARE_VERSION') ?? null,
            'footer_text' => Helpers::get_business_settings('footer_text'),
            'decimal_point_settings' => (int)(Helpers::get_business_settings('decimal_point_settings') ?? 2),
            'schedule_order_slot_duration' => (int)(Helpers::get_business_settings('schedule_order_slot_duration') ?? 30),
            'time_format' => (string)(Helpers::get_business_settings('time_format') ?? '12'),
            'promotion_campaign' => $branch_promotion,
            'social_login' => [
                'google' => (integer)$google,
                'facebook' => (integer)$facebook,
            ],
            'wallet_status' => (integer)$this->business_setting->where(['key' => 'wallet_status'])->first()->value,
            'loyalty_point_status' => (integer)$this->business_setting->where(['key' => 'loyalty_point_status'])->first()->value,
            'ref_earning_status' => (integer)$this->business_setting->where(['key' => 'ref_earning_status'])->first()->value,
            'loyalty_point_item_purchase_point' => (float)$this->business_setting->where(['key' => 'loyalty_point_item_purchase_point'])->first()->value,
            'loyalty_point_exchange_rate' => (float)($this->business_setting->where(['key' => 'loyalty_point_exchange_rate'])->first()->value ?? 0),
            'loyalty_point_minimum_point' => (float)($this->business_setting->where(['key' => 'loyalty_point_minimum_point'])->first()->value ?? 0),
            'whatsapp' => json_decode($this->business_setting->where(['key' => 'whatsapp'])->first()->value, true),
            'cookies_management' => $cookies_management,
            'toggle_dm_registration' => (integer)(Helpers::get_business_settings('dm_self_registration') ?? 0) ,
            'is_veg_non_veg_active' => (integer)(Helpers::get_business_settings('toggle_veg_non_veg') ?? 0) ,
            'otp_resend_time' => Helpers::get_business_settings('otp_resend_time') ?? 60,
            'digital_payment_info' => $digital_payment_infos,
            'digital_payment_status' => (integer)$digital_payment_status_value['status'],
            'active_payment_method_list' => $active_addon_payment_lists,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'offline_payment' => $offline_payment['status'] == 1 ? 'true' : 'false',
            'guest_checkout' => (integer)(Helpers::get_business_settings('guest_checkout') ?? 0),
            'partial_payment' => (integer)(Helpers::get_business_settings('partial_payment') ?? 0),
            'partial_payment_combine_with' => (string)Helpers::get_business_settings('partial_payment_combine_with'),
            'add_fund_to_wallet' => (integer)(Helpers::get_business_settings('add_fund_to_wallet') ?? 0),
            'apple_login' => $apple_login

        ], 200);
    }

    private function getPaymentMethods()
    {
        // Check if the addon_settings table exists
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')->where('settings_type', 'payment_config')->get();
        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additional_data?->gateway_title,
                    'gateway_image' => $additional_data?->gateway_image
                ];
            }
        }
        return $data;
    }

    private function getDefaultPaymentMethods()
    {
        // Check if the addon_settings table exists
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')
            ->whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paystack','paymob_accept','flutterwave','bkash','mercadopago'])
            ->get();

        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additional_data?->gateway_title,
                    'gateway_image' => $additional_data?->gateway_image
                ];
            }
        }
        return $data;
    }

    public function direction_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_long' => 'required',
            'destination_lat' => 'required',
            'destination_long' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json?origin='.$request['origin_lat'].','.$request['origin_long'].'&destination='.$request['destination_lat'].','.$request['destination_long'].'&mode=driving&key='.$this->map_key);
        return $response->json();
    }
}
