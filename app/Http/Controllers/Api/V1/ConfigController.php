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

class ConfigController extends Controller
{
    public function __construct(
        private Currency        $currency,
        private Branch          $branch,
        private TimeSchedule    $time_schedule,
        private BusinessSetting $business_setting,
    )
    {
    }


    /**
     * @return JsonResponse
     */
    public function configuration(): JsonResponse
    {
        $currency_symbol = $this->currency->where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode($this->business_setting->where(['key' => 'cash_on_delivery'])->first()->value, true);
        $dp = json_decode($this->business_setting->where(['key' => 'digital_payment'])->first()->value, true);

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

        $digital_payment_status = $this->business_setting->where(['key' => 'digital_payment'])->first()->value;
        $digital_payment_status_value = json_decode($digital_payment_status, true);

        $active_method_list = [];

        if ($digital_payment_status_value['status']) {
            $digital_payment_methods = ['ssl_commerz_payment', 'razor_pay', 'paypal', 'stripe', 'senang_pay', 'paystack', 'bkash', 'paymob', 'flutterwave', 'mercadopago'];
            $data = $this->business_setting->whereIn('key', $digital_payment_methods)->get();
            foreach ($data as $d) {
                $value = json_decode($d['value'], true);
                if ($value['status'] == 1) {
                    $active_method_list[] = $d['key'];
                }
            }
        }

        $cookies_config = Helpers::get_business_settings('cookies');
        $cookies_management = array(
            "status" => (int)$cookies_config['status'],
            "text" => $cookies_config['text'],
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
                'product_image_url' => asset('storage/app/public/product'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'category_banner_image_url' => asset('storage/app/public/category/banner'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'restaurant_image_url' => asset('storage/app/public/restaurant'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
                'promotional_url' => asset('storage/app/public/promotion'),
                'kitchen_image_url' => asset('storage/app/public/kitchen'),
                'branch_image_url' => asset('storage/app/public/branch'),
            ],
            'currency_symbol' => $currency_symbol,
            'delivery_charge' => (float)$this->business_setting->where(['key' => 'delivery_charge'])->first()->value,
            'delivery_management' => $delivery_management,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'branches' => $this->branch->all(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status', 'image', 'cover_image']),
            'terms_and_conditions' => $this->business_setting->where(['key' => 'terms_and_conditions'])->first()->value,
            'privacy_policy' => $this->business_setting->where(['key' => 'privacy_policy'])->first()->value,
            'about_us' => $this->business_setting->where(['key' => 'about_us'])->first()->value,
            /*'terms_and_conditions' => route('terms-and-conditions'),
            'privacy_policy' => route('privacy-policy'),
            'about_us' => route('about-us')*/
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
            'digital_payment_status' => (integer)$digital_payment_status_value['status'],
            'active_payment_method_list' => $active_method_list,
            'whatsapp' => json_decode($this->business_setting->where(['key' => 'whatsapp'])->first()->value, true),
            'cookies_management' => $cookies_management,
            'toggle_dm_registration' => (integer)(Helpers::get_business_settings('dm_self_registration') ?? 0) ,
            'is_veg_non_veg_active' => (integer)(Helpers::get_business_settings('toggle_veg_non_veg') ?? 0) ,
            'otp_resend_time' => Helpers::get_business_settings('otp_resend_time') ?? 60

        ], 200);
    }
}
