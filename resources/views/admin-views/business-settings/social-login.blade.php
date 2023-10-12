@extends('layouts.admin.app')

@section('title', translate('Social Login'))

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

        <!-- Inline Menu -->
        @include('admin-views.business-settings.partials._3rdparty-inline-menu')

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card __social-media-login __shadow">
                    <div class="card-body">
                        <div class="__social-media-login-top">
                            <div class="__social-media-login-icon">
                                <img src="{{asset('assets/admin/img/icons/google.png')}}" alt="">
                            </div>
                            <div class="text-center sub-txt text-capitalize">{{translate('google_login')}}</div>
                            <div class="custom--switch switch--right">
                                @php($google = \App\CentralLogics\Helpers::get_business_settings('google_social_login'))
                                <input onclick="loginStatusChange(this)" type="checkbox" id="google_social_login" name="google" switch="primary" class="toggle-switch-input"
                                       {{$google == 1 ? 'checked' : ''}}>
                                <label for="google_social_login" data-on-label="on" data-off-label="off"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card __social-media-login __shadow">
                    <div class="card-body">
                        <div class="__social-media-login-top">
                            <div class="__social-media-login-icon">
                                <img src="{{asset('assets/admin/img/icons/facebook.png')}}" alt="">
                            </div>
                            <div class="text-center sub-txt text-capitalize">{{translate('facebook_login')}}</div>
                            <div class="custom--switch switch--right">
                                @php($facebook = \App\CentralLogics\Helpers::get_business_settings('facebook_social_login'))
                                <input onclick="loginStatusChange(this)" type="checkbox" id="facebook" name="facebook_social_login" switch="primary" class="toggle-switch-input"
                                {{$facebook == 1 ? 'checked' : ''}}>
                                <label for="facebook" data-on-label="on" data-off-label="off"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if (isset($appleLoginService))
                    <div class="col-md-6">

                        <div class="card">
                            <form action="{{ route('admin.business-settings.web-app.third-party.update-apple-login') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header card-header-shadow">
                                    <div class="__social-media-login-top flex-grow-1">
                                        <h5 class="card-title align-items-center ">
                                            <img src="{{asset('/assets/admin/img/apple.png')}}" class="mr-1 w--20" alt="">
                                            {{translate('Apple Login')}}
                                        </h5>
                                        <div class="custom--switch switch--right">
                                            <input type="checkbox" id="apple" name="status" switch="primary" class="toggle-switch-input"
                                                {{$appleLoginService['status'] == 1 ? 'checked' : ''}}>
                                            <label for="apple" data-on-label="on" data-off-label="off"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body text-left">
                                    <div class="d-flex justify-content-end">
                                        <div class="text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#{{$appleLoginService['login_medium']}}-modal">
                                            <strong class="mr-2 text--underline">{{translate('Credential Setup')}}</strong>
                                            <div class="blinkings">
                                                <i class="tio-info-outined"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{translate('client_id')}}</label>
                                        <input type="text" class="form-control" name="client_id" value="{{ $appleLoginService['client_id'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{translate('team_id')}}</label>
                                        <input type="text" class="form-control" name="team_id" value="{{ $appleLoginService['team_id'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{translate('key_id')}}</label>
                                        <input type="text" class="form-control" name="key_id" value="{{ $appleLoginService['key_id'] }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{translate('service_file')}} {{ $appleLoginService['service_file']?translate('(Already Exists)'):'' }}</label>
                                        <input type="file" accept=".p8" class="form-control" name="service_file"
                                               value="{{ 'storage/apple-login/'.$appleLoginService['service_file'] }}">
                                    </div>
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" class="btn btn-secondary mb-2">{{translate('Reset')}}</button>
                                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn-primary mb-2">{{translate('save')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            @endif
        </div>
    </div>


    <!-- Apple -->
    <div class="modal fade" id="apple-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0"><b></b>
                    <div class="text-center mb-20">
                        <img src="{{asset('/assets/admin/img/modal/apple.png')}}" alt="" class="mb-20">
                        <h5 class="modal-title">{{translate('apple_api_set_instruction')}}</h5>
                    </div>
                    <ol>
                        <li>{{translate('Go to Apple Developer page')}} (<a href="https://developer.apple.com/account/resources/identifiers/list" target="_blank">{{translate('click_here')}}</a>)</li>
                        <li>{{translate('Here in top left corner you can see the')}} <b>{{ translate('Team ID') }}</b> {{ translate('[Apple_Deveveloper_Account_Name - Team_ID]')}}</li>
                        <li>{{translate('Click Plus icon -> select App IDs -> click on Continue')}}</li>
                        <li>{{translate('Put a description and also identifier (identifier that used for app) and this is the')}} <b>{{ translate('Client ID') }}</b> </li>
                        <li>{{translate('Click Continue and Download the file in device named AuthKey_ID.p8 (Store it safely and it is used for push notification)')}} </li>
                        <li>{{translate('Again click Plus icon -> select Service IDs -> click on Continue')}} </li>
                        <li>{{translate('Push a description and also identifier and Continue')}} </li>
                        <li>{{translate('Download the file in device named')}} <b>{{ translate('AuthKey_KeyID.p8') }}</b> {{translate('[This is the Service Key ID file and also after AuthKey_ that is the Key ID]')}}</li>
                    </ol>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-primary w-100 mw-300px" data-dismiss="modal">{{translate('Got It')}}</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script_2')
    <script>
        function loginStatusChange(t) {
            console.log(t.id)
            let url = "{{route('admin.business-settings.web-app.third-party.social-login-status')}}";
            let checked = $(t).prop("checked");
            let status = checked === true ? 1 : 0;
            let btn_name = t.id;

            Swal.fire({
                title: 'Are you sure?',
                text: 'Want to change status',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: 'default',
                cancelButtonText: '{{translate("No")}}',
                confirmButtonText: '{{translate("Yes")}}',
                reverseButtons: true
            }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: url,
                            data: {
                                status: status,
                                btn_name: btn_name,
                            },
                            success: function (data, status) {
                                toastr.success("{{translate('Status changed successfully')}}");
                            },
                            error: function (data) {
                                toastr.error("{{translate('Status changed failed')}}");
                            }
                        });
                    }
                    else if (result.dismiss) {
                        if (status == 1) {
                            $('#' + t.id).prop('checked', false)

                        } else if (status == 0) {
                            $('#'+ t.id).prop('checked', true)
                        }
                        toastr.info("{{translate("Status hasn't changed")}}");
                    }
                }
            )
        }
    </script>
@endpush
