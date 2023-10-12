<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private Category $category,
    )
    {
    }


    /**
     * @return JsonResponse
     */
    public function get_categories(): JsonResponse
    {
        try {
            $categories = $this->category->where(['position' => 0, 'status' => 1])->orderBY('priority', 'ASC')->get();
            return response()->json($categories, 200);

        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_childes($id): JsonResponse
    {
        try {
            $categories = $this->category->where(['parent_id' => $id, 'status' => 1])->get();
            return response()->json($categories, 200);

        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function get_products($id, Request $request): JsonResponse
    {
        $product_type = $request['product_type'];
        $search = $request['search'];
        return response()->json(Helpers::product_data_formatting(CategoryLogic::products($id, $product_type, $search), true), 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get_all_products($id): JsonResponse
    {
        try {
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products($id), true), 200);

        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
