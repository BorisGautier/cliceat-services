<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ApiErrorException
     */
    public function payment_process_3d(Request $request): JsonResponse
    {
        $tran = Str::random(6) . '-' . rand(1, 1000);
        $order_amount = $request['order_amount'];
        $callback = $request['callback'];
        $config = Helpers::get_business_settings('stripe');
        Stripe::setApiKey($config['api_key']);
        header('Content-Type: application/json');
        $currency_code = Helpers::get_business_settings('currency') ?? 'usd';

        $currencies_not_supported_cents = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency_code,
                    'unit_amount' => in_array($currency_code, $currencies_not_supported_cents) ? (int)$order_amount : ($order_amount * 100),
                    'product_data' => [
                        'name' => BusinessSetting::where(['key' => 'restaurant_name'])->first()->value,
                        'images' => [asset('storage/app/public/restaurant') . '/' . BusinessSetting::where(['key' => 'logo'])->first()->value],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('pay-stripe.success', ['callback' => $callback, 'transaction_reference' => $tran]),
            'cancel_url' => url()->previous(),
        ]);

        return response()->json(['id' => $checkout_session->id]);
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function success(Request $request): Redirector|RedirectResponse|Application
    {
        $callback = $request['callback'];

        //token string generate
        $transaction_reference = $request['transaction_reference'];
        $token_string = 'payment_method=stripe&&transaction_reference=' . $transaction_reference;

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
        $callback = $request['callback'];

        //token string generate
        $transaction_reference = $request['transaction_reference'];
        $token_string = 'payment_method=stripe&&transaction_reference=' . $transaction_reference;

        //fail
        if ($callback != null) {
            return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
        } else {
            return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
        }
    }
}

