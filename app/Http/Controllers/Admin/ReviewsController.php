<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;


class ReviewsController extends Controller
{
    public function __construct(
        private Review  $review,
        private Product $product,
    )
    {
    }

    /**
     * @return Renderable
     */
    public function list(): Renderable
    {
        $reviews = $this->review->with(['product', 'customer'])->latest()->paginate(Helpers::getPagination());
        return view('admin-views.reviews.list', compact('reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $products = $this->product
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })
            ->pluck('id')
            ->toArray();

        $reviews = $this->review->whereIn('product_id', $products)->get();

        return response()->json([
            'view' => view('admin-views.reviews.partials._table', compact('reviews'))->render(),
            'count' => $reviews->count()
        ]);
    }
}
