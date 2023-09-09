<div class="pos-product-item card" onclick="quickView('{{$product->id}}')">
    <div class="pos-product-item_thumb">
        <img src="{{asset('storage/app/public/product')}}/{{$product['image']}}"
                onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                class="img-fit">
    </div>

    <div class="pos-product-item_content clickable">
        <div class="pos-product-item_title">
            {{ Str::limit($product['name'], 15) }}
        </div>

        <?php
            $pb = json_decode($product->branch_products, true);
            $discount_data = [];
            if(isset($pb[0])){
                $price = $pb[0]['price'];
                $discount_data =[
                    'discount_type' => $pb[0]['discount_type'],
                    'discount' => $pb[0]['discount']
                ];
            }else{
                $price = $product['price'];
                $discount_type = $product['discount_type'];
                $discount = $product['discount'];
                $discount_data =[
                    'discount_type' => $product['discount_type'],
                    'discount' => $product['discount']
                ];
            }
        ?>
        <div class="pos-product-item_price">
            {{ \App\CentralLogics\Helpers::set_symbol(($price- \App\CentralLogics\Helpers::discount_calculate($discount_data, $price))) }}
        </div>
    </div>
</div>
