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

        <div class="g-2">
            <form action="{{route('admin.business-settings.web-app.payment-method-status')}}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                @php($cod=\App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('Cash On Delivery')}}</label>
                                    <label class="switcher">
                                        <input class="switcher_input" type="checkbox" name="cash_on_delivery" {{$cod == null || $cod['status'] == 0? '' : 'checked'}} id="cash_on_delivery_btn">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @php($dp=\App\CentralLogics\Helpers::get_business_settings('digital_payment'))
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('Digital Payment')}}</label>
                                    <label class="switcher">
                                        <input class="switcher_input" type="checkbox" name="digital_payment" {{$dp == null || $dp['status'] == 0? '' : 'checked'}} id="digital_payment_btn">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @php($op=\App\CentralLogics\Helpers::get_business_settings('offline_payment'))
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('Offline Payment')}}</label>
                                    <label class="switcher">
                                        <input class="switcher_input" type="checkbox" name="offline_payment" {{$op == null || $op['status'] == 0? '' : 'checked'}} id="offline_payment_btn">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="btn--container mt-2">
                            <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                        </div>
                    </div>
                </div>

            </form>

        </div>

        <div class="row g-2">
            @if($published_status == 1)
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body d-flex justify-content-around">
                            <h4 style="color: #8c1515; padding-top: 10px">
                                <i class="tio-info-outined"></i>
                                {{ translate('Your current payment settings are disabled, because you have enabled
                                payment gateway addon, To visit your currently active payment gateway settings please follow
                                the link.') }}
                                </h4>
                        <span>
                            <a href="{{!empty($payment_url) ? $payment_url : ''}}" class="btn btn-outline-primary"><i class="tio-settings mr-1"></i>{{translate('settings')}}</a>
                        </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row digital_payment_methods mt-3 g-3" id="payment-gatway-cards">
            @foreach($data_values as $payment)
                <div class="col-md-6" style="margin-bottom: 30px">
                    <div class="card">
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-config-update'):'javascript:'}}" method="POST"
                              id="{{$payment->key_name}}-form" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header d-flex flex-wrap align-content-around">
                                <h5>
                                    <span class="text-uppercase">{{str_replace('_',' ',$payment->key_name)}}</span>
                                </h5>
                                <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                    <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                    <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                    <input type="checkbox" name="status" value="1"
                                           class="toggle-switch-input" {{$payment['is_active']==1?'checked':''}}>
                                    <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                </label>
                            </div>

                            @php($additional_data = $payment['additional_data'] != null ? json_decode($payment['additional_data']) : [])
                            <div class="card-body">
                                <div class="payment--gateway-img">
                                    <img style="height: 80px"
                                         src="{{asset('storage/payment_modules/gateway_image')}}/{{$additional_data != null ? $additional_data->gateway_image : ''}}"
                                         onerror="this.src='{{asset('assets/admin/img/placeholder.png')}}'"
                                         alt="public">
                                </div>

                                <input name="gateway" value="{{$payment->key_name}}" class="d-none">

                                @php($mode=$data_values->where('key_name',$payment->key_name)->first()->live_values['mode'])
                                <div class="form-floating" style="margin-bottom: 10px">
                                    <select class="js-select form-control theme-input-style w-100" name="mode">
                                        <option value="live" {{$mode=='live'?'selected':''}}>Live</option>
                                        <option value="test" {{$mode=='test'?'selected':''}}>Test</option>
                                    </select>
                                </div>

                                @php($skip=['gateway','mode','status'])
                                @foreach($data_values->where('key_name',$payment->key_name)->first()->live_values as $key=>$value)
                                    @if(!in_array($key,$skip))
                                        <div class="form-floating" style="margin-bottom: 10px">
                                            <label for="exampleFormControlInput1"
                                                   class="form-label">{{ucwords(str_replace('_',' ',$key))}}
                                                *</label>
                                            <input type="text" class="form-control"
                                                   name="{{$key}}"
                                                   placeholder="{{ucwords(str_replace('_',' ',$key))}} *"
                                                   value="{{env('APP_MODE')=='demo'?'':$value}}">
                                        </div>
                                    @endif
                                @endforeach

                                <div class="form-floating" style="margin-bottom: 10px">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('payment_gateway_title')}}</label>
                                    <input type="text" class="form-control"
                                           name="gateway_title"
                                           placeholder="{{translate('payment_gateway_title')}}"
                                           value="{{$additional_data != null ? $additional_data->gateway_title : ''}}">
                                </div>

                                <div class="form-floating" style="margin-bottom: 10px">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('choose logo')}}</label>
                                    <input type="file" class="form-control" name="gateway_image" accept=".jpg, .png, .jpeg|image/*">
                                </div>

                                <div class="text-right" style="margin-top: 20px">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                            onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                            class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>


    </div>
@endsection

@push('script_2')
    <script>
        @if($published_status == 1)
            $('#payment-gatway-cards').find('input').each(function(){
                $(this).attr('disabled', true);
            });
            $('#payment-gatway-cards').find('select').each(function(){
                $(this).attr('disabled', true);
            });
            $('#payment-gatway-cards').find('.switcher_input').each(function(){
                $(this).removeAttr('checked', true);
            });
            $('#payment-gatway-cards').find('button').each(function(){
                $(this).attr('disabled', true);
            });
        @endif
    </script>
@endpush
