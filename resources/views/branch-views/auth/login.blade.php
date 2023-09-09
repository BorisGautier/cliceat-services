<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{translate('Branch')}} | {{translate('Login')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">

</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="auth-wrapper">
        <div class="auth-wrapper-left">
            <div class="auth-left-cont">
                @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                <img width="310" src="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}"
                     onerror="this.src='{{asset('assets/admin/img/logo.png')}}'">
                <h2 class="title">{{translate('your')}} <span class="d-block text-capitalize">{{translate('all_fresh_food')}}</span> <strong class="text--039D55 c1 text-capitalize">{{translate('in_one_place')}}....</strong></h2>
            </div>
        </div>

        <!-- Content -->
        <div class="auth-wrapper-right">

            <!-- Card -->
            <div class="auth-wrapper-form">
                <!-- Form -->
                <form class="" id="form-id" action="{{route('branch.auth.login')}}" method="post">
                    @csrf

                    <div class="auth-header">
                        <div class="mb-5">
                            <h2 class="title">{{translate('sign_in')}}</h2>
                            <div class="text-capitalize">{{translate('welcome_back')}}</div>
                            <p class="mb-0 text-capitalize">{{translate('want_to_login_your_admin')}}?
                                <a href="{{route('admin.auth.login')}}">
                                    {{translate('admin_login')}}
                                </a>
                            </p>
                            <span class="badge mt-2">( {{translate('branch_sign_in')}} )</span>
                        </div>
                    </div>

                    <!-- Form Group -->
                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="signinSrEmail">{{translate('your')}} {{translate('email')}}</label>

                        <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                               tabindex="1" placeholder="{{translate('email@address.com')}}" aria-label="email@address.com"
                               required data-msg="Please enter a valid email address.">
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div class="js-form-message form-group">
                        <label class="input-label" for="signupSrPassword" tabindex="0">
                                <span class="d-flex justify-content-between align-items-center">
                                {{translate('password')}}
                                </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                   name="password" id="signupSrPassword" placeholder="{{translate('8+ characters required')}}"
                                   aria-label="8+ characters required" required
                                   data-msg="Your password is invalid. Please try again."
                                   data-hs-toggle-password-options='{
                                                "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End Form Group -->

                    <!-- Checkbox -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                   name="remember">
                            <label class="custom-control-label text-muted" for="termsCheckbox">
                                {{translate('remember')}} {{translate('me')}}
                            </label>
                        </div>
                    </div>
                    <!-- End Checkbox -->

                    {{-- recaptcha --}}
                    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                        <div id="recaptcha_element" class="w-100" data-type="image"></div>
                        <br/>
                    @else
                        <div class="row p-2">
                            <div class="col-5 pr-0">
                                <input type="text" class="form-control form-control-lg" name="default_captcha_value" value=""
                                       placeholder="{{translate('Enter captcha value')}}" style="border: none" autocomplete="off">
                            </div>
                            <div class="col-7 input-icons" class="bg-white rounded">
                                <a onclick="javascript:re_captcha();">
                                    <img src="{{ URL('/branch/auth/code/captcha/1') }}" class="input-field" id="default_recaptcha_id" style="display: inline;width: 80%; height: 75%">
                                    <i class="tio-refresh icon"></i>
                                </a>
                            </div>
                        </div>
                    @endif


                    <button type="submit" class="btn btn-lg btn-block btn-primary">{{translate('sign_in')}}</button>
                </form>
                <!-- End Form -->

                @if(env('APP_MODE')=='demo')
                    <div class="border-top border-primary pt-5 mt-10">
                        <div class="row">
                            <div class="col-10">
                                <span>{{translate('Email : mainb@mainb.com')}}</span><br>
                                <span>{{translate('Password : 12345678')}}</span>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary px-3" onclick="copy_cred()"><i class="tio-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- End Card -->
        </div>
        <!-- End Content -->
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

{{-- recaptcha scripts start --}}
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script type="text/javascript">
        var onloadCallback = function () {
            grecaptcha.render('recaptcha_element', {
                'sitekey': '{{ \App\CentralLogics\Helpers::get_business_settings('recaptcha')['site_key'] }}'
            });
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    <script>
        $("#form-id").on('submit',function(e) {
            var response = grecaptcha.getResponse();

            if (response.length === 0) {
                e.preventDefault();
                toastr.error("{{translate('Please check the recaptcha')}}");
            }
        });
    </script>
@else
    <script type="text/javascript">
        function re_captcha() {
            $url = "{{ URL('/branch/auth/code/captcha') }}";
            $url = $url + "/" + Math.random();
            document.getElementById('default_recaptcha_id').src = $url;
            console.log('url: '+ $url);
        }
    </script>
@endif
{{-- recaptcha scripts end --}}

@if(env('APP_MODE')=='demo')
    <script>
        function copy_cred() {
            $('#signinSrEmail').val('mainb@mainb.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endif
<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
