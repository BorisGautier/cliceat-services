@extends('layouts.admin.app')

@section('title', translate('Promotional campaign'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('branch wise campaign')}} - {{ $branch->name }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="col-md-12">
            <div class="card">
                <div class="card-header flex-between">
                    <div class="">
                        <h5>{{translate('Promotional campaign table')}}
                            <span style="color: red; padding: 0 .4375rem;">({{$promotions->total()}})</span>
                        </h5>
                    </div>
                    <div class="d-flex">
                        <h5 class="pr-3">{{translate('promotion_status')}} : </h5>
                            <label class="switcher">
                                <input id="31" class="switcher_input" type="checkbox"
                                       onclick="location.href='{{route('admin.promotion.status',[$branch['id'],$branch->branch_promotion_status?0:1])}}'"
                                    {{$branch->branch_promotion_status?'checked':''}}>
                                <span class="switcher_control"></span>
                            </label>
                            {{--                        <label class="toggle-switch toggle-switch-sm">--}}
                            {{--                            <input type="checkbox" class="toggle-switch-input"--}}
                            {{--                                   onclick="location.href='{{route('admin.promotion.status',[$branch['id'],$branch->branch_promotion_status?0:1])}}'"--}}
                            {{--                                   class="toggle-switch-input" {{$branch->branch_promotion_status?'checked':''}}>--}}
                            {{--                            <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>--}}
                            {{--                        </label>--}}
                    </div>
                    <div class="flex-end">
                        <div class="mx-2">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                           class="form-control"
                                           placeholder="{{translate('Search')}}" aria-label="Search"
                                           value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text"><i class="tio-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               style="width: 100%">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('SL')}}#</th>
                                <th>{{translate('Promotion type')}}</th>
                                <th>{{translate('Promotion Name')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($promotions as $k=>$promotion)
                                <tr>
                                    <th scope="row">{{$k+1}}</th>
                                    <td>
                                        @php
                                            $promotion_type = $promotion['promotion_type'];
                                            echo str_replace('_', ' ', $promotion_type);
                                        @endphp
                                    </td>
                                    <td>
                                        @if($promotion['promotion_type'] == 'video')
                                            {{$promotion['promotion_name']}}
                                        @else
                                            <div style="height: 100px; width: 100px; overflow-x: hidden;overflow-y: hidden">
                                                <img src="{{asset('storage/app/public/promotion')}}/{{$promotion['promotion_name']}}" style="width: 100px">
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{$promotions->links()}}
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script_2')
    <script>

        function readURL(input, viewer_id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+viewer_id).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg").change(function () {
            readURL(this, 'viewer');
        });

    </script>
@endpush
