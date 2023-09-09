<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function __construct(
        private Wishlist $wishlist,
    )
    {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_to_wishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = $this->wishlist->where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($wishlist)) {
            $wishlist = $this->wishlist;
            $wishlist->user_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();

            return response()->json(['message' => translate('added_success')], 200);
        }

        return response()->json(['message' => translate('already_added')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_from_wishlist(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = $this->wishlist->where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            $this->wishlist->where(['user_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => translate('removed_success')], 200);
        }

        return response()->json(['message' => translate('no_data_found')], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function wish_list(Request $request): JsonResponse
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;

        $products = ProductLogic::get_wishlished_products($limit, $offset, $request);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);

        return response()->json($products, 200);
    }
}
