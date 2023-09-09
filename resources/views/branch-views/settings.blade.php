@extends('layouts.branch.app')

@section('title', translate('Profile Settings'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
{{--                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/product.png')}}" alt="">--}}
                <span class="page-header-title">
                    {{translate('Settings')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-lg-3">
                <!-- Navbar -->
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <!-- Navbar Toggle -->
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                            aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                            data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                <span class="d-flex justify-content-between align-items-center">
                  <span class="h5 mb-0">{{ translate('Nav menu') }}</span>

                  <span class="navbar-toggle-default">
                    <i class="tio-menu-hamburger"></i>
                  </span>

                  <span class="navbar-toggle-toggled">
                    <i class="tio-clear"></i>
                  </span>
                </span>
                    </button>
                    <!-- End Navbar Toggle -->

                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                        <!-- Navbar Nav -->
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav py-3">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:" id="generalSection">
                                    <i class="tio-user-outlined nav-icon"></i> {{ translate('Basic_information') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:" id="passwordSection">
                                    <i class="tio-lock-outlined nav-icon"></i> {{ translate('Password') }}
                                </a>
                            </li>
                        </ul>
                        <!-- End Navbar Nav -->
                    </div>
                </div>
                <!-- End Navbar -->
            </div>

            <div class="col-lg-9">
                <form action="{{env('APP_MODE')!='demo'?route('branch.settings'):'javascript:'}}" method="post" enctype="multipart/form-data" id="branch-settings-form">
                @csrf
                <!-- Card -->
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <!-- Profile Cover -->
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper">
                                <img id="" class="avatar-img w-100 h-100 object-cover"
                                     onerror="this.src='{{asset('assets/admin/img/1920x400/img2.jpg')}}'"
                                     src="{{asset('storage/app/public/branch')}}/{{auth('branch')->user()->cover_image}}" alt="Image">

                            </div>
                        </div>
                        <!-- End Profile Cover -->

                        <!-- Avatar -->
                        <label
                            class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                            for="avatarUploader">
                            <img id="viewer"
                                 onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                 class="avatar-img"
                                 src="{{asset('storage/app/public/branch')}}/{{auth('branch')->user()->image}}"
                                 alt="Image">

                            <input type="file" name="image" class="js-file-attach avatar-uploader-input"
                                   id="customFileEg1"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="avatar-uploader-trigger" for="customFileEg1">
                                <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                            </label>
                        </label>
                        <!-- End Avatar -->
                    </div>
                    <!-- End Card -->

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title h4"><i class="tio-info"></i> {{ translate('Basic information') }}</h2>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">{{ translate('Branch name') }} <i
                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="top"
                                        title="{{translate('Display name')}}"></i></label>

                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="name" id="firstNameLabel"
                                               placeholder="{{ translate('Branch name') }}" aria-label="Branch name"
                                               value="{{auth('branch')->user()->name}}">
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('Email') }}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{auth('branch')->user()->email}}" disabled
                                           placeholder="{{ translate('Enter new email address') }}" aria-label="Enter new email address">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{ translate('phone') }}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="phone" id=""
                                           value="{{auth('branch')->user()->phone?? ''}}"
                                           placeholder="{{ translate('Enter new phone number') }}" aria-label="Enter new phone number" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" onclick="{{env('APP_MODE')!='demo'?"form_alert('branch-settings-form','".translate("Want to update branch info ?")."')":"call_demo()"}}" class="btn btn-primary">{{ translate('Save changes') }}</button>
                            </div>

                            <!-- End Form -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </form>

                <!-- Card -->
                <div id="passwordDiv" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h4 class="card-title"><i class="tio-lock"></i> {{ translate('Change password') }}</h4>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Form -->
                        <form id="changePasswordForm" action="{{env('APP_MODE')!='demo'?route('branch.settings-password'):'javascript:'}}" method="post"
                              enctype="multipart/form-data">
                        @csrf

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="newPassword" class="col-sm-3 col-form-label input-label">{{ translate('New password') }}</label>

                                <div class="col-sm-9 input-group input-group-merge">
                                    <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password"
                                           placeholder="{{translate('Enter new password')}}" required
                                           data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                    <div id="changePassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changePassIcon" class="tio-visible-outlined mr-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label">{{ translate('Confirm password') }}</label>
                                <div class="col-sm-9 input-group input-group-merge">
                                    <input type="password" name="confirm_password" class="js-toggle-password form-control form-control input-field"
                                           id="confirm_password" placeholder="{{translate('Confirm your new password')}}" required
                                           data-hs-toggle-password-options='{
                                                "target": "#changeConPassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changeConPassIcon"
                                                }'>
                                    <div id="changeConPassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changeConPassIcon" class="tio-visible-outlined mr-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="d-flex justify-content-end">
                                <button type="button" onclick="{{env('APP_MODE')!='demo'?"form_alert('changePasswordForm','".translate("Want to update branch password ?")."')":"call_demo()"}}" class="btn btn-primary">{{ translate('Save Changes') }}</button>
                            </div>
                        </form>
                        <!-- End Form -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->

                <!-- Sticky Block End Point -->
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>

    <script>
        $("#generalSection").click(function() {
            $("#passwordSection").removeClass("active");
            $("#generalSection").addClass("active");
            $('html, body').animate({
                scrollTop: $("#generalDiv").offset().top
            }, 2000);
        });

        $("#passwordSection").click(function() {
            $("#generalSection").removeClass("active");
            $("#passwordSection").addClass("active");
            $('html, body').animate({
                scrollTop: $("#passwordDiv").offset().top
            }, 2000);
        });
    </script>
@endpush
