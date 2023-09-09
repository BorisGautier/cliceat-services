<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class SenangPayController extends Controller
{
    /**
     * @param Request $request
     * @return Redirector|RedirectResponse|Application
     */
    public function return_senang_pay(Request $request): Redirector|RedirectResponse|Application
    {
        $callback = $request['callback'];

        //token string generate
        $transaction_reference = $request['transaction_id'];
        $token_string = 'payment_method=senang_pay&&transaction_reference=' . $transaction_reference;

        if ($request['status_id'] == 1) {
            //success
            if ($callback != null) {
                return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
            }
        }

        //fail
        if ($callback != null) {
            return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
        } else {
            return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
        }
    }
}
