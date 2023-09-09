@extends('layouts.admin.app')

@section('title', translate('Employee Edit'))

@push('css_or_js')
    <link href="{{asset('assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                {{translate('Update_Employee')}}
            </span>
        </h2>
    </div>
    <!-- End Page Header -->

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin.employee.update',[$e['id']])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> {{translate('general_Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{translate('Name')}}</label>
                                    <input type="text" name="name" value="{{$e['f_name'] . ' ' . $e['l_name']}}" class="form-control" id="name"
                                        placeholder="{{translate('Ex')}} : {{translate('Md. Al Imrun')}}">
                                </div>
                                <div class="form-group">
                                    <label for="phone">{{translate('Phone')}}</label>
                                    <input type="tel" value="{{$e['phone']}}" required name="phone" class="form-control" id="phone"
                                        placeholder="{{translate('Ex')}} : +88017********">
                                </div>
                                <div class="form-group">
                                    <label for="name">{{translate('Role')}}</label>
                                    <select class="custom-select" name="role_id">
                                            <option value="0" selected disabled>---{{translate('select_Role')}}---</option>
                                            @foreach($rls as $r)
                                                <option
                                                    value="{{$r->id}}" {{$r['id']==$e['admin_role_id']?'selected':''}}>{{translate($r->name)}}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="identity_type">{{translate('Identity Type')}}</label>
                                    <select class="custom-select" name="identity_type" id="identity_type">
                                        <option value="passport" {{$e->identity_type == 'passport'? 'selected' : ''}}>{{translate('passport')}}</option>
                                        <option value="driving_license" {{$e->identity_type == 'driving_license'? 'selected' : ''}}>{{translate('driving_License')}}</option>
                                        <option value="nid" {{$e->identity_type == 'nid'? 'selected' : ''}}>{{translate('NID')}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="identity_number">{{translate('identity_Number')}}</label>
                                    <input type="text" name="identity_number" class="form-control" id="identity_number" required value="{{$e->identity_number}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="text-center mb-3">
                                        <img width="180" class="rounded-10 border" id="viewer"
                                        onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"
                                        src="{{asset('storage/app/public/admin')}}/{{$e['image']}}" alt="Employee thumbnail"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{translate('employee_image')}}</label>
                                        <span class="text-danger">( {{translate('ratio')}} 1:1 )</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileUpload">{{translate('choose')}} {{translate('file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="input-label">{{translate('identity_Image')}}</label>
                                    <div>
                                        <div class="row" id="coba"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> {{translate('account_Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">{{translate('Email')}}</label>
                                    <input type="email" value="{{$e['email']}}" name="email" class="form-control" id="email"
                                        placeholder="{{translate('Ex')}} : ex@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">{{translate('Password')}}</label>
                                    <small> ( {{translate('input if you want to change')}} )</small>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password"
                                               placeholder="{{translate('Ex: 8+ Characters')}}"
                                               data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                        <div id="changePassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changePassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="confirm_password">{{translate('confirm_Password')}}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="confirm_password" class="js-toggle-password form-control form-control input-field"
                                               id="confirm_password" placeholder="{{translate('confirm password')}}"
                                               data-hs-toggle-password-options='{
                                                "target": "#changeConPassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changeConPassIcon"
                                                }'>
                                        <div id="changeConPassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changeConPassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>

    <!--modal-->
    @include('admin-views.employee.partials.image-process._image-crop-modal',['modal_id'=>'employee-image-modal'])
    <!--modal-->
</div>
@endsection

@push('script_2')
    <script src="{{asset('assets/back-end')}}/js/select2.min.js"></script>
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

        $("#customFileUpload").change(function () {
            readURL(this);
        });


        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    @include('admin-views.employee.partials.image-process._script',[
   'id'=>'employee-image-modal',
   'height'=>200,
   'width'=>200,
   'multi_image'=>false,
   'route'=>null
   ])

    <script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '230px',
                groupClassName: 'col-6 col-lg-4 ',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
@endpush
