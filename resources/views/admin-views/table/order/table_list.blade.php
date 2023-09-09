@extends('layouts.admin.app')

@section('title', translate('branch table'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid py-5">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
{{--                    <h1 class="page-header-title">{{$branch->name}} - {{translate('Table List')}}</h1>--}}
                </div>
            </div>
        </div>
        <div class="row">
            @if($tables != null)
                @foreach($tables as $table)
                    <div class="col-md-3 mb-5">
                        <div class="card py-4">
                            <div class="card-body text-center {{ $table['order'] != null ? 'bg-danger' : 'bg-gray'}}">
                                <h5 class="card-title">{{ translate('table') }}</h5>
                                <h5 class="card-title">{{ $table['number'] }}</h5>
                                <h5 class="card-title">{{ translate('capacity') }}: {{ $table['capacity'] }}</h5>
                                <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#demo_{{$table->id}}">Show Orders</button>
                            </div>
                            <div class="text-center">
                                <div id="demo_{{$table->id}}" class="collapse px-2" >
                                    @if($table->order)
                                        @foreach($table['order'] as $order)
                                            <h5>{{ translate('order id') }}: {{ $order['id'] }}</h5>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

@endsection

@push('script_2')
    <script>

    </script>


@endpush


