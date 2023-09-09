<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class FlutterwaveController extends Controller
{
    public function __construct()
    {
        //configuration initialization
        $flutterwave = Helpers::get_business_settings('flutterwave');
        if ($flutterwave) {
            $config = array(
                'publicKey' => env('FLW_PUBLIC_KEY', $flutterwave['public_key']), // values : (local | production)
                'secretKey' => env('FLW_SECRET_KEY', $flutterwave['secret_key']),
                'secretHash' => env('FLW_SECRET_HASH', $flutterwave['hash']),
            );
            Config::set('flutterwave', $config);
        }

    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function initialize(Request $request): Redirector|RedirectResponse|Application
    {
        $callback = $request['callback'];
        $user_data = User::find($request['customer_id']);

        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $request['order_amount'],
            'email' => $user_data['email'],
            'tx_ref' => $reference,
            'currency' => Helpers::currency_code(),
            'redirect_url' => route('flutterwave_callback', ['callback'=>$callback]),
            'customer' => [
                'email' => $user_data['email'],
                "phone_number" => $user_data['phone'],
                "name" => $user_data['f_name'] . ' ' . $user_data['l_name'],
            ],

            "customizations" => [
                "title" => BusinessSetting::where(['key'=>'business_name'])->first()->value??'EFood',
                "description" => '',
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            //token string generate
            $transaction_reference = $reference;
            $token_string = 'payment_method=flutterwave&&transaction_reference=' . $transaction_reference;

            //fail
            if ($callback != null) {
                return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
            }

        }

        return redirect($payment['data']['link']);
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function callback(Request $request): Redirector|RedirectResponse|Application
    {
        $callback =$request['callback'];
        $transaction_reference = $request['transaction_reference'];
        $status = $request['status'];

        //if payment is successful
        if ($status == 'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            //token string generate
            $token_string = 'payment_method=flutterwave&&transaction_reference=' . $transaction_reference;

            //success
            if ($callback != null) {
                return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
            }
        }
        // elseif ($status ==  'cancelled'){
        //     //Put desired action/code after transaction has been cancelled here
        // }
        else{
            //token string generate
            $token_string = 'payment_method=flutterwave&&transaction_reference=' . $transaction_reference;

            //fail
            if ($callback != null) {
                return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
            }
        }
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (including parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here

    }
}
