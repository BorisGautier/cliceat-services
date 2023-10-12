<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\AddOn;
use App\Model\Admin;
use App\Model\AdminRole;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Traits\ActivationClass;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Brian2694\Toastr\Facades\Toastr;


class UpdateController extends Controller
{
    use ActivationClass;
    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        Helpers::setEnvironmentValue('SOFTWARE_ID', 'MzAzMjAzMzg=');
        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('APP_MODE', 'live');
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '1.5');
        Helpers::setEnvironmentValue('APP_NAME', 'ClicEat');

        $data = $this->actch();
        try {
            if (!$data->getData()->active) {
                $remove = array("http://", "https://", "www.");
                $url = str_replace($remove, "", url('/'));
                $activation_url = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLjZhbXRlY2guY29t');
                $activation_url .= '?username=' . $request['username'];
                $activation_url .= '&purchase_code=' . $request['purchase_key'];
                $activation_url .= '&domain=' . $url . '&';
                return redirect($activation_url);
            }
        } catch (\Exception $exception) {
            Toastr::error('verification failed! try again');
            return back();
        }

      //  Artisan::call('migrate', ['--force' => true]); 

        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

      //  Artisan::call('optimize:clear');


        DB::table('business_settings')->updateOrInsert(['key' => 'self_pickup'], [
            'value' => 1
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'delivery'], [
            'value' => 1
        ]);
        

       


        //for modified language [new multi lang in admin]
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        $lang_flag = false;

        foreach ($languages as $key => $language) {
            if(gettype($language) != 'array') {
                $lang = [
                    'id' => $key+1,
                    'name' => $language,
                    'direction' => 'ltr',
                    'code' => $language,
                    'status' => 1,
                    'default' => $language == 'en' ? true : false,
                ];

                array_push($lang_array, $lang);
                $lang_flag = true;
            }
        }
        if ($lang_flag == true) {
            BusinessSetting::where('key', 'language')->update([
                'value' => $lang_array
            ]);
        }
        //lang end

        //*** auto run script ***
        try {
            $order_details = OrderDetail::get();
            foreach($order_details as $order_detail) {

                //*** addon quantity integer casting script ***
                $qtys = json_decode($order_detail['add_on_qtys'], true);
                array_walk($qtys, function (&$add_on_qtys) {
                    $add_on_qtys = (int) $add_on_qtys;
                });
                $order_detail['add_on_qtys'] = json_encode($qtys);
                //*** end ***

                //*** variation(POS) structure change script ***
                $variation = json_decode($order_detail['variation'], true);
                $product = json_decode($order_detail['product_details'], true);

                if(count($variation) > 0) {
                    $result = [];
                    if(!array_key_exists('price', $variation[0])) {
                        $result[] = [
                            'type' => $variation[0]['Size'],
                            'price' => Helpers::set_price($product['price'])
                        ];
                    }
                    if(count($result) > 0) {
                        $order_detail['variation'] = json_encode($result);
                    }

                }
                //*** end ***

                $order_detail->save();


            }
        } catch (\Exception $exception) {
            //
        }
        //*** end ***

        //user referral code
        $users = User::whereNull('refer_code')->get();
        foreach ($users as $user) {
            $user->refer_code = Helpers::generate_referer_code();
            $user->save();
        }

        if (!BusinessSetting::where(['key' => 'offline_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'offline_payment'], [
                'value' => '{"status":"1"}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'guest_checkout'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'guest_checkout'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment_combine_with'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment_combine_with'], [
                'value' => 'all'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'qr_code'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'qr_code'], [
                'value' => '{"branch_id":"1","logo":"","title":"","description":"","opening_time":"","closing_time":"","phone":"","website":"","social_media":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'apple_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":0}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_wallet_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_wallet_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_wallet_bonus_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_wallet_bonus_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        //new database table
       /* if (!Schema::hasTable('addon_settings')) {
            $sql = File::get(base_path($request['path'] . 'database/addon_settings.sql'));
            DB::unprepared($sql);
        
        }

        if (!Schema::hasTable('payment_requests')) {
            $sql = File::get(base_path($request['path'] . 'database/payment_requests.sql'));
            DB::unprepared($sql);
        
        }*/

        $this->set_data();
        

        return redirect('/admin/auth/login');
    }

    private function set_data(){
        try{
            $gateway= [
                'ssl_commerz_payment',
                'razor_pay',
                'paypal',
                'stripe',
                'senang_pay',
                'paystack',
                'bkash',
                'paymob',
                'flutterwave',
                'mercadopago',
            ];


            $data= BusinessSetting::whereIn('key',$gateway)->pluck('value','key')->toArray();

            foreach($data as $key => $value){
                $gateway=$key;
                if($key == 'ssl_commerz_payment' ){
                    $gateway='ssl_commerz';
                }
                if($key == 'paymob' ){
                    $gateway='paymob_accept';
                }

                $decoded_value= json_decode($value , true);
                $data= ['gateway' => $gateway ,
                    'mode' =>  isset($decoded_value['status']) == 1  ?  'live': 'test'
                ];

                $additional_data =[];

                if ($gateway == 'ssl_commerz') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'store_id' => $decoded_value['store_id'],
                        'store_password' => $decoded_value['store_password'],
                    ];
                } elseif ($gateway == 'paypal') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'client_id' => $decoded_value['paypal_client_id'],
                        'client_secret' => $decoded_value['paypal_secret'],
                    ];
                } elseif ($gateway == 'stripe') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['api_key'],
                        'published_key' => $decoded_value['published_key'],
                    ];
                } elseif ($gateway == 'razor_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['razor_key'],
                        'api_secret' => $decoded_value['razor_secret'],
                    ];
                } elseif ($gateway == 'senang_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'secret_key' => $decoded_value['secret_key'],
                        'merchant_id' => $decoded_value['merchant_id'],
                    ];
                } elseif ($gateway == 'paystack') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => $decoded_value['paymentUrl'],
                        'public_key' => $decoded_value['publicKey'],
                        'secret_key' => $decoded_value['secretKey'],
                        'merchant_email' => $decoded_value['merchantEmail'],
                    ];
                } elseif ($gateway == 'paymob_accept') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'api_key' => $decoded_value['api_key'],
                        'iframe_id' => $decoded_value['iframe_id'],
                        'integration_id' => $decoded_value['integration_id'],
                        'hmac' => $decoded_value['hmac'],
                    ];
                } elseif ($gateway == 'mercadopago') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'access_token' => $decoded_value['access_token'],
                        'public_key' => $decoded_value['public_key'],
                    ];
                } elseif ($gateway == 'flutterwave') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'secret_key' => $decoded_value['secret_key'],
                        'public_key' => $decoded_value['public_key'],
                        'hash' => $decoded_value['hash'],
                    ];
                } elseif ($gateway == 'bkash') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'app_key' => $decoded_value['api_key'],
                        'app_secret' => $decoded_value['api_secret'],
                        'username' => $decoded_value['username'],
                        'password' => $decoded_value['password'],
                    ];
                }

                $credentials= json_encode(array_merge($data, $additional_data));

                $payment_additional_data=['gateway_title' => ucfirst(str_replace('_',' ',$gateway)),
                    'gateway_image' => null];

                DB::table('addon_settings')->updateOrInsert(['key_name' => $gateway, 'settings_type' => 'payment_config'], [
                    'key_name' => $gateway,
                    'live_values' => $credentials,
                    'test_values' => $credentials,
                    'settings_type' => 'payment_config',
                    'mode' => isset($decoded_value['status']) == 1  ?  'live': 'test',
                    'is_active' => isset($decoded_value['status']) == 1  ?  1: 0 ,
                    'additional_data' => json_encode($payment_additional_data),
                ]);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return true;
        }
        return true;
    }
}
