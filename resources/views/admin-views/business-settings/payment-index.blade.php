@extends('layouts.admin.app')

@section('title', translate('Payment Setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/third-party.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('third_party')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <!-- Inine Page Menu -->
        @include('admin-views.business-settings.partials._3rdparty-inline-menu')

        <div class="row g-2">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-4">{{translate('Payment_Method')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['cash_on_delivery'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-4">
                                    <label class="control-label">{{translate('cash_on_delivery')}}</label>
                                </div>
                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check form--check mr-4">
                                        <input type="radio" name="status" id="cash_active" value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="mb-0">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check form--check mr-4">
                                        <input type="radio" name="status" id="cash_inactive" value="0" {{$config['status']==0?'checked':''}}>
                                        <span class="mb-0">{{translate('inactive')}}</span>
                                    </label>
                                </div>
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-4">{{translate('payment_method')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('digital_payment'))
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['digital_payment'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-4">
                                    <label class="control-label">{{translate('digital_payment')}}</label>
                                </div>
                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check form--check mr-4">
                                        <input type="radio" name="status" id="digital_active" value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="mb-0">{{translate('active')}}</span>
                                        <br>
                                    </label>
                                    <label class="form-check form--check">
                                        <input type="radio" name="status" id="digital_inactive" value="0" {{$config['status']==0?'checked':''}}>
                                        <span class="mb-0">{{translate('inactive')}}</span>
                                        <br>
                                    </label>
                                </div>
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                    <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['ssl_commerz_payment']):'javascript:'}}" method="post">
                        @csrf
                        <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('sslcommerz')}}</h5>
                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="185" class="avatar-img" src="{{asset('assets/admin/img/icons/ssl.png')}}" alt="">
                        </center>

                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('store ID')}} </label><br>
                                    <input type="text" class="form-control" name="store_id"
                                           value="{{env('APP_MODE')!='demo'?$config['store_id']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('store Password')}}</label><br>
                                    <input type="text" class="form-control" name="store_password"
                                           value="{{env('APP_MODE')!='demo'?$config['store_password']:''}}">
                                </div>
                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary mb-2">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['razor_pay']):'javascript:'}}"
                        method="post">
                        @csrf

                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('razorpay')}}</h5>
                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/razorpay.png')}}" alt="">
                        </center>

                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('razorkey')}}</label>
                                    <input type="text" class="form-control" name="razor_key"
                                           value="{{env('APP_MODE')!='demo'?$config['razor_key']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('razorsecret')}}</label>
                                    <input type="text" class="form-control" name="razor_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['razor_secret']:''}}">
                                </div>
                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paypal']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('paypal')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('assets/admin/img/icons/paypal.png')}}" alt="">
                        </center>

                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('paypal')}} {{translate('client')}} {{translate('id')}}</label>
                                    <input type="text" class="form-control" name="paypal_client_id"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_client_id']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('paypal')}} {{translate('secret')}}</label>
                                    <input type="text" class="form-control" name="paypal_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_secret']:''}}">
                                </div>

                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['stripe']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('stripe')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/stripe.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('published')}} {{translate('key')}}</label>
                                    <input type="text" class="form-control" name="published_key"
                                           value="{{env('APP_MODE')!='demo'?$config['published_key']:''}}">
                                </div>

                                <div class="form-group">
                                    <label>{{translate('api')}} {{translate('key')}}</label>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}">
                                </div>

                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paystack']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('paystack')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/paystack.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label class="mb-0">{{translate('publicKey')}}</label>
                                    <input type="text" class="form-control" name="publicKey"
                                           value="{{env('APP_MODE')!='demo'?$config['publicKey']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('secretKey')}} </label>
                                    <input type="text" class="form-control" name="secretKey"
                                           value="{{env('APP_MODE')!='demo'?$config['secretKey']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('paymentUrl')}} </label>
                                    <input type="text" class="form-control" name="paymentUrl"
                                           value="{{env('APP_MODE')!='demo'?$config['paymentUrl']:''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('merchantEmail')}} </label>
                                    <input type="text" class="form-control" name="merchantEmail"
                                           value="{{env('APP_MODE')!='demo'?$config['merchantEmail']:''}}">
                                </div>

                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['senang_pay']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('senang_Pay')}}</h5>
                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/senangpay.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('secret_Key')}}</label>
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}">
                                </div>

                                <div class="form-group">
                                    <label>{{translate('merchant_Id')}}</label>
                                    <input type="text" class="form-control" name="merchant_id"
                                           value="{{env('APP_MODE')!='demo'?$config['merchant_id']:''}}">
                                </div>

                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary mb-2">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('internal_point'))
                    <form action="{{route('admin.business-settings.web-app.payment-method-update',['internal_point'])}}"
                          method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('customer_wallet')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/wallet_payment.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('bkash'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['bkash']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('bkash')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/bkash.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('bkash')}} {{translate('api')}} {{translate('key')}}</label>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('bkash')}} {{translate('api')}} {{translate('secret')}}</label>
                                    <input type="text" class="form-control" name="api_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['api_secret']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('username')}} </label>
                                    <input type="text" class="form-control" name="username"
                                           value="{{env('APP_MODE')!='demo'?$config['username']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('password')}} </label>
                                    <input type="text" class="form-control" name="password"
                                           value="{{env('APP_MODE')!='demo'?$config['password']??'':''}}">
                                </div>

                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paymob']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('paymob')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('/assets/admin/img/paymob.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('api_key')}}</label>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('iframe_id')}}</label>
                                    <input type="text" class="form-control" name="iframe_id"
                                           value="{{env('APP_MODE')!='demo'?$config['iframe_id']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('integration_id')}}</label>
                                    <input type="text" class="form-control" name="integration_id"
                                           value="{{env('APP_MODE')!='demo'?$config['integration_id']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('hmac')}}</label>
                                    <input type="text" class="form-control" name="hmac"
                                           value="{{env('APP_MODE')!='demo'?$config['hmac']??'':''}}">
                                </div>
                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['flutterwave']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('flutterwave')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('assets/admin/img/fluterwave.png')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('public_key')}}</label>
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('secret_key')}}</label>
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('hash')}}</label>
                                    <input type="text" class="form-control" name="hash"
                                           value="{{env('APP_MODE')!='demo'?$config['hash']??'':''}}">
                                </div>
                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['mercadopago']):'javascript:'}}"
                        method="post">
                        @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-uppercase mb-4">{{translate('mercadopago')}}</h5>

                            <label class="switcher">
                                <input class="switcher_input" name="status" type="checkbox" {{$config['status'] == 1? 'checked' : ''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                        <center class="mb-4">
                            <img width="140" class="avatar-img" src="{{asset('assets/admin/img/MercadoPago_(Horizontal).svg')}}" alt="">
                        </center>
                            @if(isset($config))
                                <div class="form-group">
                                    <label>{{translate('public_key')}}</label>
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label>{{translate('access_token')}}</label>
                                    <input type="text" class="form-control" name="access_token"
                                           value="{{env('APP_MODE')!='demo'?$config['access_token']??'':''}}">
                                </div>
                                <div class="btn--container">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="btn--container">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script_2')

@endpush
