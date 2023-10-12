@extends('layouts.admin.app')

@section('title', translate('Delivery_Fee_Setup'))

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


        <div class="row g-2">
            <!-- Delivery Fee Steup -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            {{translate('Delivery_Fee_Setup')}}
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.restaurant.update-delivery-fee')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            {{-- delivery management start --}}
                            <div class="row">
                                <div class="col-md-6">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('delivery_management'))
                                    <div class="form-group d-flex align-items-center gap-2">
                                        <input type="radio" name="shipping_status" value="1"
                                               {{$config['status']==1?'checked':''}} id="shipping_by_distance_status">
                                        <label for="shipping_by_distance_status" class="text-dark font-weight-bold mb-0">{{translate('delivery_charge_by_distance')}}</label>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>{{translate('Minimum delivery Charge')}} </label><br>
                                            <input type="number" step=".01" class="form-control"
                                                   name="min_shipping_charge"
                                                   value="{{$config['min_shipping_charge']}}"
                                                   id="min_shipping_charge" {{ $config['status']==0?'disabled':'' }} >
                                        </div>
                                        <div class="form-group">
                                            <label>{{translate('delivery Charge / Kilometer')}}</label><br>
                                            <input type="number" step=".01" class="form-control" name="shipping_per_km"
                                                   value="{{$config['shipping_per_km']}}"
                                                   id="shipping_per_km" {{ $config['status']==0?'disabled':'' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group d-flex align-items-center gap-2">
                                            <input type="radio" name="shipping_status" value="0"
                                                   {{$config['status']==0?'checked':''}} id="default_delivery_status">
                                            <label for="default_delivery_status" class="text-dark font-weight-bold mb-0">{{translate('default_delivery_charge')}}</label>
                                        </div>
                                        <div class="form-group">
                                            @php($delivery=\App\Model\BusinessSetting::where('key','delivery_charge')->first()->value)
                                            <label class="">{{translate('delivery_Charge')}} </label>
                                            <input type="number" min="0" step=".01" name="delivery_charge" value="{{$delivery}}"
                                                   class="form-control" placeholder="{{translate('EX: 100')}}" required
                                                   {{ $config['status']==1?'disabled':'' }} id="delivery_charge">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
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
    <script>
        @php($time_zone=\App\Model\BusinessSetting::where('key','time_zone')->first())
        @php($time_zone = $time_zone->value ?? null)
        $('[name=time_zone]').val("{{$time_zone}}");

        @php($language=\App\Model\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        let language = <?php echo($language); ?>;
        $('[id=language]').val(language);

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
        $("#language").on("change", function () {
            $("#alert_box").css("display", "block");
        });
    </script>

    <script>

        function currency_symbol_position(route) {
            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success(data.message);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        $(document).on('ready', function () {
            @php($country=\App\CentralLogics\Helpers::get_business_settings('country')??'BD')
            $("#country option[value='{{$country}}']").attr('selected', 'selected').change();
        })
    </script>

    <script>
        $('#shipping_by_distance_status').on('click',function(){
            $("#delivery_charge").prop('disabled', true);
            $("#min_shipping_charge").prop('disabled', false);
            $("#shipping_per_km").prop('disabled', false);
        });

        $('#default_delivery_status').on('click',function(){
            $("#delivery_charge").prop('disabled', false);
            $("#min_shipping_charge").prop('disabled', true);
            $("#shipping_per_km").prop('disabled', true);
        });
    </script>

@endpush
