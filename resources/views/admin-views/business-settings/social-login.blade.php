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
