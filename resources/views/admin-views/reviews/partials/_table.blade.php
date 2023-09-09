@foreach($reviews as $key=>$review)
    <tr>
        <td>{{++$key}}</td>
        <td>
            <div>
                @if($review->product)
                    <a class="text-dark media align-items-center gap-2" href="{{route('admin.product.view',[$review['product_id']])}}">
                        <div class="avatar">
                            <img class="rounded-circle img-fit" src="{{asset('storage/app/public/product')}}/{{$review->product['image']}}" alt=""
                                 onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'">
                        </div>
                        <span class="media-body max-w220 text-wrap">{{$review->product['name']}}</span>
                    </a>
                @else
                    <span class="badge-pill badge-soft-dark text-muted small">
                                                        {{translate('Product unavailable')}}
                                                    </span>
                @endif
            </div>
        </td>
        <td>
            @if($review->customer)
                <div class="d-flex flex-column gap-1">
                    <a class="text-dark" href="{{route('admin.customer.view',[$review->user_id])}}">
                        {{$review->customer->f_name." ".$review->customer->l_name}}
                    </a>
                    <a class="text-dark fz-12" href="tel:'{{$review->customer->phone}}'">{{$review->customer->phone}}</a>
                </div>
            @else
                <span class="badge-pill badge-soft-dark text-muted small">
                                                    {{translate('Customer unavailable')}}
                                                </span>
            @endif
        </td>
        <td>
            <div class="max-w300 line-limit-3">{{$review->comment}}</div>
        </td>
        <td>
            <label class="badge badge-soft-info">
                {{$review->rating}} <i class="tio-star"></i>
            </label>
        </td>
    </tr>

@endforeach
