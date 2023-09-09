<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Admin login
 */
Route::get('/', function () {
    return redirect(\route('admin.dashboard'));
});


/**
 * Pages
 */
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');

/**
 * Auth
 */
Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

/**
 * Payment
 */
Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

/** SSLCommerz */
Route::post('sslcommerz/pay', 'SslCommerzPaymentController@index')->name('pay-ssl');
Route::post('sslcommerz/success', 'SslCommerzPaymentController@success')->name('ssl-success');
Route::post('sslcommerz/failure', 'SslCommerzPaymentController@fail')->name('ssl-failure');
Route::post('sslcommerz/cancel', 'SslCommerzPaymentController@cancel')->name('ssl-cancel');
Route::post('sslcommerz/ipn', 'SslCommerzPaymentController@ipn')->name('ssl-ipn');

/** Paypal */
Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');

/** Stripe */
Route::get('pay-stripe', 'StripePaymentController@payment_process_3d')->name('pay-stripe');
Route::get('pay-stripe/success', 'StripePaymentController@success')->name('pay-stripe.success');
Route::get('pay-stripe/fail', 'StripePaymentController@success')->name('pay-stripe.fail');

/** Razorpay */
Route::get('paywithrazorpay', 'RazorPayController@payWithRazorpay')->name('paywithrazorpay');
Route::post('payment-razor', 'RazorPayController@payment')->name('payment-razor');

/** Internal point pay */
Route::post('internal-point-pay', 'InternalPointPayController@payment')->name('internal-point-pay');

/** SenangPay */
Route::match(['get', 'post'], '/return-senang-pay', 'SenangPayController@return_senang_pay')->name('return-senang-pay');

/** PayStack */
Route::post('/paystack-pay', 'PaystackController@redirectToGateway')->name('paystack-pay');
Route::get('/paystack-callback', 'PaystackController@handleGatewayCallback')->name('paystack-callback');
Route::get('/paystack', function () {
    return view('paystack');
});

/** BKash */
Route::group(['prefix' => 'bkash'], function () {
    // Payment Routes for bKash
    Route::get('make-payment', 'BkashPaymentController@make_tokenize_payment')->name('bkash.make-payment');
    Route::any('callback', 'BkashPaymentController@callback')->name('bkash.callback');
});

/** Paymob */
Route::post('/paymob-credit', 'PaymobController@credit')->name('paymob-credit');
Route::get('/paymob-callback', 'PaymobController@callback')->name('paymob-callback');

/** Mercado Pago */
Route::get('mercadopago/home', 'MercadoPagoController@index')->name('mercadopago.index');
Route::post('mercadopago/make-payment', 'MercadoPagoController@make_payment')->name('mercadopago.make_payment');
Route::get('mercadopago/get-user', 'MercadoPagoController@get_test_user')->name('mercadopago.get-user');

/** FlutterWave */
Route::post('/flutterwave-pay', 'FlutterwaveController@initialize')->name('flutterwave_pay');
Route::get('/rave/callback', 'FlutterwaveController@callback')->name('flutterwave_callback');

/**
 * Currency
 */
Route::get('add-currency', function () {
    $currencies = file_get_contents("installation/currency.json");
    $decoded = json_decode($currencies, true);
    $keep = [];
    foreach ($decoded as $item) {
        array_push($keep, [
            'country' => $item['name'],
            'currency_code' => $item['code'],
            'currency_symbol' => $item['symbol_native'],
            'exchange_rate' => 1,
        ]);
    }
    DB::table('currencies')->insert($keep);
    return response()->json(['ok']);
});

