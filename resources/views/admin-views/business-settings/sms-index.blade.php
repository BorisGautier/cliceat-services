@extends('layouts.admin.app')

@section('title', translate('SMS Module Setup'))

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

        <!-- Inline Page Menu -->
        @include('admin-views.business-settings.partials._3rdparty-inline-menu')

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase mb-1">
                            <h5 class="mb-0">{{translate('twilio_SMS')}}</h5>
                            <div class="pl-2">
                                <img src="{{asset('assets/admin/img/twilio.png')}}" alt="public" style="height: 50px">
                            </div>
                        </div>
                        <div>
                            <div class="px-2 d-inline-block rounded badge-soft-info mb-3 fz-12">{{translate('NB : #OTP# will be replace with otp')}}</div>
                        </div>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('twilio_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.sms-module-update',['twilio_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{translate('twilio_sms')}}</label>
                            </div>
                            <div class="d-flex flex-wrap mb-4">
                                <label class="form-check form--check mr-4 mr-md-4">
                                    <input type="radio" name="status" id="twilio_sms_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                    <span for="twilio_sms_active" class="mb-0">{{translate('active')}}</span>
                                </label>
                                <label class="form-check form--check">
                                    <input type="radio" name="status" id="twilio_sms_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                    <span for="twilio_sms_inactive" class="mb-0">{{translate('inactive')}} </span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{translate('sid')}}</label>
                                <input type="text" class="form-control" name="sid"
                                       value="{{env('APP_MODE')!='demo'?$config['sid']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('messaging_service_sid')}}</label>
                                <input type="text" class="form-control" name="messaging_service_sid"
                                       value="{{env('APP_MODE')!='demo'?$config['messaging_service_sid']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('token')}}</label>
                                <input type="text" class="form-control" name="token"
                                       value="{{env('APP_MODE')!='demo'?$config['token']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('from')}}</label>
                                <input type="text" class="form-control" name="from"
                                       value="{{env('APP_MODE')!='demo'?$config['from']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('otp_template')}}</label>
                                <input type="text" class="form-control" name="otp_template"
                                       value="{{env('APP_MODE')!='demo'?$config['otp_template']??"":''}}">
                            </div>

                            <div class="btn--container">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase mb-1">
                            <h5 class="mb-0">{{translate('2factor_sms')}}</h5>
                            <div class="pl-2">
                                <img src="{{asset('assets/admin/img/2factor.png')}}" alt="public" style="height: 50px">
                            </div>
                        </div>
                        <div>
                            <div class="px-2 d-inline-block rounded badge-soft-info mb-1 fz-12">{{\App\CentralLogics\translate("EX of SMS provider's template : your OTP is XXXX here, please check.")}}</div>
                        </div>
                        <div>
                            <div class="px-2 d-inline-block rounded badge-soft-info mb-3 fz-12">{{translate('NB : XXXX will be replace with otp')}}</div>
                        </div>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('2factor_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.sms-module-update',['2factor_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{translate('2factor_SMS')}}</label>
                            </div>
                            <div class="d-flex flex-wrap mb-4">
                                <label class="form-check form--check mr-4 mr-md-4">
                                    <input type="radio" name="status" id="2factor_sms_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                    <span for="2factor_sms_active" class="mb-0">{{translate('active')}}</span>
                                    <br>
                                </label>
                                <label class="form-check form--check">
                                    <input type="radio" name="status" id="2factor_sms_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                    <span for="2factor_sms_inactive" class="mb-0">{{translate('inactive')}} </span>
                                    <br>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{translate('api_key')}}</label>
                                <input type="text" class="form-control" name="api_key"
                                       value="{{env('APP_MODE')!='demo'?$config['api_key']??"":''}}">
                            </div>

                            <div class="btn--container">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase mb-1">
                            <h5 class="mb-0">{{translate('signalwire_SMS')}}</h5>
                            <div class="pl-2">
                                <img src="{{asset('assets/admin/img/signalwire.png')}}" alt="public" style="height: 50px">
                            </div>
                        </div>
                        <div>
                            <div class="px-2 d-inline-block rounded badge-soft-info mb-3 fz-12">{{translate('NB : #OTP# will be replace with otp')}}</div>
                        </div>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('signalwire_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.sms-module-update',['signalwire_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{translate('signalwire_SMS')}}</label>
                            </div>
                            <div class="d-flex flex-wrap mb-4">
                                <label class="form-check form--check mr-4 mr-md-4">
                                    <input type="radio" name="status" id="signalwire_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                    <span class="mb-0" for="signalwire_active">{{translate('active')}}</span>
                                </label>
                                <label class="form-check form--check">
                                    <input type="radio" name="status" id="signalwire_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                    <span class="mb-0" for="signalwire_inactive">{{translate('inactive')}} </span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{translate('project_id')}}</label>
                                <input type="text" class="form-control" name="project_id"
                                       value="{{env('APP_MODE')!='demo'?$config['project_id']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label>{{translate('token')}}</label>
                                <input type="text" class="form-control" name="token"
                                       value="{{env('APP_MODE')!='demo'?$config['token']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label>{{translate('space_url')}}</label>
                                <input type="text" class="form-control" name="space_url"
                                       value="{{env('APP_MODE')!='demo'?$config['space_url']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label>{{translate('from')}}</label>
                                <input type="text" class="form-control" name="from"
                                       value="{{env('APP_MODE')!='demo'?$config['from']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label style="padding-left: 2px">{{translate('otp_template')}}</label>
                                <input type="text" class="form-control" name="otp_template"
                                       value="{{env('APP_MODE')!='demo'?$config['otp_template']??"":''}}">
                            </div>

                            <div class="btn--container">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase mb-1">
                            <h5 class="mb-0">{{translate('nexmo_SMS')}}</h5>
                            <div class="pl-2">
                                <img src="{{asset('assets/admin/img/nexmo.png')}}" alt="public" style="height: 50px">
                            </div>
                        </div>
                        <div>
                            <div class="badge-soft-info d-inline-block px-2 rounded mb-3 fz-12">{{translate('NB : #OTP# will be replace with otp')}}</div>
                        </div>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('nexmo_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.sms-module-update',['nexmo_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group mb-2">
                                <label class="control-label">{{translate('nexmo_sms')}}</label>
                            </div>
                            <div class="d-flex flex-wrap mb-4">
                                <label class="form-check form--check mr-4 mr-md-4">
                                    <input type="radio" name="status" id="nexmo_sms_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                    <span class="mb-0">{{translate('active')}}</span>
                                </label>
                                <label class="form-check form--check">
                                    <input type="radio" name="status" id="nexmo_sms_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                    <span class="mb-0">{{translate('inactive')}} </span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{translate('api_key')}}</label>
                                <input type="text" class="form-control" name="api_key"
                                       value="{{env('APP_MODE')!='demo'?$config['api_key']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label>{{translate('api_secret')}}</label>
                                <input type="text" class="form-control" name="api_secret"
                                       value="{{env('APP_MODE')!='demo'?$config['api_secret']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('from')}}</label>
                                <input type="text" class="form-control" name="from"
                                       value="{{env('APP_MODE')!='demo'?$config['from']??"":''}}">
                            </div>

                            <div class="form-group">
                                <label>{{translate('otp_template')}}</label>
                                <input type="text" class="form-control" name="otp_template"
                                       value="{{env('APP_MODE')!='demo'?$config['otp_template']??"":''}}">
                            </div>

                            <div class="btn--container">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                class="btn btn-primary">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center text-uppercase mb-1">
                            <h5 class="mb-0">{{translate('msg91_SMS')}}</h5>
                            <div class="pl-2">
                                <img src="{{asset('assets/admin/img/msg91.png')}}" alt="public" style="height: 50px">
                            </div>
                        </div>
                        <div>
                            <div class="badge-soft-info d-inline-block px-2 rounded mb-3 fz-12">{{translate('NB : Keep an OTP variable in your SMS providers OTP Template.')}}</div>
                        </div>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('msg91_sms'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.sms-module-update',['msg91_sms']):'javascript:'}}"
                              method="post">
                            @csrf

                            <div class="form-group">
                                <label class="control-label">{{translate('msg91_sms')}}</label>
                            </div>
                            <div class="d-flex flex-wrap mb-4">
                                <label class="form-check form--check mr-4 mr-md-4">
                                    <input type="radio" name="status" id="msg91_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                    <span for="msg91_active" class="mb-0">{{translate('active')}}</span>
                                    <br>
                                </label>
                                <label class="form-check form--check">
                                    <input type="radio" name="status" id="msg91_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                    <span for="msg91_inactive" class="mb-0">{{translate('inactive')}} </span>
                                    <br>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{{translate('template_id')}}</label><br>
                                <input type="text" class="form-control" name="template_id"
                                       value="{{env('APP_MODE')!='demo'?$config['template_id']??"":''}}">
                            </div>
                            <div class="form-group">
                                <label>{{translate('authkey')}}</label><br>
                                <input type="text" class="form-control" name="authkey"
                                       value="{{env('APP_MODE')!='demo'?$config['authkey']??"":''}}">
                            </div>

                            <div class="btn--container">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                class="btn btn-primary mb-2">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
