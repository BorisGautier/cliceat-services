<div class="pos-product-item card" onclick="quickView('{{$product->id}}')">
    <div class="pos-product-item_thumb">
        <img class="img-fit" src="{{asset('storage/app/public/product')}}/{{$product['image']}}"
                 onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'">
    </div>
    <?php
        $pb = json_decode($product->product_by_branch, true);
        $price = 0;
        $discount_data = [];
        if(isset($pb[0])){
            $price = $pb[0]['price'];
            $discount_data =[
                'discount_type' => $pb[0]['discount_type'],
                'discount' => $pb[0]['discount']
            ];
        }
    ?>

    <div class="pos-product-item_content clickable">
        <div class="pos-product-item_title">{{ Str::limit($product['name'], 15) }}</div>

        <div class="pos-product-item_price">
            {{ \App\CentralLogics\Helpers::set_symbol(($price- \App\CentralLogics\Helpers::discount_calculate($discount_data, $price))) }}
            {{--@if($product->discount > 0)
                <strike style="font-size: 8px!important;color: grey!important;">
                    {{ \App\CentralLogics\Helpers::set_symbol($product['price']) }}
                </strike><br>
            @endif --}}
        </div>
    </div>
</div>
