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
        <div class="">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"> {{translate('Table Availability')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="mt-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group ">
                        <label class="input-label" for="exampleFormControlSelect1">{{translate('Select Branch')}}</label>
                        <select name="branch_id" class="form-control js-select2-custom" id="select_branch" required>
                            <option disabled selected>{{ translate('--Select Branch--') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{$branch['id']}}">{{$branch['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="row" id="table_list">

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

