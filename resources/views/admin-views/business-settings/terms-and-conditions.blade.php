@extends('layouts.admin.app')

@section('title', translate('Terms and Conditions'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/pages.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('page_setup')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <!-- Inline Menu -->
        @include('admin-views.business-settings.partials._page-setup-inline-menu')


        <div class="row g-2">
            <div class="col-12">
                <form action="{{route('admin.business-settings.page-setup.terms-and-conditions')}}" method="post" id="tnc-form">
                    @csrf
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="tnc">{!! $tnc->value !!}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3 align-items-center">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
@endpush
