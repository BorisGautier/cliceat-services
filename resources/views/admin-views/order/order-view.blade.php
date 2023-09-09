@extends('layouts.admin.app')

@section('title', translate('Order Details'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/order_details.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Order_Details')}}
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}}</span>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="px-card py-3">
                        <div class="row gy-2">
                            <div class="col-sm-6 d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="page-header-title h1 mb-3">{{translate('order')}} #{{$order['id']}}</h2>
                                    <h5 class="text-capitalize">
                                        <i class="tio-shop"></i>
                                        {{translate('branch')}} :
                                        <label class="badge-soft-info px-2 rounded">
                                            {{$order->branch?$order->branch->name:'Branch deleted!'}}
                                        </label>
                                    </h5>

                                    <div class="mt-2 d-flex flex-column">
                                        @if($order['order_type'] == 'dine_in')
                                            <div class="hs-unfold">
                                                <h5 class="text-capitalize">
                                                    <i class="tio-table"></i>
                                                    {{translate('table no')}} : <label
                                                        class="badge badge-secondary">{{$order->table?$order->table->number:'Table deleted!'}}</label>
                                                </h5>
                                            </div>
                                            @if($order['number_of_people'] != null)
                                                <div class="hs-unfold">
                                                    <h5 class="text-capitalize">
                                                        <i class="tio-user"></i>
                                                        {{translate('number of people')}} : <label
                                                            class="badge badge-secondary">{{$order->number_of_people}}</label>
                                                    </h5>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="">
                                        {{translate('Order_Date_&_Time')}}: <i class="tio-date-range"></i>{{date('d M Y',strtotime($order['created_at']))}} {{ date(config('time_format'), strtotime($order['created_at'])) }}
                                    </div>
                                </div>

                                <h5>{{translate('order')}} {{translate('note')}} : {{$order['order_note']}}</h5>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-right">
                                    <div class="d-flex flex-wrap gap-2 justify-content-sm-end">
                                        @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')

                                            <div class="hs-unfold ml-1">
                                                @if($order['order_status']=='out_for_delivery')
                                                    @php($origin=\App\Model\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->first())
                                                    @php($current=\App\Model\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->latest()->first())
                                                    @if(isset($origin))
                                                        <a class="btn btn-outline-primary" target="_blank"
                                                           title="{{translate('Delivery Man Last Location')}}" data-toggle="tooltip" data-placement="top"
                                                           href="https://www.google.com/maps/dir/?api=1&origin={{$origin['latitude']}},{{$origin['longitude']}}&destination={{$current['latitude']}},{{$current['longitude']}}">
                                                            <i class="tio-map"></i> {{translate('Show_Location_in_Map')}}
                                                        </a>
                                                    @else
                                                        <a class="btn btn-outline-primary" href="javascript:" data-toggle="tooltip"
                                                           data-placement="top" title="{{translate('Waiting for location...')}}">
                                                            <i class="tio-map"></i> {{translate('Show_Location_in_Map')}}
                                                        </a>
                                                    @endif
                                                @else
                                                    <a class="btn btn-outline-dark" href="javascript:" onclick="last_location_view()"
                                                       data-toggle="tooltip" data-placement="top"
                                                       title="{{translate('Only available when order is out for delivery!')}}">
                                                        <i class="tio-map"></i> {{translate('Show_Location_in_Map')}}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                        <a class="btn btn-info" href={{route('admin.orders.generate-invoice',[$order['id']])}}>
                                            <i class="tio-print"></i> {{translate('Print_Invoice')}}
                                        </a>
                                    </div>

                                    <div class="d-flex gap-3 justify-content-sm-end my-3">
                                        <div class="text-dark font-weight-semibold">{{translate('Status')}} :</div>
                                        @if($order['order_status']=='pending')
                                            <span class="badge-soft-info px-2 rounded text-capitalize">{{translate('pending')}}</span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge-soft-info px-2 rounded text-capitalize">{{translate('confirmed')}}</span>
                                        @elseif($order['order_status']=='processing')
                                            <span class="badge-soft-warning px-2 rounded text-capitalize">{{translate('processing')}}</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                            <span class="badge-soft-warning px-2 rounded text-capitalize">{{translate('out_for_delivery')}}</span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge-soft-success px-2 rounded text-capitalize">{{translate('delivered')}}</span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">{{translate('failed_to_deliver')}}</span>
                                        @else
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>


                                    <div class="text-capitalize d-flex gap-3 justify-content-sm-end mb-3">
                                        <span>{{translate('payment')}} {{translate('method')}} :</span>
                                        <span class="text-dark">{{str_replace('_',' ',$order['payment_method'])}}</span>
                                    </div>

