@extends('layouts.admin.app')

@section('title', translate('Driver Report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/takeaway.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Deliveryman_Report')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->


        <div class="card">
            <div class="card-body">
                <div class="media flex-column flex-sm-row flex-wrap align-items-sm-center gap-4">
                    <!-- Avatar -->
                    <div class="avatar avatar-xl">
                        <img class="avatar-img" src="{{asset('assets/admin')}}/svg/illustrations/order.png"
                                alt="Image Description">
                    </div>
                    <!-- End Avatar -->

                    <div class="media-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="">
                                <h2 class="page-header-title">{{translate('deliveryman_Report_Overview')}}</h2>

                                <div class="">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>
                            </div>

                            <div class="d-flex">
                                <a class="btn btn-icon btn-primary rounded-circle px-2" href="{{route('admin.dashboard')}}">
                                    <i class="tio-home-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <!-- Header -->
            <div class="card-header border-0 pb-0">
                <div class="w-100">
                    <form action="javascript:" id="search-form">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12">
                                <h4 class="form-label mb-0">{{translate('Show Data by Date Range')}}</h4>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="mb-3">
                                    <select class="form-control" name="delivery_man_id"
                                            id="delivery_man">
                                        <option
                                            value="0">{{translate('select Deliveryman')}}</option>
                                        @foreach(\App\Model\DeliveryMan::all() as $deliveryMan)
                                            <option
                                                value="{{$deliveryMan['id']}}">
                                                {{$deliveryMan['f_name'].' '.$deliveryMan['l_name']}}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <input type="date" name="from" id="from_date"
                                            class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <input type="date" name="to" id="to_date"
                                            class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <div class="mb-3">
                                    <button type="submit"
                                            class="btn btn-primary btn-block">{{translate('show')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3">
                        <strong>
                            {{translate('Total Delivered QTY')}} :
                            <span id="delivered_qty"></span>
                        </strong>
                    </div>
                </div>
            </div>
            <!-- End Header -->
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom py-3">
                    <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    {{translate('SL')}}
                                </th>
                                <th>{{translate('order')}}</th>
                                <th>{{translate('date')}}</th>
                                <th>{{translate('customer')}}</th>
                                <th>{{translate('branch')}}</th>
                                {{-- <th>{{translate('payment')}} {{translate('status')}}</th> --}}
                                <th>{{translate('total')}}</th>
                                <th>{{translate('order')}} {{translate('status')}}</th>
                                <th>{{translate('actions')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">

                        </tbody>
                    </table>
                </div>
                <div class="table-responsive mt-4 px-3">
                    <div class="d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {{--{!! $orders->links() !!}--}}
                    </div>
                </div>
            </div>
        </div>
            @endsection

            @push('script')

            @endpush

            @push('script_2')

            <script src="{{asset('assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
            <script
                src="{{asset('assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
            <script src="{{asset('assets/admin')}}/js/hs.chartjs-matrix.js"></script>

            <script>
                $(document).on('ready', function () {

                    // INITIALIZATION OF FLATPICKR
                    // =======================================================
                    $('.js-flatpickr').each(function () {
                        $.HSCore.components.HSFlatpickr.init($(this));
                    });


                    // INITIALIZATION OF NAV SCROLLER
                    // =======================================================
                    $('.js-nav-scroller').each(function () {
                        new HsNavScroller($(this)).init()
                    });


                    // INITIALIZATION OF DATERANGEPICKER
                    // =======================================================
                    $('.js-daterangepicker').daterangepicker();

                    $('.js-daterangepicker-times').daterangepicker({
                        timePicker: true,
                        startDate: moment().startOf('hour'),
                        endDate: moment().startOf('hour').add(32, 'hour'),
                        locale: {
                            format: 'M/DD hh:mm A'
                        }
                    });

                    var start = moment();
                    var end = moment();

                    function cb(start, end) {
                        $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
                    }

                    $('#js-daterangepicker-predefined').daterangepicker({
                        startDate: start,
                        endDate: end,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        }
                    }, cb);

                    cb(start, end);


                    // INITIALIZATION OF CHARTJS
                    // =======================================================
                    $('.js-chart').each(function () {
                        $.HSCore.components.HSChartJS.init($(this));
                    });

                    var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

                    // Call when tab is clicked
                    $('[data-toggle="chart"]').click(function (e) {
                        let keyDataset = $(e.currentTarget).attr('data-datasets')

                        // Update datasets for chart
                        updatingChart.data.datasets.forEach(function (dataset, key) {
                            dataset.data = updatingChartDatasets[keyDataset][key];
                        });
                        updatingChart.update();
                    })


                    // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
                    // =======================================================
                    function generateHoursData() {
                        var data = [];
                        var dt = moment().subtract(365, 'days').startOf('day');
                        var end = moment().startOf('day');
                        while (dt <= end) {
                            data.push({
                                x: dt.format('YYYY-MM-DD'),
                                y: dt.format('e'),
                                d: dt.format('YYYY-MM-DD'),
                                v: Math.random() * 24
                            });
                            dt = dt.add(1, 'day');
                        }
                        return data;
                    }

                    $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                        data: {
                            datasets: [{
                                label: 'Commits',
                                data: generateHoursData(),
                                width: function (ctx) {
                                    var a = ctx.chart.chartArea;
                                    return (a.right - a.left) / 70;
                                },
                                height: function (ctx) {
                                    var a = ctx.chart.chartArea;
                                    return (a.bottom - a.top) / 10;
                                }
                            }]
                        },
                        options: {
                            tooltips: {
                                callbacks: {
                                    title: function () {
                                        return '';
                                    },
                                    label: function (item, data) {
                                        var v = data.datasets[item.datasetIndex].data[item.index];

                                        if (v.v.toFixed() > 0) {
                                            return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                        } else {
                                            return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                        }
                                    }
                                }
                            },
                            scales: {
                                xAxes: [{
                                    position: 'bottom',
                                    type: 'time',
                                    offset: true,
                                    time: {
                                        unit: 'week',
                                        round: 'week',
                                        displayFormats: {
                                            week: 'MMM'
                                        }
                                    },
                                    ticks: {
                                        "labelOffset": 20,
                                        "maxRotation": 0,
                                        "minRotation": 0,
                                        "fontSize": 12,
                                        "fontColor": "rgba(22, 52, 90, 0.5)",
                                        "maxTicksLimit": 12,
                                    },
                                    gridLines: {
                                        display: false
                                    }
                                }],
                                yAxes: [{
                                    type: 'time',
                                    offset: true,
                                    time: {
                                        unit: 'day',
                                        parser: 'e',
                                        displayFormats: {
                                            day: 'ddd'
                                        }
                                    },
                                    ticks: {
                                        "fontSize": 12,
                                        "fontColor": "rgba(22, 52, 90, 0.5)",
                                        "maxTicksLimit": 2,
                                    },
                                    gridLines: {
                                        display: false
                                    }
                                }]
                            }
                        }
                    });


                    // INITIALIZATION OF CLIPBOARD
                    // =======================================================
                    $('.js-clipboard').each(function () {
                        var clipboard = $.HSCore.components.HSClipboard.init(this);
                    });


                    // INITIALIZATION OF CIRCLES
                    // =======================================================
                    $('.js-circle').each(function () {
                        var circle = $.HSCore.components.HSCircles.init($(this));
                    });
                });
            </script>

            <script>


                $('#search-form').on('submit', function () {
                    let formDate = $('#from_date').val();
                    let toDate = $('#to_date').val();
                    let delivery_man = $('#delivery_man').val();
                    $.post({
                        url: "{{route('admin.report.deliveryman_filter')}}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'formDate': formDate,
                            'toDate': toDate,
                            'delivery_man': delivery_man,
                        },

                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            console.log(data.delivered_qty)
                            $('#set-rows').html(data.view);
                            $('#delivered_qty').html(data.delivered_qty);
                            $('.card-footer').hide();
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                });

            </script>
            <script>
                $('#from_date,#to_date').change(function () {
                    let fr = $('#from_date').val();
                    let to = $('#to_date').val();
                    if (fr != '' && to != '') {
                        if (fr > to) {
                            $('#from_date').val('');
                            $('#to_date').val('');
                            toastr.error('{{\App\CentralLogics\translate("Invalid date range!")}}', Error, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }

                });


            </script>
    @endpush
