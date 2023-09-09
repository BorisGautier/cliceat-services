@extends('layouts.admin.app')

@section('title', translate('Table Availability'))

@push('css_or_js')
<style>
    .bg-gray{
        background: #e4e4e4;
    }
    .bg-c1 {
        background-color: #FF6767 !important;
    }
    .c1 {
        color: #FF6767 !important;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/table.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Table_Availability')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <div class="card card-body">
            <div class="d-flex gap-3 flex-wrap align-items-center justify-content-between mb-4">
{{--                next release--}}
{{--                <div class="inline-page-menu">--}}
{{--                    <ul class="list-unstyled">--}}
{{--                        <li class="active"><a href="#">All</a></li>--}}
{{--                        <li><a href="#">Scheduled</a></li>--}}
{{--                        <li><a href="#">Currently Busy</a></li>--}}
{{--                    </ul>--}}
{{--                </div>--}}

                <select name="branch_id" class="custom-select max-w220" id="select_branch" required>
                    <option value="" selected disabled>{{ translate('--Select_Branch--') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{$branch['id']}}">{{$branch['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="table_box_list justify-content-center gap-2 gap-md-3" id="table_list">

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function (){
            $('#select_branch').on('change', function (){
                var branch = this.value;
                console.log(branch);
                $('#table_list').html('');
                $('#table_title').html('');
                $.ajax({
                    url: "{{ url('admin/table/branch-table') }}",
                    type: "POST",
                    data: {
                        branch_id : branch,
                        _token : '{{ csrf_token() }}',
                    },
                    dataType : 'json',
                    success: function (result){
                        $('#table_list').html(result.view);
                    },
                });
            });
        });
    </script>
@endpush


