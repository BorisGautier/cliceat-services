@extends('layouts.admin.app')

@section('title', translate('Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
                <span class="page-header-title">
                    {{$dm['f_name'].' '.$dm['f_name']}}
                </span>
            </h2>

            <a href="{{url()->previous()}}" class="btn btn-primary">
                <i class="tio-back-ui"></i> {{translate('back')}}
            </a>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card mb-3 mb-lg-5">
            <!-- Body -->
            <div class="card-body">
                <div class="row align-items-md-center g-3">
                    <div class="col-md-5 d-flex justify-content-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xxl mr-4">
                                <img class="img-fit"
                                onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}"
                                alt="Image Description">
                            </div>
                            <div class="d-block">
                                <h4 class="display-2 text-dark mb-0"><span class="c1">{{count($dm->rating)>0?number_format($dm->rating[0]->average, 1, '.', ' '):0}}</span><span class="text-muted" style="font-size: 42px;">/5</span></h4>
                                <p> {{$dm->reviews->count()}} {{translate('reviews')}}
                                    <span class="badge badge-soft-dark badge-pill ml-1"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <ul class="list-unstyled list-unstyled-py-2 mb-0">

                        @php($total=$dm->reviews->count())
                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],5))
                                <span class="progress-name">{{translate('Excellent')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($five/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$five}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($four=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],4))
                                <span class="progress-name">{{translate('Good')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($four/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$four}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($three=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],3))
                                <span class="progress-name">{{translate('Average')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($three/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$three}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($two=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],2))
                                <span class="progress-name">{{translate('Below_Average')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($two/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$two}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($one=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],1))
                                <span class="progress-name">{{translate('Poor')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($one/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$one}}</span>
                            </li>
                            <!-- End Review Ratings -->
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Body -->
        </div>
        <!-- End Card -->

        <!-- Card -->
        <div class="card">
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                       data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0, 3, 6],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('reviewer')}}</th>
                            <th>{{translate('review')}}</th>
                            {{-- <th>{{translate('attachment')}}</th> --}}
                            <th>{{translate('date')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                @if($review->customer)
                                    <a class="d-flex align-items-center"
                                       href="{{route('admin.customer.view',[$review['user_id']])}}">
                                        <div class="avatar avatar-circle">
                                            <img class="avatar-img" width="75" height="75"
                                                 onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                                 src="{{asset('storage/app/public/profile/'.$review->customer->image)}}"
                                                 alt="Image Description">
                                        </div>
                                        <div class="ml-3">
                                        <span class="d-block h5 text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}} <i
                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                title="Verified Customer"></i></span>
                                            <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        </div>
                                    </a>
                                @else
                                    <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                        {{translate('Customer unavailable')}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap">
                                    <div class="d-flex">
                                        <label class="badge badge-soft-info">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </label>
                                    </div>
                                    <div class="max-w300">
                                        <div class="d-block text-break text-dark __descripiton-txt __not-first-hidden">
                                            <div>
                                                <p>
                                                    {!! $review['comment'] !!}
                                                </p>
                                            </div>
                                            <div class="show-more text-info text-center">
                                                <span class="">See More</span>
                                            </div>
                                        </div>
{{--                                        {{$review['comment']}}--}}
                                    </div>
                                </div>
                            </td>
                            {{-- <td>
                                @foreach(json_decode($review['attachment'],true) as $attachment)
                                    <img width="100" onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'" src="{{asset('storage/app/public')}}/{{$attachment}}">
                                @endforeach
                            </td> --}}
                            <td>
                                {{date('d M Y',strtotime($review['created_at']))}} {{ date(config('time_format'), strtotime($review['created_at'])) }}
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
                    {!! $reviews->links() !!}
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        $('.show-more span').on('click', function(){
            $('.__descripiton-txt').toggleClass('__not-first-hidden')
            if($(this).hasClass('active')) {
                $('.show-more span').text('{{translate('See More')}}')
                $(this).removeClass('active')
            }else {
                $('.show-more span').text('{{translate('See Less')}}')
                $(this).addClass('active')
            }
        })
    </script>
@endpush
