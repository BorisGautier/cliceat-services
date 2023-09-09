@extends('layouts.admin.app')

@section('title', translate('Table Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="">
                <span class="page-header-title">
                    {{translate($status)}} {{translate('Table_Orders')}}
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
                                <h4 class="mb-0">{{translate('select_date_range')}}</h4>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="select_branch">{{translate('Select_Branch')}}</label>
                                <!-- Select -->
                                <select class="form-control" name="branch"  onchange="filter_branch_orders(this.value)" id="select_branch">
                                    <option disabled selected>--- {{translate('select')}} {{translate('branch')}} ---</option>
                                    <option value="0" {{session('branch_filter')==0?'selected':''}}>{{translate('all')}} {{translate('branch')}}</option>
                                    @foreach(\App\Model\Branch::all() as $branch)
                                        <option value="{{$branch['id']}}" {{session('branch_filter')==$branch['id']?'selected':''}}>{{$branch['name']}}</option>
                                    @endforeach
                                </select>
                                <!-- End Select -->
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">{{ translate('Start Date') }}</label>
                                    <input type="date" name="from" value="{{$from}}" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">{{ translate('End Date') }}</label>
                                    <input type="date" value="{{$to}}" name="to" id="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 d-flex gap-2">
                                <button type="clear" class="btn btn-secondary flex-grow-1">{{ translate('Clear') }}</button>
                                <button type="submit" class="btn btn-primary text-nowrap flex-grow-1">{{ translate('Show Data') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Filter Card -->

            @if($status == 'all')
                <div class="px-4 mt-4">
                    <div class="row g-2">
                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.list',['confirmed'])}}">
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

                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.list',['cooking'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/cooking.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('cooking')}}</span>
                                    </h6>
                                    <span class="card-title text-danger">
                                {{$order_count['cooking']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.list',['done'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/review.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('ready_to_serve')}}</span>
                                    </h6>
                                    <span class="card-title text-00B2BE">
                                {{$order_count['done']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.list',['completed'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/product.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('completed')}}</span>
                                    </h6>
                                    <span class="card-title text-success">
                                {{$order_count['completed']}}
                            </span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.list',['canceled'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/canceled.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('canceled')}}</span>
                                    </h6>
                                    <span class="card-title text-success">
                                {{$order_count['canceled']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <!-- Static Cancel -->
                        <div class="col-sm-6 col-lg-4">
                            <a class="order--card h-100" href="{{route('admin.table.order.running')}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/out_for_delivery.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('running')}}</span>
                                    </h6>
                                    <span class="card-title text-danger">
                                    {{\App\Model\Order::with('table_order')->whereHas('table_order', function($q){
                                        $q->where('branch_table_token_is_expired', 0);
                                    })->count()}}
                            </span>
                                </div>
                            </a>
                        </div>
                        <!-- Static Cancel -->

                    </div>
                </div>
            @endif
            <!-- Header -->
            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <div>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                           class="form-control"
                                           placeholder="{{translate('Search by Order ID  Order Status or Transaction Reference')}}" aria-label="Search"
                                           value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">{{translate('Search')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                        <div>
                            <button type="button" class="btn btn-outline-primary" data-toggle="dropdown" aria-expanded="false">
                                <i class="tio-download-to"></i>
                                {{translate('export')}}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.table.order.export-excel', ['search'=>$search, 'from' =>$from, 'to' => $to, 'status'=> $status])}}">
                                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                        {{translate('excel')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="py-4">
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th class="">
                            {{translate('SL')}}
                        </th>
                        <th>{{translate('order_ID')}}</th>
                        <th>{{translate('order_Date')}}</th>
                        <th>{{translate('branch')}}</th>
                        <th>{{translate('total_Amount')}}</th>
                        <th>{{translate('order')}} {{translate('status')}}</th>
                        <th>{{translate('order')}} {{translate('type')}}</th>
                        <th class="text-center">{{translate('actions')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)
                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$key+$orders->firstItem()}}
                            </td>
                            <td>
                                <a class="text-dark" href="{{route('admin.table.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>
                                <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                <div>{{date('h:m A',strtotime($order['created_at']))}}</div>
                            </td>
                            <td>
                                @if($order->branch)
                                    {{$order->branch['name']}}
                                @else
                                    <span class="text-muted">
                                        {{translate('Branch unavailable')}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ \App\CentralLogics\Helpers::set_symbol($order['order_amount']) }}</div>
                                @if($order->payment_status=='paid')
                                    <span class="text-success">{{translate('paid')}}</span>
                                @else
                                    <span class="text-danger">{{translate('unpaid')}}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge-soft-info px-2 rounded">{{translate('pending')}}</span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge-soft-info px-2 rounded">{{translate('confirmed')}}</span>
                                @elseif($order['order_status']=='processing')
                                    <span class="badge-soft-warning px-2 rounded">{{translate('processing')}}</span>
                                @elseif($order['order_status']=='picked_up')
                                    <span class="badge-soft-warning px-2 rounded">{{translate('out_for_delivery')}}</span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge-soft-success px-2 rounded">{{translate('delivered')}}</span>
                                @elseif($order['order_status']=='cooking')
                                    <span class="badge-soft-success px-2 rounded">{{translate('cooking')}}</span>
                                @elseif($order['order_status']=='completed')
                                    <span class="badge-soft-success px-2 rounded">{{translate('completed')}}</span>
                                @else
                                    <span class="badge-soft-danger px-2 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_type']=='take_away')
                                    <span class="badge-soft-info px-2 rounded">{{translate('take_away')}}</span>
                                @elseif($order['order_type']=='dine_in')
                                    <span class="badge-soft-info px-2 rounded">{{translate('dine_in')}}</span>
                                @else
                                    <span class="badge-soft-success px-2 rounded">{{translate('delivery')}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-sm btn-outline-primary square-btn" href="{{route('admin.table.order.details',['id'=>$order['id']])}}">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <button type="button" href="{{route('admin.orders.generate-invoice',[$order['id']])}}" class="btn btn-sm btn-outline-success square-btn" target="_blank" onclick="print_invoice('{{$order->id}}')">
                                        <i class="tio-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
            <!-- End Table -->

            <div class="table-responsive mt-4 px-3">
                <div class="d-flex justify-content-lg-end">
                    <!-- Pagination -->
                    {!! $orders->links() !!}
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
                                value="{{translate('Proceed, If thermal printer is ready..')}}"/>
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
        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        function print_invoice(order_id) {
            $.get({
                url: '{{url('/')}}/admin/pos/invoice/'+order_id,
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
        function filter_branch_orders(id) {
            location.href = '{{url('/')}}/admin/orders/branch-filter/' + id;
        }
    </script>
@endpush
