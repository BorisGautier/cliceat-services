<!-- Header -->
<div class="card-header d-flex justify-content-between gap-10">
    <h5 class="mb-0">{{translate('Top_Customer')}}</h5>
    <a href="{{route('admin.customer.list')}}" class="btn-link">{{translate('View_All')}}</a>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="d-flex flex-column gap-3">
        @foreach($top_customer as $key=>$item)
            @if(isset($item->customer))
                <a class="d-flex justify-content-between align-items-center text-dark" href='{{route('admin.customer.view', [$item['user_id']])}}'>
                    <div class="media align-items-center gap-3">
                        <img class="rounded avatar avatar-lg"
                                onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"
                                src="{{asset('storage/app/public/profile'.'/'. $item->customer->image  ?? '' )}}">
                        <div class="media-body d-flex flex-column" style="margin-right: 60px">
                            <span class="font-weight-semibold text-capitalize">{{$item->customer['f_name']??'Not exist'}}</span>
                            <span class="text-dark">{{ $item->customer['phone']?? translate('Not exist') }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-1 badge-soft-c1 font-weight-bold fz-12 rounded lh-1">{{translate('Orders :')}}{{$item['count']}}</span>
                </a>
            @endif
        @endforeach
    </div>
</div>
<!-- End Body -->
