@extends('layouts.admin.app')

@section('title', translate('Deliveryman List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Deliveryman_List')}}
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-circle fz-12">{{ $delivery_men->total() }}</span>
        </div>
        <!-- End Page Header -->


        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('Search by Name or Phone or Email')}}" aria-label="Search" value="{{$search}}" required="" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                        {{translate('Search')}}
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="d-flex flex-wrap justify-content-md-end gap-3">
                                <div>
                                    <button type="button" class="btn btn-outline-primary text-nowrap" data-toggle="dropdown" aria-expanded="false">
                                        <i class="tio-download-to"></i>
                                        Export
                                        <i class="tio-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.delivery-man.excel-export')}}">
                                                <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                                {{ translate('Excel') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                 <a href="{{route('admin.delivery-man.add')}}" class="btn btn-primary">
                                    <i class="tio-add"></i>
                                    {{translate('add_Deliveryman')}}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="py-4">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th>{{translate('name')}}</th>
                                        <th>{{translate('Contact_Info ')}}</th>
                                        <th>{{translate('Total_Orders')}}</th>
                                        <th>{{translate('Status')}}</th>
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="set-rows">
                                @foreach($delivery_men as $key=>$dm)
                                    <tr>
                                        <td>{{$delivery_men->firstitem()+$key}}</td>
                                        <td>
                                            <div class="media gap-3 align-items-center">
                                                <div class="avatar">
                                                    <img width="60" class="img-fit rounded-circle"
                                                        onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                                        src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}">
                                                    {{--<span class="d-block font-size-sm">{{$banner['image']}}</span>--}}
                                                </div>
                                                <div class="media-body">
                                                    {{$dm['f_name'].' '.$dm['l_name']}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <div>
                                                    <a class="text-dark" href="mailto:{{$dm['email']}}">
                                                        <strong>{{$dm['email']}}</strong>
                                                    </a>
                                                </div>
                                                <a class="text-dark" href="tel:{{$dm['phone']}}">{{$dm['phone']}}</a>
                                            </div>
                                        </td>
                                        <td><span class="badge fz-14 badge-soft-info px-5">{{ $dm['orders_count'] }}</span></td>
                                        <td>
                                            <label class="switcher">
                                                <input id="{{$dm['id']}}" type="checkbox" class="switcher_input" {{$dm['is_active'] == 1? 'checked' : ''}}
                                                       data-url="{{route('admin.delivery-man.ajax-is-active', ['id'=>$dm['id']])}}" onchange="status_change(this)"
                                                >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-3">
                                                <a class="btn btn-outline-info btn-sm edit square-btn"
                                                href="{{route('admin.delivery-man.edit',[$dm['id']])}}"><i class="tio-edit"></i></a>
                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn"
                                                onclick="form_alert('delivery-man-{{$dm['id']}}','{{translate('Want to remove this information ?')}}')"><i class="tio-delete"></i></button>
                                            </div>
                                            <form action="{{route('admin.delivery-man.delete',[$dm['id']])}}"
                                                method="post" id="delivery-man-{{$dm['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="table-responsive px-3 mt-3">
                            <div class="d-flex justify-content-end">
                                {!! $delivery_men->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
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
                url: '{{route('admin.delivery-man.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
