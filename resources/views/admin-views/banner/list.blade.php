@extends('layouts.admin.app')

@section('title', translate('Banner list'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/banner.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Banner_Setup')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->


        <div class="row g-2">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder="{{translate('New banner')}}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="input-label">{{translate('item_Type')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="item_type" class="custom-select js-select2-custom" onchange="show_item(this.value)">
                                            <option selected disabled>{{translate('select_item_type')}}</option>
                                            <option value="product">{{translate('product')}}</option>
                                            <option value="category">{{translate('category')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="type-product">
                                        <label class="input-label">{{translate('product')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="product_id" class="custom-select js-select2-custom">
                                            <option selected disabled>{{translate('select_a_product')}}</option>
                                            @foreach(\App\Model\Product::all() as $product)
                                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="type-category" style="display: none">
                                        <label class="input-label">{{translate('category')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="custom-select js-select2-custom">
                                            <option selected disabled>{{translate('select_a_category')}}</option>
                                            @foreach(\App\Model\Category::where('parent_id', 0)->get() as $category)
                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <label class="mb-0">{{translate('banner_Image')}}</label>
                                            <small class="text-danger">* ( {{translate('ratio 3:1')}} )</small>
                                        </div>
                                        <div class="d-flex justify-content-center mt-4">
                                            <div class="upload-file">
                                                <input type="file" name="image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" class="upload-file__input">
                                                <div class="upload-file__img_drag upload-file__img">
                                                    <img width="465" id="viewer" src="{{asset('assets/admin/img/icons/upload_img2.png')}}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-12">
                <!-- Card -->
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row align-items-center gy-2">
                            <div class="col-sm-4 col-md-6 col-lg-8">
                                <h5 class="d-flex align-items-center gap-2 mb-0">
                                    {{translate('Banner_List')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12">{{ $banners->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('Search_by_Title')}}" aria-label="Search" value="" required="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                {{translate('Search')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
                                    <th>{{translate('Banner_Image')}}</th>
                                    <th>{{translate('Title')}}</th>
                                    <th>{{translate('Banner_Type')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($banners as $key=>$banner)
                                    <tr>
                                        <td>{{$banners->firstitem()+$key}}</td>
                                        <td>
                                            <img class="img-vertical-150" src="{{asset('storage/app/public/banner')}}/{{$banner['image']}}"
                                                onerror="this.src='{{asset('assets/admin/img/900x400/img1.jpg')}}'">
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$banner['title']}}
                                            </div>
                                        </td>
                                        @if(isset($banner->category_id))
                                            <td>{{translate('category')}}: {{substr(\App\Model\Category::find($banner->category_id)?->name, 0, 15)}}</td>
                                        @elseif(isset($banner->product_id))
                                            <td>{{translate('product')}}: {{ substr(\App\Model\Product::find($banner->product_id)?->name,0, 15) }}...</td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>
                                            <label class="switcher">
                                                <input class="switcher_input" type="checkbox" {{$banner['status']==1 ? 'checked' : ''}} id="{{$banner['id']}}"
                                                    data-url="{{route('admin.banner.status',[$banner['id'],0])}}" onchange="status_change(this)"
                                                >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm edit square-btn"
                                                    href="{{route('admin.banner.edit',[$banner['id']])}}"><i class="tio-edit"></i></a>
                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn"
                                                    onclick="form_alert('banner-{{$banner['id']}}','{{translate('Want to delete this banner')}}')"><i class="tio-delete"></i></button>
                                            </div>
                                            <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                                method="post" id="banner-{{$banner['id']}}">
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
                                {!! $banners->links() !!}
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });


        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    </script>

    <script>
        $(".js-select2-custom").select2({
            placeholder: "Select a state",
            allowClear: true
        });
    </script>
@endpush
