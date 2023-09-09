<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

use DGvai\SSLCommerz\SSLCommerz;

class SslCommerzPaymentController extends Controller
{
    public function __construct()
    {
        $mode = env('APP_MODE');
        $ssl = Helpers::get_business_settings('ssl_commerz_payment');
        if ($ssl) {
            if ($mode == 'live') {
                $url = "https://securepay.sslcommerz.com";
                $host = false;
            } else {
                $url = "https://sandbox.sslcommerz.com";
                $host = true;
            }

            //configuration initialization
            $config = array(
                'product_profile' => 'example',
                //'localhost' => false,
                'path' => [
                    'domain' => [
                        'sandbox' => 'https://sandbox.sslcommerz.com',
                        'live' => 'https://securepay.sslcommerz.com',
                    ],
                    'endpoint' => [
                        'make_payment' => $url . "/gwprocess/v4/api.php",
                        'transaction_status' => $url . "/validator/api/merchantTransIDvalidationAPI.php",
                        'order_validate' => $url . "/validator/api/validationserverAPI.php",
                        'refund_payment' => $url . "/validator/api/merchantTransIDvalidationAPI.php",
                        'refund_status' => $url . "/validator/api/merchantTransIDvalidationAPI.php",
                    ]

                ],
                'store' => [
                    'id' => $ssl['store_id'],
                    'password' => $ssl['store_password'],
                ],
                'route' => [
                    'success' => 'ssl-success',
                    'failure' => 'ssl-failure',
                    'cancel' => 'ssl-cancel',
                    'ipn' => 'ssl-ipn',
                ]
            );
            Config::set('sslcommerz', $config);
        }
    }

    /**
     * @param Request $request
     * @return false|RedirectResponse|string|void
     */
    public function index(Request $request)
    {
        //initialization
        $callback = $request['callback'];
        $customer = User::find($request['customer_id']);
        $order_amount = $request['order_amount'];
        $transaction_reference = Str::random(6) . '-' . rand(1, 1000);

        try {
            $sslc = new SSLCommerz();
            $sslc->amount($order_amount)
                ->trxid($transaction_reference)
                ->product('Product')
                ->customer($customer['f_name'] . ' ' . $customer['l_name'], $customer['mail'] ?? 'example@example.com')
                ->setUrl([route('ssl-success', ['callback' => $callback]), route('ssl-failure', ['callback' => $callback]), route('ssl-cancel', ['callback' => $callback]), route('ssl-ipn')])
                ->setCurrency(Helpers::currency_code());

            $payment_options = $sslc->make_payment();
            if (!SSLCommerz::query_transaction($transaction_reference)->status) {
                Toastr::error('Your currency is not supported by SSLCOMMERZ.');
                return back()->withErrors(['error' => 'Failed']);
            }

            return $payment_options;

        } catch (\Exception $exception) {
            Toastr::error('Misconfiguration or data is missing!');
            return back()->withErrors(['error' => 'Failed']);
        }


    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function success(Request $request): Redirector|RedirectResponse|Application
    {
        //token string generate
        $callback = $request['callback'];
        $token_string = 'payment_method=ssl_commerz_payment&&transaction_reference=' . $request['tran_id'];

        //success
        if ($callback != null) {
            return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
        } else {
            return \redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
        }
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function fail(Request $request): Redirector|RedirectResponse|Application
    {
        //token string generate
        $callback = $request['callback'];
        $token_string = 'payment_method=ssl_commerz_payment&&transaction_reference=' . $request['tran_id'];

        //fail
        if ($callback != null) {
            return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
        } else {
            return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
        }
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function cancel(Request $request): Redirector|RedirectResponse|Application
    {
        //token string generate
        $callback = $request['callback'];
        $token_string = 'payment_method=ssl_commerz_payment&&transaction_reference=' . $request['tran_id'];

        //cancel
        if ($callback != null) {
            return redirect($callback . '/cancel' . '?token=' . base64_encode($token_string));
        } else {
            return \redirect()->route('payment-cancel', ['token' => base64_encode($token_string)]);
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function ipn(Request $request)
    {
        //
    }
}
