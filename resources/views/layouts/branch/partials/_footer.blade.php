<div class="footer">
    <div class="row justify-content-between align-items-center">
        <div class="col">
            <p class="font-size-sm mb-0">
                <span class="d-none d-sm-inline-block">{{\App\Model\BusinessSetting::where(['key'=>'footer_text'])->first()->value}}</span>
            </p>
        </div>
        <div class="col-auto">
            <div class="d-flex justify-content-end">
                <!-- List Dot -->
                <ul class="list-inline-menu justify-content-center justify-content-md-end">
                    <li>
                        <a href="{{route('branch.settings')}}">
                            <span>{{translate('Profile')}}</span>
                            <img width="12" class="avatar-img rounded-0" src="{{asset('assets/admin/img/icons/profile.png')}}" alt="Image Description">
                        </a>
                    </li>

                    <li>
                        <a href="{{route('branch.dashboard')}}">
                            <span>{{translate('Home')}}</span>
                            <img width="12" class="avatar-img rounded-0" src="{{asset('assets/admin/img/icons/home.png')}}" alt="Image Description">
                        </a>
                    </li>
                </ul>
{{--                <ul class="list-inline list-separator">--}}
{{--                    <li class="list-inline-item">--}}
{{--                        <a class="list-separator-link" href="{{route('branch.business-settings.restaurant.restaurant-setup')}}">{{trans('messages.restaurant')}} {{trans('messages.settings')}}</a>--}}
{{--                    </li>--}}

{{--                    <li class="list-inline-item">--}}
{{--                        <a class="list-separator-link" href="{{route('branch.settings')}}">{{trans('messages.profile')}}</a>--}}
{{--                    </li>--}}

{{--                    <li class="list-inline-item">--}}
{{--                        <!-- Keyboard Shortcuts Toggle -->--}}
{{--                        <div class="hs-unfold">--}}
{{--                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"--}}
{{--                               href="{{route('admin.dashboard')}}">--}}
{{--                                <i class="tio-home-outlined"></i>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        <!-- End Keyboard Shortcuts Toggle -->--}}
{{--                    </li>--}}
{{--                </ul>--}}
                <!-- End List Dot -->
            </div>
        </div>
    </div>
</div>
