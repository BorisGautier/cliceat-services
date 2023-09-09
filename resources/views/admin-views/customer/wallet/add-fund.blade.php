@extends('layouts.admin.app')

@section('title',translate('add_fund'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{asset('/assets/admin/img/money.png')}}" alt="public">
                <span>
                    {{translate('Add_Fund')}}
                </span>
            </h2>
        </div>

        <!-- End Page Header -->
        <div class="card gx-2 gx-lg-3">
            <div class="card-body">
{{--                <form action="{{route('admin.customer.wallet.add-fund-store')}}" method="post" id="add_fund">--}}
                <form action="javascript:" method="post" id="add_fund">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="form-label" for="customer">{{translate('customer')}}</label>
                                <select id='customer' name="customer_id" data-placeholder="{{translate('Select_Customer')}}" class="js-data-example-ajax form-control h--45px" required>

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="form-label" for="amount">{{translate('amount')}}</label>
                                <input type="number" class="form-control h--45px" name="amount" id="amount" step=".01" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="referance">{{translate('reference')}} <small>({{translate('optional')}})</small></label>
                                <input type="text" class="form-control h--45px" name="referance" id="referance">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" id="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
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

        $('#add_fund').on('submit', function (e) {

            e.preventDefault();
            var formData = new FormData(this);
            console.log(formData);

            Swal.fire({
                title: '{{translate('are_you_sure')}}',
                text: '{{translate('add_fund ')}}'+$('#amount').val()+' {{\App\CentralLogics\Helpers::currency_code().' '.translate('to')}} '+$('#customer option:selected').text()+'{{translate('wallet')}}',
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{translate('no')}}',
                confirmButtonText: '{{translate('add')}}',
                reverseButtons: true
            }).then((result) => {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                if (result.value) {
                    $.post({
                        url: '{{route('admin.customer.wallet.add-fund-store')}}',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                $('#customer').val(null).trigger('change');
                                $('#amount').val(null).trigger('change');
                                $('#referance').val(null).trigger('change');
                                toastr.success('{{__("Fund added successfully")}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        }
                    });
                }
            })
        })

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.customer.select-list')}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#reset').click(function(){
            $('#customer').val(null).trigger('change');
        })
    </script>
@endpush
