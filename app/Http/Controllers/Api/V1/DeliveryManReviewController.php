<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\DeliveryMan;
use App\Model\DMReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeliveryManReviewController extends Controller
{
    public function __construct(
        private DMReview    $dm_review,
        private DeliveryMan $delivery_man
    )
    {
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_reviews($id): JsonResponse
    {
        $reviews = $this->dm_review->with(['customer', 'delivery_man'])->where(['delivery_man_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            $storage[] = $item;
        }

        return response()->json($storage, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_rating($id): JsonResponse
    {
        try {
            $totalReviews = $this->dm_review->where(['delivery_man_id' => $id])->get();
            $rating = 0;
            foreach ($totalReviews as $key => $review) {
                $rating += $review->rating;
            }

            if ($rating == 0) {
                $overallRating = 0;
            } else {
                $overallRating = number_format($rating / $totalReviews->count(), 2);
            }

            return response()->json(floatval($overallRating), 200);

        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submit_review(Request $request): JsonResponse
    {
        // deleting previous records
        $this->dm_review
            ->where(['user_id' => $request->user()->id, 'delivery_man_id' => $request->delivery_man_id])
            ->delete();

        $validator = Validator::make($request->all(), [
            'delivery_man_id' => 'required',
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
        ]);

        $dm = $this->delivery_man->find($request->delivery_man_id);
        if (isset($dm) == false) {
            $validator->errors()->add('delivery_man_id', 'There is no such delivery man!');
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image_array = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->file('attachment') as $image) {
                if ($image != null) {
                    if (!Storage::disk('public')->exists('review')) {
                        Storage::disk('public')->makeDirectory('review');
                    }
                    $image_array[] = Storage::disk('public')->put('review', $image);
                }
            }
        }

        $review = $this->dm_review;
        $review->user_id = $request->user()->id;
        $review->delivery_man_id = $request->delivery_man_id;
        $review->order_id = $request->order_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($image_array);
        $review->save();

        return response()->json(['message' => translate('successfully review submitted!')], 200);
    }
}
