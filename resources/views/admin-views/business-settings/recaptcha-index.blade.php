@extends('layouts.admin.app')

@section('title', translate('reCaptcha Setup'))

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

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="mb-3">{{translate('Google Recapcha Information')}}</h3>
                    <a class="btn-sm btn btn-outline-primary p-2 cursor-pointer" href="https://www.google.com/recaptcha/admin/create">
                        <i class="tio-info-outined"></i>
                        {{translate('Credentials_SetUp')}}
                    </a>
                </div>
                <div class="mt-4">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.third-party.recaptcha_update',['recaptcha']):'javascript:'}}"
                        method="post">
                        @csrf
                        <div class="d-flex flex-wrap mb-4">
                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" name="status" id="captcha_active" value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                <span class="mb-0">{{translate('active')}}</span>
                            </label>
                            <label class="form-check form--check">
                                <input class="form-check-input" type="radio" name="status" id="captcha_inactive" value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                <span class="mb-0">{{translate('inactive')}} </span>
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="text-capitalize">{{translate('Site Key')}}</label><br>
                                    <input type="text" class="form-control" name="site_key" value="{{env('APP_MODE')!='demo'?$config['site_key']??"":''}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="text-capitalize">{{translate('Secret Key')}}</label><br>
                                    <input type="text" class="form-control" name="secret_key" value="{{env('APP_MODE')!='demo'?$config['secret_key']??"":''}}">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <h4 class="mb-3" id="staticBackdropLabel">{{translate('Instructions')}}</h4>
                            <ol class="d-flex flex-column __gap-5px __instructions">
                                <li>{{translate('Go to the Credentials page')}}
                                    ({{translate('Click')}} <a
                                        href="https://www.google.com/recaptcha/admin/create"
                                        target="_blank">{{translate('here')}}</a>)
                                </li>
                                <li>{{translate('Add a ')}}
                                    <b>{{translate('label')}}</b> {{translate('(Ex: Test Label)')}}
                                </li>
                                <li>
                                    {{translate('Select reCAPTCHA v2 as ')}}
                                    <b>{{translate('reCAPTCHA Type')}}</b>
                                    ({{\App\CentralLogics\translate("Sub type: I'm not a robot Checkbox")}}
                                    )
                                </li>
                                <li>
                                    {{translate('Add')}}
                                    <b>{{translate('domain')}}</b>
                                    {{translate('(For ex: demo.6amtech.com)')}}
                                </li>
                                <li>
                                    {{translate('Check in ')}}
                                    <b>{{translate('Accept the reCAPTCHA Terms of Service')}}</b>
                                </li>
                                <li>
                                    {{translate('Press')}}
                                    <b>{{translate('Submit')}}</b>
                                </li>
                                <li>{{translate('Copy')}} <b>Site
                                        Key</b> {{translate('and')}} <b>Secret
                                        Key</b>, {{translate('paste in the input filed below and')}}
                                    <b>Save</b>.
                                </li>
                            </ol>
                        </div>

                        <div class="btn--container">
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn-primary">{{translate('save')}}</button>
                        </div>
                    </form>
                    {{-- Modal --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
