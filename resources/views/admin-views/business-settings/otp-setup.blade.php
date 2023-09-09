@extends('layouts.admin.app')

@section('title', translate('OTP setup'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('business_setup')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <!-- Inine Page Menu -->
        @include('admin-views.business-settings.partials._business-setup-inline-menu')

            <!-- Business Information -->
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <form action="{{route('admin.business-settings.restaurant.otp-setup-update')}}" method="post">
                            @csrf
                            <div class="row">
                                @php($maximum_otp_hit=\App\Model\BusinessSetting::where('key','maximum_otp_hit')->first()?->value)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize">{{translate('maximum_OTP_submit_attempt')}}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum OTP hit is a measure of how many times a specific one-time password has been generated and used within a time.') }}">
                                            </i>
                                        </label>
                                        <input type="number" value="{{$maximum_otp_hit}}" min="1"
                                               name="maximum_otp_hit" class="form-control" required>
                                    </div>
                                </div>

                                @php($otp_resend_time=\App\Model\BusinessSetting::where('key','otp_resend_time')->first()?->value)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize">{{translate('otp_resend_time')}}
                                            <span class="text-danger">( {{ translate('in second') }} )</span>
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('If the user fails to get the OTP within a certain time, user can request a resend.') }}">
                                            </i>
                                        </label>
                                        <input type="number" value="{{$otp_resend_time}}" min="1"
                                               name="otp_resend_time" class="form-control" required>
                                    </div>
                                </div>

                                @php($temporary_block_time=\App\Model\BusinessSetting::where('key','temporary_block_time')->first()?->value)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize">{{translate('temporary_block_time')}}
                                            <span class="text-danger">( {{ translate('in second') }} )</span>
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('Temporary OTP block time refers to a security measure implemented by systems to restrict access to OTP service for a specified period of time for wrong OTP submission.') }}">
                                            </i>
                                        </label>
                                        <input type="number" value="{{$temporary_block_time}}" min="1"
                                               name="temporary_block_time" class="form-control" required>
                                    </div>
                                </div>

                                @php($maximum_login_hit=\App\Model\BusinessSetting::where('key','maximum_login_hit')->first()?->value)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize">{{translate('maximum_login_attempt')}}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum login hit is a measure of how many times a user can submit password within a time.') }}">
                                            </i>
                                        </label>
                                        <input type="number" value="{{$maximum_login_hit}}" min="1"
                                               name="maximum_login_hit" class="form-control" required>
                                    </div>
                                </div>

                                @php($temporary_login_block_time=\App\Model\BusinessSetting::where('key','temporary_login_block_time')->first()?->value)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize">{{translate('temporary_login_block_time')}}
                                            <span class="text-danger">( {{ translate('in second') }} )</span>
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('Temporary login block time refers to a security measure implemented by systems to restrict access for a specified period of time for wrong Password submission.') }}">
                                            </i>
                                        </label>
                                        <input type="number" value="{{$temporary_login_block_time}}" min="1"
                                               name="temporary_login_block_time" class="form-control" required>
                                    </div>
                                </div>

                            </div>

                            <div class="btn--container mt-4">
                                <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary">{{translate('submit')}}</button>
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
