@extends('layouts.admin.app')

@section('title', translate('Add new attribute'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/attribute.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Add_New_Attribute')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->


        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                            <h5 class="d-flex align-items-center gap-2 mb-0">
                                {{translate('Attribute_Table')}}
                                <span class="badge badge-soft-dark rounded-50 fz-12">{{ $attributes->total() }}</span>
                            </h5>

                            <div class="d-flex flex-wrap justify-content-md-end gap-3">
                                <form action="#" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('search_by_name')}}" aria-label="Search" value="{{ $search }}" required="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                                        </div>
                                    </div>
                                </form>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#adAttributeModal">
                                    <i class="tio-add"></i>
                                    {{translate('Add_Attribute')}}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="py-4">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th>{{translate('name')}}</th>
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($attributes as $key=>$attribute)
                                    <tr>
                                        <td>{{$attributes->firstitem()+$key}}</td>
                                        <td>
                                            <div>
                                                {{$attribute['name']}}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Dropdown -->
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm edit square-btn"
                                                   href="{{route('admin.attribute.edit',[$attribute['id']])}}"><i class="tio-edit"></i></a>
                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn"
                                                        onclick="form_alert('attribute-{{$attribute['id']}}','{{translate('Want to delete this attribute ?')}}')"><i class="tio-delete"></i>
                                                </button>
                                            </div>
                                            <form action="{{route('admin.attribute.delete',[$attribute['id']])}}"
                                                method="post" id="attribute-{{$attribute['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {!! $attributes->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="adAttributeModal" tabindex="-1" role="dialog" aria-labelledby="adAttributeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <form action="{{route('admin.attribute.store')}}" method="post">
                        @csrf
                        @php($data = Helpers::get_business_settings('language'))
                        @php($default_lang = Helpers::get_default_language())

                        @if($data && array_key_exists('code', $data[0]))
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($data as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{$lang['default'] == true ? 'active':''}}" href="#" id="{{$lang['code']}}-link">
                                            {{ Helpers::get_language_name($lang['code']).'('.strtoupper($lang['code']).')' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-12">
                                    @foreach($data as $lang)
                                        <div class="form-group lang_form {{$lang['default'] == false ? 'd-none':''}}" id="{{$lang['code']}}-form">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('name')}} ({{strtoupper($lang['code'])}})</label>
                                            <input type="text" name="name[]" class="form-control"
                                                placeholder="{{translate('New attribute')}}"
                                                {{$lang['status'] == true ? 'required':''}} maxlength="255"
                                                @if($lang['status'] == true) oninvalid="document.getElementById('{{$lang['code']}}-link').click()" @endif>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group lang_form" id="{{$default_lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('name')}} ({{strtoupper($default_lang)}})</label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('New attribute')}}" maxlength="255">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$default_lang}}">
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();

            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            //console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>

    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush

