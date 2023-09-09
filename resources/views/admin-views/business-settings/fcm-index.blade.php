@extends('layouts.admin.app')

@section('title', translate('FCM Settings'))

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
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.third-party.update-fcm'):'javascript:'}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            @php($key=\App\Model\BusinessSetting::where('key','push_notification_key')->first()->value)
                            <div class="form-group">
                                <label class="input-label">{{translate('server key')}}</label>
                                <textarea name="push_notification_key" class="form-control"
                                          required>{{env('APP_MODE')!='demo'?$key:''}}</textarea>
                            </div>

                            <div class="row" style="display: none">
                                @php($project_id=\App\Model\BusinessSetting::where('key','fcm_project_id')->first()->value)
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('FCM Project ID')}}</label>
                                        <input type="text" value="{{$project_id}}"
                                               name="fcm_project_id" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{translate('Push Messages')}}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                @php($opm=\App\Model\BusinessSetting::where('key','order_pending_message')->first()->value)
                                @php($data=json_decode($opm,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="pending_status">
                                                <input type="checkbox" name="pending_status" class="switcher_input"
                                                    value="1" id="pending_status" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('order_Pending_Message')}}</span>
                                        </div>
                                        <textarea name="pending_message" class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($ocm=\App\Model\BusinessSetting::where('key','order_confirmation_msg')->first()->value)
                                @php($data=json_decode($ocm,true))
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="confirm_status">
                                                <input type="checkbox" name="confirm_status" class="switcher_input"
                                                    value="1" id="confirm_status" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('order confirmation message')}}</span>
                                        </div>

                                        <textarea name="confirm_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($oprm=\App\Model\BusinessSetting::where('key','order_processing_message')->first()->value)
                                @php($data=json_decode($oprm,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="processing_status">
                                                <input type="checkbox" name="processing_status" class="switcher_input"
                                                    value="1" id="processing_status" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('order processing message')}}</span>
                                        </div>

                                        <textarea name="processing_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($ofdm=\App\Model\BusinessSetting::where('key','out_for_delivery_message')->first()->value)
                                @php($data=json_decode($ofdm,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="out_for_delivery">
                                                <input type="checkbox" name="out_for_delivery_status" class="switcher_input"
                                                    value="1" id="out_for_delivery" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('order out for delivery message')}}</span>
                                        </div>

                                        <textarea name="out_for_delivery_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($odm=\App\Model\BusinessSetting::where('key','order_delivered_message')->first()->value)
                                @php($data=json_decode($odm,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="delivered_status">
                                                <input type="checkbox" name="delivered_status" class="switcher_input"
                                                    value="1" id="delivered_status" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('order delivered message')}}</span>
                                        </div>

                                        <textarea name="delivered_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($dba=\App\Model\BusinessSetting::where('key','delivery_boy_assign_message')->first()->value)
                                @php($data=json_decode($dba,true))
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="delivery_boy_assign">
                                                <input type="checkbox" name="delivery_boy_assign_status" class="switcher_input"
                                                    value="1" id="delivery_boy_assign" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('deliveryman assign message')}}</span>
                                        </div>

                                        <textarea name="delivery_boy_assign_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($data= \App\CentralLogics\Helpers::get_business_settings('customer_notify_message'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="customer_notify">
                                                <input type="checkbox" name="customer_notify_status" class="switcher_input"
                                                    value="1" id="customer_notify" {{isset($data) && $data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('Customer notify message for deliveryman')}}</span>
                                        </div>

                                        <textarea name="customer_notify_message"
                                                  class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>

                                @php($data= \App\CentralLogics\Helpers::get_business_settings('customer_notify_message_for_time_change'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="customer_notify_for_time_change">
                                                <input type="checkbox" name="customer_notify_status_for_time_change" class="switcher_input"
                                                    value="1" id="customer_notify_for_time_change" {{isset($data) && $data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('Customer notify message for food preparation time change')}}</span>
                                        </div>

                                        <textarea name="customer_notify_message_for_time_change"
                                                  class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>

                                @php($dbs=\App\Model\BusinessSetting::where('key','delivery_boy_start_message')->first()->value)
                                @php($data=json_decode($dbs,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="delivery_boy_start_status">
                                                <input type="checkbox" name="delivery_boy_start_status" class="switcher_input"
                                                    value="1" id="delivery_boy_start_status" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('deliveryman start message')}}</span>
                                        </div>

                                        <textarea name="delivery_boy_start_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($dbc=\App\Model\BusinessSetting::where('key','delivery_boy_delivered_message')->first()->value)
                                @php($data=json_decode($dbc,true))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="delivery_boy_delivered">
                                                <input type="checkbox" name="delivery_boy_delivered_status" class="switcher_input"
                                                    value="1" id="delivery_boy_delivered" {{$data['status']==1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('deliveryman delivered message')}}</span>
                                        </div>

                                        <textarea name="delivery_boy_delivered_message"
                                                  class="form-control">{{$data['message']}}</textarea>
                                    </div>
                                </div>

                                @php($data=\App\CentralLogics\Helpers::get_business_settings('returned_message'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="returned_status">
                                                <input type="checkbox" name="returned_status" class="switcher_input"
                                                    value="1" id="returned_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('Order_returned_message')}}</span>
                                        </div>

                                        <textarea name="returned_message"
                                                  class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>

                                @php($data=\App\CentralLogics\Helpers::get_business_settings('failed_message'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="failed_status">
                                                <input type="checkbox" name="failed_status" class="switcher_input"
                                                    value="1" id="failed_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('Order_failed_message')}}</span>
                                        </div>

                                        <textarea name="failed_message"
                                                  class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>

                                @php($data=\App\CentralLogics\Helpers::get_business_settings('canceled_message'))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <label class="switcher" for="canceled_status">
                                                <input type="checkbox" name="canceled_status" class="switcher_input"
                                                    value="1" id="canceled_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                            <span class="text-dark">{{translate('Order_canceled_message')}}</span>
                                        </div>

                                        <textarea name="canceled_message"
                                                  class="form-control">{{$data['message']??''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
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
