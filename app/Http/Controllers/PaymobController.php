<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\Currency;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use function App\CentralLogics\translate;

class PaymobController extends Controller
{
    /**
     * @param $url
     * @param $json
     * @return mixed
     */
    protected function cURL($url, $json): mixed
    {
        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function GETcURL($url): mixed
    {
        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function credit(Request $request): RedirectResponse
    {
        $payment_data = [
            'callback' => $request['callback'],
            'customer_id' => $request['customer_id'],
            'order_amount' => $request['order_amount']
        ];

        session()->put('customer_id', $request['customer_id']);
        session()->put('callback', $request['callback']);

        $currency_code = Currency::where(['currency_code' => 'EGP'])->first();
        if (!isset($currency_code)) {
            Toastr::error(translate('paymob_supports_EGP_currency'));
            return back()->withErrors(['error' => 'Failed']);
        }

        $config = Helpers::get_business_settings('paymob');
        try {
            $token = $this->getToken();
            $order = $this->createOrder($token, $payment_data);
            $paymentToken = $this->getPaymentToken($order, $token, $payment_data);
        } catch (\Exception $exception) {
            Toastr::error(translate('country_permission_denied_or_misconfiguration'));
            return back()->withErrors(['error' => 'Failed']);
        }
        return Redirect::away('https://accept.paymob.com/api/acceptance/iframes/' . $config['iframe_id'] . '?payment_token=' . $paymentToken);
    }

    /**
     * @return mixed
     */
    public function getToken(): mixed
    {
        $config = Helpers::get_business_settings('paymob');
        $response = $this->cURL(
            'https://accept.paymobsolutions.com/api/auth/tokens',
            ['api_key' => $config['api_key']]
        );

        return $response->token;
    }

    /**
     * @param $token
     * @param $payment_data
     * @return mixed
     */
    public function createOrder($token, $payment_data): mixed
    {
        $amount = $payment_data['order_amount'];

        $data = [
            "auth_token" => $token,
            "delivery_needed" => "false",
            "amount_cents" => round($amount, 2) * 100,
            "currency" => "EGP",

        ];
        $response = $this->cURL(
            'https://accept.paymob.com/api/ecommerce/orders',
            $data
        );

        return $response;
    }

    /**
     * @param $order
     * @param $token
     * @param $payment_data
     * @return mixed
     */
    public function getPaymentToken($order, $token, $payment_data): mixed
    {
        $amount = $payment_data['order_amount'];
        $user = User::find(session('customer_id'));
        $config = Helpers::get_business_settings('paymob');
        $billingData = [
            "apartment" => "NA",
            "email" => $user['email'],
            "floor" => "NA",
            "first_name" => $user['f_name'],
            "street" => "NA",
            "building" => "NA",
            "phone_number" => $user['phone'],
            "shipping_method" => "PKG",
            "postal_code" => "NA",
            "city" => "NA",
            "country" => "NA",
            "last_name" => $user['l_name'],
            "state" => "NA",
        ];
        $data = [
            "auth_token" => $token,
            "amount_cents" => round($amount, 2) * 100,
            "expiration" => 3600,
            "order_id" => $order->id,
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => $config['integration_id']
        ];

        $response = $this->cURL(
            'https://accept.paymob.com/api/acceptance/payment_keys',
            $data
        );

        return $response->token;
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function callback(Request $request): Redirector|RedirectResponse|Application
    {
        $callback = session('callback');
        //token string generate
        $transaction_reference = 'trx_' . Str::random(25);
        $token_string = 'payment_method=paymob&&transaction_reference=' . $transaction_reference;

        $config = Helpers::get_business_settings('paymob');
        $data = $request->all();
        ksort($data);
        $hmac = $data['hmac'];
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = $config['hmac'];
        $hased = hash_hmac('sha512', $connectedString, $secret);
        if ($hased == $hmac) {
            if ($callback != null) {
                return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
            }
        }

        return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
    }
}
