@extends('layouts.admin.app')

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
                    <form action="#" id="form-data" method="GET">
                        <div class="row gy-3 gx-2 align-items-end">
                            <div class="col-12 pb-0">
                                <h4 class="mb-0">{{translate('select_date_range')}}</h4>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label for="select_branch">{{translate('Select_Branch')}}</label>
                                <!-- Select -->
                                <select class="form-control" name="branch" onchange="filter_branch_orders(this.value)" id="select_branch">
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
                                    <input type="date" name="from" value="{{ session()->get('') }}" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">{{ translate('End Date') }}</label>
                                    <input type="date" value="" name="to" id="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 d-flex gap-2">
                                <button type="clear" class="btn btn-secondary flex-grow-1">{{ translate('Clear') }}</button>
                                <button type="submit" class="btn btn-primary text-nowrap flex-grow-1" onclick="formUrlChange(this)">{{ translate('Show Data') }}</button>
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'pending'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/pending.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('Pending')}}</span>
                                    </h6>
                                    <span class="card-title text-0661CB">
                                    {{$order_count['pending']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'confirmed'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'processing'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'out_for_delivery'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'delivered'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'canceled'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'returned'])}}">
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
                            <a class="order--card h-100" href="{{route('admin.orders.list', ['status' => 'failed'])}}">
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

            <!-- Card Top -->
            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search by Order ID, Order Status or Transaction Reference')}}" aria-label="Search"
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
                                {{translate('export')}}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.orders.export-excel', ['search'=>$search, 'from' =>$from, 'to' => $to, 'status'=> $status])}}">
                                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                        {{ translate('excel') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Card Top -->

            <!-- Table -->
            <div class="py-4">
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('Order_ID')}}</th>
                                <th>{{translate('Delivery_Date')}}</th>
                                <th>{{translate('Customer_Info')}}</th>
                                <th>{{translate('branch')}}</th>
                                <th>{{translate('Total_Amout')}}</th>
                                <th>{{translate('Order_Status')}}</th>
                                <th>{{translate('Order_Type')}}</th>
                                <th class="text-center">{{translate('actions')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td>{{$orders->firstitem()+$key}}</td>
                                <td>
                                    <a class="text-dark" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['delivery_date']))}}</div>
                                    <div>{{date('h:i A',strtotime($order['delivery_time']))}}</div>
                                </td>
                                <td>
                                    @if($order->is_guest == 0)
                                        @if($order->customer)
                                            <h6 class="text-capitalize mb-1">
                                                <a class="text-dark" href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                            </h6>
                                            <a class="text-dark fz-12" href="tel:{{$order->customer->phone}}">{{$order->customer->phone}}</a>
                                        @else
                                            <span class="text-capitalize text-muted">
                                            {{translate('Customer_Unavailable')}}
                                            </span>
                                        @endif
                                    @else
                                        <h6 class="text-capitalize text-info">
                                            {{translate('Guest Customer')}}
                                        </h6>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-soft-info px-2 py-1 rounded">{{$order->branch?$order->branch->name:'Branch deleted!'}}</span>
                                </td>
                                <td>
                                    <div>{{ \App\CentralLogics\Helpers::set_symbol($order['order_amount'] + $order['delivery_charge']) }}</div>
                                    @if($order->payment_status=='paid')
                                        <span class="text-success">{{translate('paid')}}</span>
                                    @else
                                        <span class="text-danger">{{translate('unpaid')}}</span>
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
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge-soft-danger px-2 py-1 rounded">{{translate("failed_to_deliver")}}</span>
                                    @else
                                        <span class="badge-soft-danger px-2 py-1 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-soft-success px-2 py-1 rounded">{{translate($order['order_type'])}}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-sm btn-outline-primary square-btn" href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a href="{{route('admin.orders.generate-invoice',[$order['id']])}}" class="btn btn-sm btn-outline-success square-btn" target="_blank">
                                            <i class="tio-print"></i>
                                        </a>
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
                    {!!$orders->links()!!}
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        function filter_branch_orders(id) {
            location.href = '{{url('/')}}/admin/orders/branch-filter/' + id;
        }
    </script>

    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.orders.search')}}',
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
@endpush
