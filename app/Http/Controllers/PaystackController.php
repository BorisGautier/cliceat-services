<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Paystack;

class PaystackController extends Controller
{
    public function __construct()
    {
        //configuration initialization
        $paystack = Helpers::get_business_settings('paystack');
        if ($paystack) {
            $config = array(
                'publicKey' => env('PAYSTACK_PUBLIC_KEY', $paystack['publicKey']),
                'secretKey' => env('PAYSTACK_SECRET_KEY', $paystack['secretKey']),
                'paymentUrl' => env('PAYSTACK_PAYMENT_URL', $paystack['paymentUrl']),
                'merchantEmail' => env('MERCHANT_EMAIL', $paystack['merchantEmail']),
            );
            Config::set('paystack', $config);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectToGateway(Request $request): RedirectResponse
    {
        \session()->put('callback', $request['callback']);
        try {
            return Paystack::getAuthorizationUrl()->redirectNow();

        } catch (\Exception $e) {
            Toastr::error('Your currency is not supported by Paystack.');
            return Redirect::back()->withErrors(['error' => 'Failed']);
        }
    }

    /**
     * @return Redirector|RedirectResponse|Application
     */
    public function handleGatewayCallback(): Redirector|RedirectResponse|Application
    {
        $callback = session('callback');

        $paymentDetails = Paystack::getPaymentData();

        //token string generate
        $transaction_reference = $paymentDetails['data']['reference'];
        $token_string = 'payment_method=paystack&&transaction_reference=' . $transaction_reference;

        if ($paymentDetails['status'] == true) {
            //success
            if ($callback != null) {
                return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
            }

        } else {
            //fail
            if ($callback != null) {
                return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
            }
        }
    }

    /**
     * @return mixed
     */
    public static function generate_transaction_Referance(): mixed
    {
        return Paystack::genTranxRef();
    }
}
