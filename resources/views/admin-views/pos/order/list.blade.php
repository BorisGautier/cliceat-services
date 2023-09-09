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
                    {{translate('POS_Orders')}}
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
                    <form action="{{ url()->current() }}" id="form-data" method="GET">
                        <input type="hidden" name="filter">
                        <div class="row gy-3 gx-2 align-items-end">
                            <div class="col-12 pb-0">
                                <h4 class="mb-0">{{ translate('Select Date Range') }}</h4>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <select name="branch_id" class="form-control">
                                        <option value="all"
                                            {{ $branch_id == 'all'? 'selected' : '' }}
                                        >{{ translate('All Branch') }}</option>
                                    @forelse($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $branch_id == $branch->id? 'selected' : '' }}
                                        >{{ $branch->name }}</option>
                                    @empty
                                        <option>{{ translate('No Branch Found') }}</option>
                                    @endforelse

                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">Start Date</label>
                                    <input type="date" name="from" id="from_date" class="form-control" value="{{$from}}" >
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">End Date</label>
                                    <input type="date" name="to" id="to_date" class="form-control" value="{{$to}}" >
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <button type="submit" class="btn btn-primary btn-block">{{ translate('Show Data') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Filter Card -->

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
                                        {{ translate('Search') }}
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
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.pos.export-excel') }}?branch_id={{$branch_id}}&from={{$from}}&to={{$to}}&search={{$search}}">
                                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                        {{ translate('Excel') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
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
                                <th>{{translate('Order_ID')}}</th>
                                <th>{{translate('Order_Date')}}</th>
                                <th>{{translate('Customer_Info')}}</th>
                                <th>{{translate('Branch')}}</th>
                                <th>{{translate('Total_Amount')}}</th>
                                <th>{{translate('Order_Status')}}</th>
                                <th>{{translate('Order_Type')}}</th>
                                <th class="text-center">{{translate('actions')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td>{{$key+$orders->firstItem()}}</td>
                                <td>
                                    <a class="text-dark" href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                    <div>{{date("h:i A",strtotime($order['created_at']))}}</div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <h6 class="text-capitalize mb-1">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</h6>
                                        <a class="text-dark fz-12" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                    @elseif($order['user_id'] == null)
                                        <h6 class="text-capitalize text-muted">{{translate('walk_in_customer')}}</h6>
                                    @else
                                        <h6 class="text-capitalize text-muted">{{translate('Customer_Unavailable')}}</h6>
                                    @endif
                                </td>
                                <td>{{ $order->branch->name }}</td>
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
                                    @else
                                        <span class="badge-soft-danger px-2 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    <span class="badge-soft-success px-2 py-1 rounded">{{translate($order['order_type'])}}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-sm btn-outline-primary square-btn" href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-success square-btn" target="_blank" type="button"
                                                onclick="print_invoice('{{$order->id}}')"><i
                                                class="tio-print"></i>
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

            <!-- {{--
            <div class="card-footer">
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    {{--<div class="col-sm mb-2 mb-sm-0">
                        <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                            <span class="mr-2">Showing:</span>

                            <select id="datatableEntries" class="js-select2-custom"
                                    data-hs-select2-options='{
                                    "minimumResultsForSearch": "Infinity",
                                    "customClass": "custom-select custom-select-sm custom-select-borderless",
                                    "dropdownAutoWidth": true,
                                    "width": true
                                  }'>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>

                            <span class="text-secondary mr-2">of</span>

                            <span id="datatableWithPaginationInfoTotalQty"></span>
                        </div>
                    </div>--}}

                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            {!! $orders->links() !!}
                            {{--<nav id="datatablePagination" aria-label="Activity pagination"></nav>--}}
                        </div>
                    </div>
                </div>
            </div>
            --}} -->
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
        $('#from_date, #to_date').change(function () {
            let from = $('#from_date').val();
            let to = $('#to_date').val();
            if(from != ''){
                $('#to_date').attr('required','required');
            }
            if(to != ''){
                $('#from_date').attr('required','required');
            }
            if (from != '' && to != '') {
                if (from > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{\App\CentralLogics\translate('Invalid date range')}}!');
                }
            }

        })
    </script>

@endpush