{{--                                    <div class="d-flex gap-3 justify-content-sm-end align-items-center mb-3">--}}
                                        @if($order['transaction_reference']==null && $order['order_type']!='pos')
                                        <div class="d-flex gap-3 justify-content-sm-end align-items-center mb-3">
                                            {{translate('reference')}} {{translate('code')}} :
                                            <button class="btn btn-outline-primary px-3 py-1" data-toggle="modal"
                                                    data-target=".bd-example-modal-sm">
                                                {{translate('add')}}
                                            </button>
                                        </div>
                                        @elseif($order['order_type']!='pos')
                                        <div class="d-flex gap-3 justify-content-sm-end align-items-center mb-3">
                                            {{translate('reference')}} {{translate('code')}}
                                            : {{$order['transaction_reference']}}
                                        </div>
                                        @endif
{{--                                    </div>--}}

                                    <div class="d-flex gap-3 justify-content-sm-end mb-3">
                                        <div>{{translate('Payment_Status')}} :</div>
                                        @if($order['payment_status']=='paid')
                                            <span class="badge-soft-success px-2 rounded text-capitalize">{{translate('paid')}}</span>
                                        @else
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">{{translate('unpaid')}}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-3 justify-content-sm-end mb-3 text-capitalize">
                                        {{translate('order')}} {{translate('type')}}
                                        : <label class="badge-soft-info px-2 rounded">
                                            {{str_replace('_',' ',$order['order_type'])}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <div class="py-4 table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('Item Details')}}</th>
                                <th>{{translate('Price')}}</th>
                                <th>{{translate('Discount')}}</th>
                                <th>{{translate('Tax')}}</th>
                                <th class="text-right">{{translate('Total_price')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                            </tr>
                            @php($sub_total=0)
                            @php($total_tax=0)
                            @php($total_dis_on_pro=0)
                            @php($add_ons_cost=0)
                            @php($add_on_tax=0)
                            @php($add_ons_tax_cost=0)
                            @foreach($order->details as $detail)
                                @php($product_details = json_decode($detail['product_details'], true))
                                @php($add_on_qtys=json_decode($detail['add_on_qtys'],true))
                                @php($add_on_prices=json_decode($detail['add_on_prices'],true))
                                @php($add_on_taxes=json_decode($detail['add_on_taxes'],true))

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media gap-3 w-max-content">

                                            <img class="img-fluid avatar avatar-lg"
                                                 src="{{asset('storage/app/public/product/')}}/{{$detail->product?->image}}"
                                                 onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                                                 alt="Image Description">

                                            <div class="media-body text-dark fz-12">
{{--                                                <h6 class="text-capitalize">{{$detail->product?->name}}</h6>--}}
                                                <h6 class="text-capitalize">{{$product_details['name']}}</h6>
                                                <div class="d-flex gap-2">
                                                    @if (isset($detail['variation']))
                                                        @foreach(json_decode($detail['variation'],true) as  $variation)
                                                            @if (isset($variation['name'])  && isset($variation['values']))
                                                                <span class="d-block text-capitalize">
                                                                <strong>{{  $variation['name']}} -</strong>
                                                            </span>
                                                                @foreach ($variation['values'] as $value)

                                                                    <span class="d-block text-capitalize">
                                                                     {{ $value['label']}} :
                                                                    <strong>{{\App\CentralLogics\Helpers::set_symbol( $value['optionPrice'])}}</strong>
                                                                </span>
                                                                @endforeach
                                                            @else
                                                                @if (isset(json_decode($detail['variation'],true)[0]))
                                                                    <strong><u> {{  translate('Variation') }} : </u></strong>
                                                                    @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                                        <div class="font-size-sm text-body">
                                                                            <span>{{$key1}} :  </span>
                                                                            <span class="font-weight-bold">{{$variation}}</span>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="font-size-sm text-body">
                                                            <span class="text-dark">{{translate('price')}}  : {{\App\CentralLogics\Helpers::set_symbol($detail['price'])}}</span>
                                                        </div>
                                                    @endif

                                                    <div class="d-flex gap-2">
                                                        <span class="">{{translate('Qty')}} :  </span>
                                                        <span>{{$detail['quantity']}}</span>
                                                    </div>

                                                    <br>
                                                    @php($addon_ids = json_decode($detail['add_on_ids'],true))
                                                    @if ($addon_ids)
                                                    <span>
                                                        <u><strong>{{translate('addons')}}</strong></u>
                                                        @foreach($addon_ids as $key2 =>$id)
                                                            @php($addon=\App\Model\AddOn::find($id))
                                                            @php($add_on_qtys==null? $add_on_qty=1 : $add_on_qty=$add_on_qtys[$key2])

                                                            <div class="font-size-sm text-body">
                                                                    <span>{{$addon ? $addon['name'] : translate('addon deleted')}} :  </span>
                                                                    <span class="font-weight-semibold">
                                                                        {{$add_on_qty}} x {{ \App\CentralLogics\Helpers::set_symbol($add_on_prices[$key2]) }} <br>
                                                                    </span>
                                                                </div>
                                                            @php($add_ons_cost+=$add_on_prices[$key2] * $add_on_qty)
                                                            @php($add_ons_tax_cost +=  $add_on_taxes[$key2] * $add_on_qty)
                                                        @endforeach
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php($amount=$detail['price']*$detail['quantity'])
                                        {{\App\CentralLogics\Helpers::set_symbol($amount)}}
                                    </td>
                                    <td>
                                        @php($tot_discount = $detail['discount_on_product']*$detail['quantity'])
                                        {{\App\CentralLogics\Helpers::set_symbol($tot_discount)}}
                                    </td>
                                    <td>
                                        @php($product_tax = $detail['tax_amount']*$detail['quantity'])
                                        {{\App\CentralLogics\Helpers::set_symbol($product_tax + $add_ons_tax_cost)}}
                                    </td>
                                    <td class="text-right">{{\App\CentralLogics\Helpers::set_symbol($amount-$tot_discount + $product_tax)}}</td>
                                </tr>
                                @php($total_dis_on_pro += $tot_discount)
                                @php($sub_total += $amount)
                                @php($total_tax += $product_tax)

                            @endforeach
                            </tbody>
                        </table>
                    </div>


                    <div class="card-body pt-0">
                        <hr>
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row">
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            {{translate('items')}} {{translate('price')}} <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">{{ \App\CentralLogics\Helpers::set_symbol($sub_total) }}</dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('tax')}} / {{translate('vat')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">{{ \App\CentralLogics\Helpers::set_symbol($total_tax + $add_ons_tax_cost) }}</dd>

                                    <dt class="col-6">

                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('addon')}} {{translate('cost')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        {{ \App\CentralLogics\Helpers::set_symbol($add_ons_cost) }}
                                    </dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('item')}} {{translate('discount')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">{{ \App\CentralLogics\Helpers::set_symbol($total_dis_on_pro) }}</dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>
                                        {{translate('subtotal')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        {{ \App\CentralLogics\Helpers::set_symbol($sub_total =$sub_total+$total_tax+$add_ons_cost-$total_dis_on_pro + $add_ons_tax_cost) }}</dd>

                                    <dt class="col-6">

                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('coupon')}} {{translate('discount')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        - {{ \App\CentralLogics\Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('extra discount')}} </span>
                                        <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        - {{ \App\CentralLogics\Helpers::set_symbol($order['extra_discount']) }}</dd>
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>
                                                {{translate('delivery')}} {{translate('fee')}}</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        @if($order['order_type']=='take_away')
                                            @php($del_c=0)
                                        @else
                                            @php($del_c=$order['delivery_charge'])
                                        @endif
                                        {{ \App\CentralLogics\Helpers::set_symbol($del_c) }}
                                    </dd>

                                    <dt class="col-6 border-top pt-2 fz-16 font-weight-bold">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>{{translate('total')}}</span>
                                        <span>:</span>
                                        </div>
                                    </dt>
                                    {{--<dd class="col-6 border-top pt-2 fz-16 font-weight-bold text-dark">{{ \App\CentralLogics\Helpers::set_symbol($sub_total+$del_c+$total_tax+$add_ons_cost-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd> --}}
                                    <dd class="col-6 border-top pt-2 fz-16 font-weight-bold text-dark text-right">{{ \App\CentralLogics\Helpers::set_symbol($sub_total - $order['coupon_discount_amount'] - $order['extra_discount'] + $del_c) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>

{{--            @if($order->customer)--}}
                <div class="col-lg-4">
                    @if($order['order_type'] != 'pos')
                    <div class="card mb-3">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <h4 class="mb-0 text-center">{{translate('Order_Setup')}}</h4>
                            @if($order['order_type'] != 'pos')
                                <div class="">
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Change_Order_Status')}}</label>
                                    <select name="order_status" onchange="route_alert('{{route('admin.orders.status',['id'=>$order['id']])}}'+'&order_status='+ this.value,'{{translate("Change the order status to ") }}'+  this.value.replace(/_/g, ' '))" class="status custom-select">
                                        @if($order['order_type'] != 'dine_in')
                                            <option value="pending" {{$order['order_status'] == 'pending'? 'selected' : ''}}> {{translate('pending')}}</option>
                                        @endif
                                        <option value="confirmed" {{$order['order_status'] == 'confirmed'? 'selected' : ''}}> {{translate('confirmed')}}</option>
                                        @if($order['order_type'] != 'dine_in')
                                            <option value="processing" {{$order['order_status'] == 'processing'? 'selected' : ''}}> {{translate('processing')}}</option>
                                            <option value="out_for_delivery" {{$order['order_status'] == 'out_for_delivery'? 'selected' : ''}}>{{translate('Out_For_Delivery')}} </option>
                                            <option value="delivered" {{$order['order_status'] == 'delivered'? 'selected' : ''}}>{{translate('Delivered')}} </option><option value="returned" {{$order['order_status'] == 'returned'? 'selected' : ''}}> {{translate('Returned')}}</option>
                                            <option value="failed" {{$order['order_status'] == 'failed'? 'selected' : ''}}>{{translate('Failed_to_Deliver')}} </option>
                                        @endif
                                        @if($order['order_type'] == 'dine_in')
                                            <option value="cooking" {{$order['order_status'] == 'cooking'? 'selected' : ''}}> {{translate('cooking')}}</option>
                                            <option value="completed" {{$order['order_status'] == 'completed'? 'selected' : ''}}> {{translate('completed')}}</option>
                                        @endif
                                                <option value="canceled" {{$order['order_status'] == 'canceled'? 'selected' : ''}}>{{translate('canceled')}} </option>
                                    </select>
                                </div>
                                <div class="">
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item"
                                           onclick="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'paid'])}}','{{\App\CentralLogics\translate("Change status to paid ?")}}')"
                                           href="javascript:">{{translate('paid')}}</a>
                                        <a class="dropdown-item"
                                           onclick="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'unpaid'])}}','{{\App\CentralLogics\translate("Change status to unpaid ?")}}')"
                                           href="javascript:">{{translate('unpaid')}}</a>
                                    </div>
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Payment_Status')}}</label>
                                    <select name="order_status" onchange="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id']])}}'+'&payment_status='+ this.value,'{{\App\CentralLogics\translate("Change status to ")}}' + this.value)" class="status custom-select" data-id="100147">
                                        <option value="paid" {{$order['payment_status'] == 'paid'? 'selected' : ''}}> {{translate('paid')}}</option>
                                        <option value="unpaid" {{$order['payment_status'] == 'unpaid'? 'selected' : ''}}>{{translate('unpaid')}} </option>
                                    </select>
                                </div>
                            @endif
                            @if($order->customer)
                            <div class="">
{{--                                need change option--}}
                                <label class="font-weight-bold text-dark fz-14">{{translate('Delivery_Date_&_Time')}} {{$order['delivery_date'] > \Carbon\Carbon::now()->format('Y-m-d')? translate('(Scheduled)') : ''}}</label>
                                <div class="d-flex gap-2 flex-wrap flex-xxl-nowrap">
                                    <input onchange="changeDeliveryTimeDate(this)" name="delivery_date" type="date" class="form-control" value="{{$order['delivery_date'] ?? ''}}">
                                    <input onchange="changeDeliveryTimeDate(this)" name="delivery_time" type="time" class="form-control" value="{{$order['delivery_time'] ?? ''}}">
                                </div>
                            </div>
                            @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in' && !$order['delivery_man_id'])

                                <a href="#" class="btn btn-primary btn-block d-flex gap-1 justify-content-center align-items-center" data-toggle="modal" data-target="#assignDeliveryMan">
                                    <img width="17" src="{{asset('assets/admin/img/icons/assain_delivery_man.png')}}" alt="">
                                    {{translate('Assign_Delivery_Man')}}
                                </a>
                            @endif
@endif
                            {{-- counter --}}
                            <div class="">
                                @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Food_Preparation_Time')}}</label>
                                    <div class="form-control justify-content-between">
                                        <span class="ml-2 ml-sm-3 ">
                                        <i class="tio-timer d-none" id="timer-icon"></i>
                                        <span id="counter" class="text-info"></span>
                                        <i class="tio-edit p-2 d-none" id="edit-icon" style="cursor: pointer;" data-toggle="modal" data-target="#counter-change" data-whatever="@mdo"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>


                            @if($order->delivery_man_id)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="mb-4 d-flex gap-2">
                                    <span class="card-header-icon">
                                        <i class="tio-user text-dark"></i>
                                    </span>
                                            <span>{{ translate('delivery_man') }}</span>
                                            <a  href="#"  data-toggle="modal" data-target="#assignDeliveryMan"
                                                class="text--base cursor-pointer ml-auto">
                                                {{translate('Change')}}
                                            </a>
                                        </h4>
                                        <div class="media flex-wrap gap-3">
                                            <a target="_blank" class="" href="{{route('admin.customer.view',[$order->customer['id']])}}">
                                                <img class="avatar avatar-lg rounded-circle" src="{{asset('storage/app/public/delivery-man/'.$order->delivery_man->image)}}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" alt="Image">
                                            </a>
                                            <div class="media-body d-flex flex-column gap-1">
                                                <a target="" href="#" class="text-dark"><span>{{$order->delivery_man['f_name'].' '.$order->delivery_man['l_name'] ?? ''}}</span></a>
                                                <span class="text-dark"> <span>{{$order->delivery_man['orders_count']}}</span> {{translate('Orders')}}</span>
                                                <span class="text-dark break-all">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="tel:{{$order->delivery_man['phone']}}" class="text-dark">{{$order->delivery_man['phone'] ?? ''}}</a>
                                        </span>
                                                <span class="text-dark break-all">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$order->delivery_man['email']}}" class="text-dark">{{$order->delivery_man['email'] ?? ''}}</a>
                                        </span>
                                            </div>
                                        </div>
                                        <hr class="w-100">
                                        @if($order['order_status']=='out_for_delivery')
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5>{{translate('Last_location')}}</h5>
                                            </div>
                                            @php($origin=\App\Model\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->first())
                                            @php($current=\App\Model\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->latest()->first())
                                            @if(isset($origin))
                                                <a target="_blank" class="text-dark"
                                                   title="Delivery Boy Last Location" data-toggle="tooltip" data-placement="top"
                                                   href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$current['latitude']}}+{{$current['longitude']}}">
                                                    <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$current['location']?? ''}}
                                                </a>
                                            @else
                                                <a href="javascript:" data-toggle="tooltip" class="text-dark"
                                                   data-placement="top" title="{{translate('Waiting for location...')}}">
                                                    <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{translate('Waiting for location...')}}
                                                </a>
                                            @endif
                                        @else
                                            <a href="javascript:" onclick="last_location_view()" class="text-dark"
                                               data-toggle="tooltip" data-placement="top"
                                               title="{{translate('Only available when order is out for delivery!')}}">
                                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{translate('Only available when order is out for delivery!')}}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')
                            <div class="card">
                                <div class="card-body">
{{--                                    <div class="d-flex justify-content-between gap-3 border-top mt-3 pt-3">--}}
                                    <div class="mb-4 d-flex gap-2 justify-content-between">
                                        <h4 class="mb-0 d-flex gap-2">
                                            <i class="tio-user text-dark"></i>
                                            {{translate('Delivery_Informatrion')}}
                                        </h4>

                                        <div class="edit-btn cursor-pointer" data-toggle="modal" data-target="#deliveryInfoModal">
                                            {{-- <img width="24" src="{{asset('assets/admin/img/icons/edit.png')}}" alt=""> --}}
                                            <i class="tio-edit"></i>
                                        </div>
                                    </div>
                                    <div class="delivery--information-single flex-column">
                                        @php($address=\App\Model\CustomerAddress::find($order['delivery_address_id']))
                                        {{--                                @dump($address)--}}
                                        <div class="d-flex">
                                            <div class="name">{{ translate('Name') }}</div>
                                            <div class="info">{{ $address? $address['contact_person_name']: '' }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">{{translate('Contact')}}</div>
                                            <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="info">{{ $address? $address['contact_person_number']: '' }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">{{translate('floor')}}</div>
                                            <div class="info">{{$address['floor'] ?? ''}}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">{{translate('house')}}</div>
                                            <div class="info">{{$address['house'] ?? ''}}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">{{translate('road')}}</div>
                                            <div class="info">{{$address['road'] ?? ''}}</div>
                                        </div>
                                        <hr class="w-100">
                                        <div class="d-flex align-items-center gap-3">
                                            @if(isset($address['address']) && isset($address['latitude']) && isset($address['longitude']))
                                            <a target="_blank" class="text-dark"
                                               href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                {{$address['address']}}
                                            </a>
                                            @else
                                            <a target="_blank" class="text-dark"
                                               href="#">
                                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                {{translate('no_location_found')}}
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                    @endif


                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <i class="tio-user text-dark"></i>
                                {{ \App\CentralLogics\translate('Customer Information') }}
                            </h4>
                            @if($order->customer)
                                <div class="media flex-wrap gap-3">
                                    <a target="_blank" class="" href="{{route('admin.customer.view',[$order->customer['id']])}}">
                                        <img class="avatar avatar-lg rounded-circle" src="{{asset('storage/app/public/profile/'.$order->customer['image'])}}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" alt="Image">
                                    </a>
                                    <div class="media-body d-flex flex-column gap-1">
                                        <a target="_blank" href="{{route('admin.customer.view',[$order->customer['id']])}}" class="text-dark"><strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong></a>
                                        <span class="text-dark">{{$order->customer['orders_count']}} {{translate('Orders')}}</span>
                                        <span class="text-dark">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a class="text-dark break-all" href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                        </span>
                                        <span class="text-dark">
                                            <i class="tio-email mr-2"></i>
                                            <a class="text-dark break-all" href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if($order->user_id == null)
                                <div class="media flex-wrap gap-3 align-items-center">
                                    <a target="#" class="" >
                                        <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="Image">
                                    </a>
                                    <div class="media-body d-flex flex-column gap-1">
                                        <a target="#"  class="text-dark text-capitalize"><strong>{{translate('walking_customer')}}</strong></a>
                                    </div>
                                </div>
                            @endif
                            @if($order->user_id != null && !isset($order->customer))
                                <div class="media flex-wrap gap-3 align-items-center">
                                    <a target="#" class="" >
                                        <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="Image">
                                    </a>
                                    <div class="media-body d-flex flex-column gap-1">
                                        <a target="#"  class="text-dark text-capitalize"><strong>{{translate('Customer_not_available')}}</strong></a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <i class="tio-user text-dark"></i>
                                {{translate('Branch Information')}}
                            </h4>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar avatar-lg rounded-circle" src="{{asset('storage/app/public/branch/'.$order->branch['image'])}}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" alt="Image">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="text-dark"><span>{{$order->branch['name']}}</span></span>
                                    <span class="text-dark"> <span>{{$order->branch['orders_count']}}</span> {{translate('Orders served')}}</span>
                                    @if($order->branch['phone'])
                                    <span class="text-dark break-all">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        <a class="text-dark" href="tel:{{$order->branch['phone']}}">{{$order->branch['phone']}}</a>
                                    </span>
                                    @endif
                                    <span class="text-dark break-all">
                                        <i class="tio-email mr-2"></i>
                                        <a class="text-dark" href="mailto:{{$order->branch['email']}}">{{$order->branch['email']}}</a>
                                    </span>
                                </div>
                            </div>

                            <hr class="w-100">
                            <div class="d-flex align-items-center text-dark gap-3">
                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">
                                <a target="_blank" class="text-dark"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$order->branch['latitude']}}+{{$order->branch['longitude']}}">
                                    {{$order->branch['address']}}<br>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
{{--            @endif--}}
        </div>
        <!-- End Row -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="assignDeliveryMan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="assignDeliveryManLabel">{{translate('Assign_Delivery_Man')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @foreach($delivery_man as $deliveryMan)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 justify-content-between">
                                <div class="media align-items-center gap-2 flex-wrap">
                                    <div class="avatar">
                                        <img class="img-fit rounded-circle" loading="lazy" decoding="async"
                                         onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                         src="{{asset('/storage/app/public/delivery-man/'.$deliveryMan->image)}}" alt="Jhon Doe">
                                    </div>
                                    <span>{{$deliveryMan['f_name'].' '.$deliveryMan['l_name']}}</span>
                                </div>
                                <a id="{{$deliveryMan->id}}" onclick="addDeliveryMan(this.id)" class="btn btn-primary btn-sm">{{translate('Assign')}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->


    <!-- Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4"
                        id="mySmallModalLabel">{{translate('reference')}} {{translate('code')}} {{translate('add')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                            aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{route('admin.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                   placeholder="{{translate('EX : Code123')}}" required>
                        </div>
                        <!-- End Input Group -->
                        <button class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoModal" id="deliveryInfoModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('Update_Delivery_Informatrion')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>
                @if($order['delivery_address_id'])
                    <form action="{{route('admin.orders.update-shipping',[$order['delivery_address_id']])}}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$order->user_id}}">
                        <input type="hidden" name="order_id" value="{{$order->id}}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{translate('Type')}}</label>
                                <input type="text" name="address_type" class="form-control"
                                       placeholder="{{translate('EX : Home')}}" value="{{ $address['address_type'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{translate('Name')}}</label>
                                <input type="text" class="form-control" name="contact_person_name"
                                       placeholder="{{translate('EX : Jhon Doe')}}" value="{{ $address['contact_person_name'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{translate('Contact_Number')}}</label>
                                <input type="text" class="form-control" name="contact_person_number"
                                       placeholder="{{translate('EX : 01888888888')}}" value="{{ $address['contact_person_number']?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{translate('floor')}}</label>
                                <input type="text" class="form-control" name="floor"
                                       placeholder="{{translate('EX : 5')}}" value="{{ $address['floor'] ?? '' }}" >
                            </div>
                            <div class="form-group">
                                <label>{{translate('house')}}</label>
                                <input type="text" class="form-control" name="house"
                                       placeholder="{{translate('EX : 21/B')}}" value="{{ $address['house'] ?? '' }}" >
                            </div>
                            <div class="form-group">
                                <label>{{translate('road')}}</label>
                                <input type="text" class="form-control" name="road"
                                       placeholder="{{translate('EX : Baker Street')}}" value="{{ $address['road'] ?? '' }}" >
                            </div>
                            <div class="form-group">
                                <label>{{translate('Address')}}</label>
                                <input type="text" class="form-control" name="address"
                                       placeholder="{{translate('EX : Dhaka,_Bangladesh')}}" value="{{ $address['address'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{translate('latitude')}}</label>
                                <input type="text" class="form-control" name="latitude"
                                       placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['latitude'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{translate('longitude')}}</label>
                                <input type="text" class="form-control" name="longitude"
                                       placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['longitude'] ?? '' }}" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal -->
    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
        <div class="modal fade" id="counter-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px">{{ translate('Need time to prepare the food') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('admin.orders.increase-preparation-time', ['id' => $order->id])}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group text-center">
                                <input type="number" min="0" name="extra_minute" id="extra_minute" class="form-control" placeholder="{{translate('EX : 20')}}" required>
                            </div>

                            <div class="form-group flex-between">
                                <div class="badge text-info shadow" onclick="predefined_time_input(10)" style="cursor: pointer">{{ translate('10min') }}</div>
                                <div class="badge text-info shadow" onclick="predefined_time_input(20)" style="cursor: pointer">{{ translate('20min') }}</div>
                                <div class="badge text-info shadow" onclick="predefined_time_input(30)" style="cursor: pointer">{{ translate('30min') }}</div>
                                <div class="badge text-info shadow" onclick="predefined_time_input(40)" style="cursor: pointer">{{ translate('40min') }}</div>
                                <div class="badge text-info shadow" onclick="predefined_time_input(50)" style="cursor: pointer">{{ translate('50min') }}</div>
                                <div class="badge text-info shadow" onclick="predefined_time_input(60)" style="cursor: pointer">{{ translate('60min') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!-- End Modal -->
@endsection

@push('script_2')
    <script>
        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: $('#product_form').serialize(),
                success: function (data) {
                    if(data.status == true) {
                        toastr.success('{{\App\CentralLogics\translate("Delivery man successfully assigned/changed")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }else{
                        toastr.error('{{\App\CentralLogics\translate("Deliveryman man can not assign/change in that status")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function () {
                    toastr.error('{{\App\CentralLogics\translate("Add valid data")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('{{\App\CentralLogics\translate("Only available when order is out for delivery!")}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

    <script>
        function predefined_time_input(min) {
            document.getElementById("extra_minute").value = min;
        }
    </script>
    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
        <script>
            const expire_time = "{{ $order['remaining_time'] }}";
            var countDownDate = new Date(expire_time).getTime();
            const time_zone = "{{ \App\CentralLogics\Helpers::get_business_settings('time_zone') ?? 'UTC' }}";

            var x = setInterval(function() {
                var now = new Date(new Date().toLocaleString("en-US", {timeZone: time_zone})).getTime();

                var distance = countDownDate - now;

                var days = Math.trunc(distance / (1000 * 60 * 60 * 24));
                var hours = Math.trunc((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.trunc((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.trunc((distance % (1000 * 60)) / 1000);


                document.getElementById("timer-icon").classList.remove("d-none");
                document.getElementById("edit-icon").classList.remove("d-none");
                $text = (distance < 0) ? "{{ translate('over') }}" : "{{ translate('left') }}";
                document.getElementById("counter").innerHTML = Math.abs(days) + "d " + Math.abs(hours) + "h " + Math.abs(minutes) + "m " + Math.abs(seconds) + "s " + $text;
                if (distance < 0) {
                    var element = document.getElementById('counter');
                    element.classList.add('text-danger');
                }
            }, 1000);
        </script>
    @endif

    <script>
        function changeDeliveryTimeDate(t) {
            let name = t.name
            let value = t.value
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/ajax-change-delivery-time-date/{{$order['id']}}?' + t.name + '=' + t.value,
                data: {
                    name : name,
                    value : value
                },
                success: function (data) {
                    console.log(data)
                    if(data.status == true && name == 'delivery_date') {
                        toastr.success('{{\App\CentralLogics\translate("Delivery date changed successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else if(data.status == true && name == 'delivery_time'){
                        toastr.success('{{\App\CentralLogics\translate("Delivery time changed successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else {
                        toastr.error('{{\App\CentralLogics\translate("Order No is not valid")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    location.reload();
                },
                error: function () {
                    toastr.error('{{\App\CentralLogics\translate("Add valid data")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
            });
        }
    </script>

@endpush
