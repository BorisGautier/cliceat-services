<div class="coupon__details-left">
    <div class="text-center">
        @if($coupon->discount_type != "amount")
            <h6 class="title" id="title">{{$coupon->discount}}% {{\App\CentralLogics\translate('discount')}}</h6>
        @else
            <h6 class="title" id="title">{{\App\CentralLogics\Helpers::set_symbol($coupon->discount)}} {{\App\CentralLogics\translate('discount')}}</h6>
        @endif
        <h6 class="subtitle">{{\App\CentralLogics\translate('code')}} : <span id="coupon_code">{{$coupon->code}}</span></h6>
        <div class="text-capitalize">
            <span>{{\App\CentralLogics\translate('discount_in')}}</span>
            <strong id="discount_on">{{$coupon->discount_type}}</strong>
        </div>
    </div>
    <div class="coupon-info">
        <div class="coupon-info-item">
            <span>{{\App\CentralLogics\translate('min_purchase')}} :</span>
            <strong id="min_purchase">{{\App\CentralLogics\Helpers::set_symbol($coupon->min_purchase)}}</strong>
{{--            <span class="currency_symbol">$</span>--}}
        </div>
        @if($coupon->discount_type != "amount")
        <div class="coupon-info-item" id="max_discount_modal_div">
            <span>{{\App\CentralLogics\translate('max_discount')}} : </span>
            <strong id="max_discount">{{\App\CentralLogics\Helpers::set_symbol($coupon->max_discount)}}</strong>
{{--            <span class="currency_symbol">$</span>--}}
        </div>
        @endif
        <div class="coupon-info-item">
            <span>{{\App\CentralLogics\translate('start_date')}} : </span>
            <span id="start_date">{{date_format($coupon->start_date, 'Y-m-d')}}</span>
        </div>
        <div class="coupon-info-item">
            <span>{{\App\CentralLogics\translate('expire_date')}} : </span>
            <span id="expire_date">{{date_format($coupon->expire_date, 'Y-m-d')}}</span>
        </div>
    </div>
</div>
<div class="coupon__details-right">
    <div class="coupon">
        <div class="d-flex">
            @if($coupon->discount_type != "amount")
                <h4 class="" id="">{{$coupon->discount}}%</h4>
            @else
                <h4 class="" id="">{{\App\CentralLogics\Helpers::set_symbol($coupon->discount)}}</h4>
            @endif
        </div>

        <span>{{\App\CentralLogics\translate('off')}}</span>
    </div>
</div>
