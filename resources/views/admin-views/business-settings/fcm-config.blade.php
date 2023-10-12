@extends('layouts.admin.app')

@section('title', translate('FCM Settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/firebase.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('firebase_push_notification_setup')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <!-- Inine Page Menu -->

        <div class="card">
            <div class="card-header card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 gap-2">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-index') }}" class="nav-link pb-2 px-0 pb-sm-3" data-slide="1">
                                <img src="{{asset('/assets/admin/img/notify.png')}}" alt="">
                                <span>{{translate('Push Notification')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-config') }}" class="nav-link pb-2 px-0 pb-sm-3 active" data-slide="2">
                                <img src="{{asset('/assets/admin/img/firebase2.png')}}" alt="">
                                <span>{{translate('Firebase Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="item text-primary d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#firebase-modal">
                            <strong class="mr-2">{{translate('Where to get this information')}}</strong>
                            <div class="blinkings">
                                <i class="tio-info-outined"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
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


        <div class="modal fade" id="firebase-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="max-349 mx-auto mb-20">
                            <div class="text-center">
                                <img src="{{asset('/assets/admin/img/firebase/slide-4.png')}}" alt="" class="mb-20">
                                <h5 class="modal-title">{{translate('Please_Visit_the_Docs_to_Set_FCM_on_Mobile_Apps')}}</h5>
                            </div>
                            <div class="text-center">
                                <p>
                                    {{translate('Please_check_the_documentation_below_for_detailed_instructions_on_setting_up_your_mobile_app_to_receive_Firebase_Cloud_Messaging_(FCM)_notifications.')}}
                                </p>
                                <a href="https://docs.6amtech.com/docs-efood/mobile-apps/mandatory-setup#setup-firebase-for-push-notification" target="_blank" >{{translate('Click Here')}}</a>
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
        $('[data-slide]').on('click', function(){
            let serial = $(this).data('slide')
            $(`.tab--content .item`).removeClass('show')
            $(`.tab--content .item:nth-child(${serial})`).addClass('show')
        })
    </script>

@endpush
