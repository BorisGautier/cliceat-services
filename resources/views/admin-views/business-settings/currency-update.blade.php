@extends('layouts.admin.app')

@section('title', translate('Currency'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('Update Currency')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.currency-update',[$currency['id']])}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <div class="form-group mb-2">
                        <label style="padding-left: 10px">{{translate('Country Name')}}</label><br>
                        <input type="text" placeholder="{{translate('ex : Bangladesh')}}" value="{{$currency['country']}}" class="form-control" name="country">
                    </div>

                    <div class="form-group mb-2">
                        <label style="padding-left: 10px">{{translate('Code')}}</label><br>
                        <input type="text" placeholder="{{translate('ex : USD')}}" value="{{$currency['currency_code']}}" class="form-control" name="currency_code">
                    </div>

                    <div class="form-group mb-2">
                        <label style="padding-left: 10px">{{translate('Symbol')}}</label><br>
                        <input type="text" placeholder="{{translate('ex : $')}}" value="{{$currency['currency_symbol']}}" class="form-control" name="symbol">
                    </div>

                    <div class="form-group mb-2">
                        <label style="padding-left: 10px">{{translate('Exchange Rate ( 1 USD ) with USD')}}</label><br>
                        <input type="number" placeholder="{{translate('ex : 1')}}" value="{{$currency['exchange_rate']}}" class="form-control" name="exchange_rate">
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">{{translate('Update')}}</button>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
