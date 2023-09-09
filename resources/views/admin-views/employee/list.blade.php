@extends('layouts.admin.app')

@section('title', translate('Employee List'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                {{translate('employee_List')}}
            </span>
        </h2>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-top px-card pt-4">
                    <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                        <h5 class="d-flex gap-2">
                            {{translate('employee_table')}}
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{$em->total()}}</span>
                        </h5>

                        <div class="d-flex flex-wrap justify-content-md-end gap-3">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('Search by name, email or phone')}}" aria-label="Search" value="" required="" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">{{translate('Search')}}</button>
                                    </div>
                                </div>
                            </form>
                            <div>
                                <button type="button" class="btn btn-outline-primary text-nowrap" data-toggle="dropdown" aria-expanded="false">
                                    <i class="tio-download-to"></i>
                                    Export
                                    <i class="tio-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.employee.excel-export')}}">
                                            <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="">
                                            {{ translate('Excel') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="py-3">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('Name')}}</th>
                                    <th>{{translate('Contact_Info')}}</th>
                                    <th>{{translate('Role')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($em as $k=>$e)
                            @if($e->role)
                                <tr>
                                    <td>{{$em->firstitem()+$k}}</td>
                                    <td class="text-capitalize">
                                        <div class="media align-items-center gap-3">
                                            <div class="avatar">
                                                <img class="img-fit rounded-circle" src="{{asset('storage/app/public/admin')}}/{{$e['image']}}" alt=""
                                                     onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                                >
                                            </div>
                                            <div class="media-body">{{$e['f_name'] . ' ' . $e['l_name']}}</div>
                                        </div>
                                    </td>
                                    <td >
                                      <div><a class="text-dark" href="mailto:{{$e['email']}}"><strong>{{$e['email']}}</strong></a></div>
                                      <div><a href="tel:{{$e['phone']}}" class="text-dark">{{$e['phone']}}</a></div>
                                    </td>
                                    <td><span class="badge badge-soft-info py-1 px-2">{{$e->role['name']}}</span></td>
                                    <td>
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher_input"
                                                   onclick="location.href='{{route('admin.employee.status',[$e['id'],$e->status?0:1])}}'"
                                                   class="toggle-switch-input" {{$e->status?'checked':''}}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{route('admin.employee.update',[$e['id']])}}"
                                            class="btn btn-outline-info btn-sm square-btn"
                                            title="{{translate('Edit')}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a onclick="form_alert('employee-{{$e->id}}', '{{translate('want_to_delete_this_employee?')}}')"
                                               class="btn btn-outline-danger btn-sm delete square-btn"
                                               title="{{translate('delete')}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.employee.delete')}}" method="post" id="employee-{{$e->id}}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{$e->id}}">
                                        </form>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4 px-3">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{$em->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endpush
