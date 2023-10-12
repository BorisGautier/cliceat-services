@extends('layouts.branch.app')

@section('title', translate('Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="">
                <span class="page-header-title">
                {{translate($status)}} {{translate('Orders')}}
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{ $orders->total() }}</span>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">

            <!-- Filter Card -->
            <div class="card">
                <div class="card-body">
                    <form action="{{url()->current()}}" id="form-data" method="GET">
                        <div class="row gy-3 gx-2 align-items-end">
                            <div class="col-12 pb-0">
                                <h4 class="mb-0">{{translate('Select Date Range')}}</h4>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-0">
                                    <label class="text-dark">{{ translate('Start Date') }}</label>
                                    <input type="date" name="from" value="{{$from}}" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group mb-0">
                                    <label class="text-dark">{{ translate('End Date') }}</label>
                                    <input type="date" value="{{$to}}" name="to" id="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 d-flex gap-2">
                                <button type="reset" class="btn btn-secondary flex-grow-1">{{ translate('Clear') }}</button>
                                <button type="submit" class="btn btn-primary flex-grow-1 text-nowrap">{{ translate('Show_Data') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Filter Card -->
            @if($status == 'all')
            <div class="px-4 mt-4">
                <div class="row g-2">
                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'pending'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/pending.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('pending')}}</span>
                                </h6>
                                <span class="card-title text-0661CB">
                                    {{$order_count['pending']}}
                            </span>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'confirmed'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/confirmed.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('confirmed')}}</span>
                                </h6>
                                <span class="card-title text-107980">
                                    {{$order_count['confirmed']}}
                            </span>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'processing'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/packaging.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('processing')}}</span>
                                </h6>
                                <span class="card-title text-danger">
                                    {{$order_count['processing']}}
                            </span>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'out_for_delivery'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/out_for_delivery.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('out_for_delivery')}}</span>
                                </h6>
                                <span class="card-title text-00B2BE">
                                    {{$order_count['out_for_delivery']}}
                            </span>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'delivered'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/delivered.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('delivered')}}</span>
                                </h6>
                                <span class="card-title text-success">
                                {{$order_count['delivered']}}
                            </span>
                            </div>
                        </a>
                    </div>

                    <!-- Static Cancel -->
                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'canceled'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/canceled.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('canceled')}}</span>
                                </h6>
                                <span class="card-title text-danger">
                                    {{$order_count['canceled']}}
                            </span>
                            </div>
                        </a>
                    </div>
                    <!-- Static Cancel -->

                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'returned'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/returned.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('returned')}}</span>
                                </h6>
                                <span class="card-title text-warning">
                                    {{$order_count['returned']}}
                            </span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'returned'])}}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                    <img src="{{asset('assets/admin/img/icons/failed_to_deliver.png')}}" alt="dashboard" class="oder--card-icon">
                                    <span>{{translate('failed_to_deliver')}}</span>
                                </h6>
                                <span class="card-title text-danger">
                                    {{$order_count['failed']}}
                            </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <!-- Header -->
            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search by ID, customer or payment status')}}" aria-label="Search"
                                        value="{{$search}}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                        <div>
                            <button type="button" class="btn btn-outline-primary" data-toggle="dropdown" aria-expanded="false">
                                <i class="tio-download-to"></i>
                                {{translate('Export')}}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('branch.orders.export-excel')}}">
                                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                        {{translate('Excel')}}
                                    </a>
                                </li>
{{--                                <li>--}}
{{--                                    <a href="{{route('admin.orders.export')}}" class="dropdown-item d-flex align-items-center gap-2">--}}
{{--                                        <i class="tio-pages"></i>--}}
{{--                                        {{translate('Bulk_Export')}}--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <div class="py-4">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('order_ID')}}</th>
                                <th>{{translate('Delivery_Date')}}</th>
{{--                                <th>{{translate('Time_Slot')}}</th>--}}
                                <th>{{translate('Customer_Info')}}</th>
                                <th>{{translate('Branch')}}</th>
                                <th>{{translate('total_Amount')}}</th>
                                <th>{{translate('order_Status')}}</th>
                                <th>{{translate('order_Type')}}</th>
                                <th class="text-center">{{translate('actions')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)

                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="">
                                    {{ $orders->firstitem()+$key }}
                                </td>
                                <td>
                                    <a class="text-dark" href="{{route('branch.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>
                                        {{date('d M Y',strtotime($order['created_at']))}}
                                    </div>
                                    <div>{{date('h:i A',strtotime($order['created_at']))}}</div>
                                </td>
{{--                                <td>12:30:00 - 15:30:00</td>--}}
                                <td>
                                    @if($order->customer)
                                        <a class="text-dark text-capitalize"
                                        href="{{route('branch.orders.details',['id'=>$order['id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                    @else
                                        <span class="text-capitalize text-muted">
                                            {{translate('Customer_Unavailable')}}
                                        </span>
                                    @endif
                                </td>
                                <td><span class="badge-soft-info px-2 py-1 rounded">{{$order->branch->name}}</span></td>
                                <td>
                                    <div>{{ \App\CentralLogics\Helpers::set_symbol($order['order_amount'] + $order['delivery_charge'])  }}</div>

                                    @if($order->payment_status=='paid')
                                        <span class="badge badge-soft-success">{{translate('paid')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">{{translate('unpaid')}}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    @if($order['order_status']=='pending')
                                        <span class="badge-soft-info px-2 py-1 rounded">{{translate('pending')}}</span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge-soft-info px-2 py-1 rounded">{{translate('confirmed')}}</span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge-soft-warning px-2 py-1 rounded">{{translate('processing')}}</span>
                                    @elseif($order['order_status']=='out_for_delivery')
                                        <span class="badge-soft-warning px-2 py-1 rounded">{{translate('out_for_delivery')}}</span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge-soft-success px-2 py-1 rounded">{{translate('delivered')}}</span>
                                    @else
                                        <span class="badge-soft-danger px-2 py-1 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    @if($order['order_type']=='take_away')
                                        <span class="badge-soft-success px-2 rounded">{{translate('take_away')}}
                                        </span>
                                    @else
                                        <span class="badge-soft-success px-2 rounded">{{translate('delivery')}}
                                        </span>
                                    @endif

                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-sm btn-outline-info square-btn"
                                        href="{{route('branch.orders.details',['id'=>$order['id']])}}"><i
                                                class="tio-visible"></i></a>
                                        <button class="btn btn-sm btn-outline-success square-btn" target="_blank" type="button"
                                                onclick="print_invoice('{{$order->id}}')"><i
                                                class="tio-download"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->

                <div class="table-responsive mt-4 px-3">
                    <div class="d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {!! $orders->links() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>

    <div class="modal fade" id="print-invoice" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{translate('print')}} {{translate('invoice')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row" style="font-family: emoji;">
                    <div class="col-md-12">
                        <center>
                            <input type="button" class="btn btn-primary non-printable" onclick="printDiv('printableArea')"
                                   value="{{translate('Proceed, If thermal printer is ready.')}}"/>
                        <a href="{{url()->previous()}}" class="btn btn-danger non-printable">{{translate('Back')}}</a>
                        </center>
                        <hr class="non-printable">
                    </div>
                    <div class="row" id="printableArea" style="margin: auto;">

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('branch.order.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>

    <script>
        function print_invoice(order_id) {
            $.get({
                url: '{{url('/')}}/branch/pos/invoice/'+order_id,
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    console.log("success...")
                    $('#print-invoice').modal('show');
                    $('#printableArea').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function printDiv(divName) {

            if($('html').attr('dir') === 'rtl') {
                $('html').attr('dir', 'ltr')
                var printContents = document.getElementById(divName).innerHTML;
                document.body.innerHTML = printContents;
                $('#printableAreaContent').attr('dir', 'rtl')
                window.print();
                $('html').attr('dir', 'rtl')
                location.reload();
            }else{
                var printContents = document.getElementById(divName).innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                location.reload();
            }

        }

    </script>

    <script>

    </script>
@endpush
