<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use MercadoPago\Payment;
use MercadoPago\Payer;
use App\CentralLogics\Helpers;

class MercadoPagoController extends Controller
{
    private $data;

    public function __construct()
    {
        $this->data = Helpers::get_business_settings('mercadopago');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function index(Request $request): Factory|View|Application
    {
        $data = $this->data;
        $data['order_amount'] = $request['order_amount'];
        $data['callback'] = $request['callback'];
        $data['customer_id'] = $request['customer_id'];

        return view('payment-view-marcedo-pogo', compact('data'));
    }

    public function make_payment(Request $request): JsonResponse
    {
        $callback = $request['callback'];

        SDK::setAccessToken($this->data['access_token']);
        $payment = new Payment();
        $payment->transaction_amount = (float)$request['transactionAmount'];
        $payment->token = $request['token'];
        $payment->description = $request['description'];
        $payment->installments = (int)$request['installments'];
        $payment->payment_method_id = $request['paymentMethodId'];
        $payment->issuer_id = (int)$request['issuer'];

        $payer = new Payer();
        $payer->email = $request['payer']['email'];
        $payer->identification = array(
            "type" => $request['payer']['identification']['type'],
            "number" => $request['payer']['identification']['number']
        );
        $payment->payer = $payer;

        $payment->save();

        $response = array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id,
            'callback' => null
        );

        if ($payment->error) {
            $response['error'] = $payment->error->message;
        }

        //token string generate
        $transaction_reference = $payment->id;
        $token_string = 'payment_method=mercadopago&&transaction_reference=' . $transaction_reference;

        if ($payment->status == 'approved') {
            //success
            if ($callback != null) {
                $response['callback'] = $callback . '/success' . '?token=' . base64_encode($token_string);
            } else {
                $response['callback'] = route('payment-success', ['token' => base64_encode($token_string)]);
            }


        } else {
            //fail
            if ($callback != null) {
                $response['callback'] = $callback . '/fail' . '?token=' . base64_encode($token_string);
            } else {
                $response['callback'] = route('payment-fail', ['token' => base64_encode($token_string)]);
            }
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function get_test_user(Request $request): void
    {
        // curl -X POST \
        // -H "Content-Type: application/json" \
        // -H 'Authorization: Bearer PROD_ACCESS_TOKEN' \
        // "https://api.mercadopago.com/users/test_user" \
        // -d '{"site_id":"MLA"}'

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.mercadopago.com/users/test_user");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->data['access_token']
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"site_id":"MLA"}');
        $response = curl_exec($curl);
        //dd($response);
    }
}
