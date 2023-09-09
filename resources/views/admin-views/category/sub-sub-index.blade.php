@extends('layouts.admin.app')

@section('title', translate('Add new sub sub category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('Sub Sub Category')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.category.store')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlSelect1">{{translate('Sub Categories')}}
                            <span class="input-label-secondary">*</span></label>
                        <select id="exampleFormControlSelect1" name="parent_id" class="form-control" required>
                            @foreach(\App\Model\Category::where(['position'=>1])->get() as $category)
                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('Name')}}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{translate('New category')}}" required>
                    </div>
                    <input name="position" value="2" style="display: none">
                    <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
                </form>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <hr>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title"></h5>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#sl')}}</th>
                                <th style="width: 50%">{{translate('Sub Category')}}</th>
                                <th style="width: 50%">{{translate('Sub Sub Category')}}</th>
                                <th style="width: 20%">{{translate('Status')}}</th>
                                <th style="width: 10%">{{translate('Action')}}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" id="column1_search" class="form-control form-control-sm"
                                           placeholder="{{translate('Search sub category')}}">
                                </th>

                                <th>
                                    <input type="text" id="column2_search" class="form-control form-control-sm"
                                           placeholder="{{translate('Search sub sub category')}}">
                                </th>

                                <th>
                                    <select id="column3_search" class="js-select2-custom"
                                            data-hs-select2-options='{
                                              "minimumResultsForSearch": "Infinity",
                                              "customClass": "custom-select custom-select-sm text-capitalize"
                                            }'>
                                        <option value="">{{translate('Any')}}</option>
                                        <option value="Active">{{translate('Active')}}</option>
                                        <option value="Disabled">{{translate('Disabled')}}</option>
                                    </select>
                                </th>
                                <th>
                                    {{--<input type="text" id="column4_search" class="form-control form-control-sm"
                                           placeholder="Search countries">--}}
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach(\App\Model\Category::with(['parent'])->where(['position'=>2])->latest()->get() as $key=>$category)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$category->parent_id!=0?$category->parent['name']:''}}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$category['name']}}
                                        </span>
                                    </td>

                                    <td>
                                        @if($category['status']==1)
                                            <div style="padding: 10px;border: 1px solid;cursor: pointer"
                                                 onclick="location.href='{{route('admin.category.status',[$category['id'],0])}}'">
                                                <span class="legend-indicator bg-success"></span>{{translate('Active')}}
                                            </div>
                                        @else
                                            <div style="padding: 10px;border: 1px solid;cursor: pointer"
                                                 onclick="location.href='{{route('admin.category.status',[$category['id'],1])}}'">
                                                <span class="legend-indicator bg-danger"></span>{{translate('Disabled')}}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('admin.category.edit',[$category['id']])}}">Edit</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="$('#category-{{$category['id']}}').submit()">Delete</a>
                                                <form action="{{route('admin.category.delete',[$category['id']])}}"
                                                      method="post" id="category-{{$category['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')


@endpush
