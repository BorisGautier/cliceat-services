<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductByBranch;
use App\Model\Review;
use App\Model\Tag;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class ProductController extends Controller
{
    public function __construct(
        private Product         $product,
        private Category        $category,
        private ProductByBranch $product_by_branch,
        private Translation     $translation,
    )
    {
    }


    public function variant_combination(Request $request): JsonResponse
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_categories(Request $request): JsonResponse
    {
        $cat = $this->category->where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }

        return response()->json([
            'options' => $res,
        ]);
    }

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        $categories = $this->category->where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function list(Request $request): Renderable
    {
        //update daily stock
        Helpers::update_daily_product_stock();

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $query = $this->product;
        }

        $products = $query->with('main_branch_product')->orderBy('id', 'DESC')->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.product.list', compact('products', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $products = $this->product->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();

        return response()->json([
            'view' => view('admin-views.product.partials._table', compact('products'))->render()
        ]);
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function view($id): Renderable
    {
        $product = $this->product->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->latest()->paginate(20);

        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products',
            'category_id' => 'required',
            'image' => 'required',
            'price' => 'required|numeric',
            'item_type' => 'required',
            'product_type' => 'required|in:veg,non_veg',
            'discount_type' => 'required',
            'tax_type' => 'required',
            'stock_type' => 'required|in:unlimited,daily,fixed',
            'product_stock' => 'required_if:stock_type,daily,fixed',
        ], [
            'name.required' => translate('Product name is required!'),
            'name.unique' => translate('Product name has been taken.'),
            'category_id.required' => translate('category  is required!'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }


        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('Discount can not be more or equal to the price!'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tag_ids[] = $tag->id;
            }
        }

        $img_names = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', 'png', $img);
                $img_names[] = $image_data;
            }
            $image_data = json_encode($img_names);
        } else {
            $image_data = json_encode([]);
        }

        $product = $this->product;
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = strip_tags($request->description[array_search('en', $request->lang)]);

        $choice_options = [];
        $product->choice_options = json_encode($choice_options);

        //new variation
        $variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                if ($option['min'] > 0 && $option['min'] >= $option['max']) {
                    $validator->getMessageBag()->add('name', translate('maximum_value_can_not_be_smaller_or_equal_then_minimum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('please_add_options_for') . ' ' . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('please_add_more_options_or_change_the_max_value_for') . ' ' . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    $temp_value[] = $temp_option;
                }
                $temp_variation['values'] = $temp_value;
                $variations[] = $temp_variation;
            }
        }

        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->set_menu = $request->item_type;
        $product->product_type = $request->product_type;
        $product->image = Helpers::upload('product/', 'png', $request->file('image'));
        $product->available_time_starts = $request->available_time_starts;
        $product->available_time_ends = $request->available_time_ends;

        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;

        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $product->status = $request->status == 'on' ? 1 : 0;
        $product->save();

        $product->tags()->sync($tag_ids);

        $main_branch_product = $this->product_by_branch;
        $main_branch_product->product_id = $product->id;
        $main_branch_product->price = $request->price;
        $main_branch_product->discount_type = $request->discount_type;
        $main_branch_product->discount = $request->discount;
        $main_branch_product->branch_id = 1;
        $main_branch_product->is_available = 1;
        $main_branch_product->variations = $variations;
        $main_branch_product->stock_type = $request->stock_type;
        $main_branch_product->stock = $request->product_stock ?? 0;
        $main_branch_product->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => strip_tags($request->description[$index]),
                );
            }
        }
        $this->translation->insert($data);

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $product = $this->product->withoutGlobalScopes()->with(['translations', 'main_branch_product'])->find($id);
        $product_category = json_decode($product->category_ids);
        $categories = $this->category->where(['parent_id' => 0])->get();

        return view('admin-views.product.edit', compact('product', 'product_category', 'categories'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();

        Toastr::success(translate('Product status updated!'));
        return back();
    }


    /**
     * @param $label
     * @param $array
     * @return int|string|null
     */
    function searchForLabel($label, $array): int|string|null
    {
        foreach ($array as $key => $val) {
            if ($val['label'] === $label) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $id,
            'category_id' => 'required',
            'price' => 'required|numeric',
            'product_type' => 'required|in:veg,non_veg',
            'item_type' => 'required',
            'discount_type' => 'required',
            'tax_type' => 'required',
            'stock_type' => 'required|in:unlimited,daily,fixed',
            'product_stock' => 'required_if:stock_type,daily,fixed',
        ], [
            'name.required' => translate('Product name is required!'),
            'category_id.required' => translate('category  is required!'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('Discount can not be more or equal to the price!'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tag_ids[] = $tag->id;
            }
        }

        $product = $this->product->find($id);
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = strip_tags($request->description[array_search('en', $request->lang)]);

        $choice_options = [];
        $product->choice_options = json_encode($choice_options);

        //dd($request->options);

        //new variation
        $variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                if ($option['min'] > 0 && $option['min'] >= $option['max']) {
                    $validator->getMessageBag()->add('name', translate('maximum_value_can_not_be_smaller_or_equal_then_minimum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('please_add_options_for') . ' ' . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('please_add_more_options_or_change_the_max_value_for') . ' ' . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_variation['required'] = $option['required'] ?? 'off';

                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    $temp_value[] = $temp_option;
                }
                $temp_variation['values'] = $temp_value;
                $variations[] = $temp_variation;
            }
        }

        $product->variations = json_encode($variations);

        // branch variation update start
        $branch_products = $this->product_by_branch->where(['product_id' => $id])->get();

        foreach ($branch_products as $branch_product) {
            $mapped = array_map(function ($variation) use ($branch_product) {
                $variation_array = [];
                $variation_array['name'] = $variation['name'];
                $variation_array['type'] = $variation['type'];
                $variation_array['min'] = $variation['min'];
                $variation_array['max'] = $variation['max'];
                $variation_array['required'] = $variation['required'];
                $variation_array['values'] = array_map(function ($value) use ($branch_product, $variation) {
                    $option_array = [];
                    $option_array['label'] = $value['label'];

                    $price = $value['optionPrice'];
                    foreach ($branch_product['variations'] as $branch_variation) {
                        if ($branch_variation['name'] == $variation['name']) {
                            foreach ($branch_variation['values'] as $branch_value) {
                                if ($branch_value['label'] == $value['label']) {
                                    $price = $branch_value['optionPrice'];
                                }
                            }
                        }
                    }
                    $option_array['optionPrice'] = $price;

                    return $option_array;
                }, $variation['values']);
                return $variation_array;
            }, $variations);

            $data = [
                'variations' => $mapped,
            ];

            $this->product_by_branch->whereIn('product_id', [$id])->update($data);
        }

        // branch variation update end
        $product->price = $request->price;
        $product->set_menu = $request->item_type;
        $product->product_type = $request->product_type;
        $product->image = $request->has('image') ? Helpers::update('product/', $product->image, 'png', $request->file('image')) : $product->image;
        $product->available_time_starts = $request->available_time_starts;
        $product->available_time_ends = $request->available_time_ends;

        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;

        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $product->status = $request->status == 'on' ? 1 : 0;
        $product->save();

        $product->tags()->sync($tag_ids);

        $updated_product = $this->product_by_branch->updateOrCreate([
            'product_id' => $product->id,
            'branch_id' => 1,
        ], [
                'product_id' => $product->id,
                'price' => $request->price,
                'discount_type' => $request->discount_type,
                'discount' => $request->discount,
                'branch_id' => 1,
                'is_available' => 1,
                'variations' => $variations,
                'stock_type' => $request->stock_type,
                'stock' =>  $request->product_stock ?? 0,
            ]
        );

        if ($updated_product->wasChanged('stock_type') || $updated_product->wasChanged('stock')) {
            $updated_product->sold_quantity = 0;
            $updated_product->save();
        }

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    ['translationable_type' => 'App\Model\Product',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    ['translationable_type' => 'App\Model\Product',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => strip_tags($request->description[$index])]
                );
            }
        }

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        Helpers::delete('product/' . $product['image']);
        $product->delete();

        Toastr::success(translate('Product removed!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function bulk_import_index(): Renderable
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk_import_data(Request $request): RedirectResponse
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }

        //check
        $field_array = ['name', 'description', 'price', 'tax', 'category_id', 'sub_category_id', 'discount', 'discount_type', 'tax_type', 'set_menu', 'available_time_starts', 'available_time_ends', 'product_type'];
        if (count($collections) < 1) {
            Toastr::error(translate('At least one product have to import.'));
            return back();
        }
        foreach ($field_array as $field) {
            if (!array_key_exists($field, $collections->first())) {
                Toastr::error(translate($field) . translate(' must not be empty.'));
                return back();
            }
        }

        $data = [];
        foreach ($collections as $key => $collection) {
            if ($collection['name'] === "") {
                Toastr::error(translate('Please fill name field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['description'] === "") {
                Toastr::error(translate('Please fill description field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['price'] === "") {
                Toastr::error(translate('Please fill price field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['tax'] === "") {
                Toastr::error(translate('Please fill tax field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['category_id'] === "") {
                Toastr::error(translate('Please fill category_id field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['sub_category_id'] === "") {
                Toastr::error(translate('Please fill sub_category_id field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['discount'] === "") {
                Toastr::error(translate('Please fill discount field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['discount_type'] === "") {
                Toastr::error(translate('Please fill discount_type field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['tax_type'] === "") {
                Toastr::error(translate('Please fill tax_type field of row') . ' ' . ($key + 2));
                return back();
            }
            if ($collection['set_menu'] === "") {
                Toastr::error(translate('Please fill set_menu field of row') . ' ' . ($key + 2));
                return back();
            }

            if ($collection['product_type'] === "") {
                Toastr::error(translate('Please fill product_type field of row') . ' ' . ($key + 2));
                return back();
            }

            if (!is_numeric($collection['price'])) {
                Toastr::error(translate('Price of row') . ' ' . ($key + 2) . ' ' . translate('must be number'));
                return back();
            }

            if (!is_numeric($collection['discount'])) {
                Toastr::error(translate('Discount of row') . ' ' . ($key + 2) . ' ' . translate('must be number'));
                return back();
            }

            if (!is_numeric($collection['tax'])) {
                Toastr::error(translate('Tax of row') . ' ' . ($key + 2) . ' ' . ' must be number');
                return back();
            }

            $product = [
                'discount_type' => $collection['discount_type'],
                'discount' => $collection['discount'],
            ];
            if ($collection['price'] <= Helpers::discount_calculate($product, $collection['price'])) {
                Toastr::error(translate('Discount can not be more or equal to the price in row') . ' ' . ($key + 2));
                return back();
            }
            if (!isset($collection['available_time_starts'])) {
                Toastr::error(translate('Please fill available_time_starts field'));
                return back();
            } elseif ($collection['available_time_starts'] === "") {
                Toastr::error(translate('Please fill available_time_starts field of row') . ' ' . ($key + 2));
                return back();
            }
            if (!isset($collection['available_time_ends'])) {
                Toastr::error(translate('Please fill available_time_ends field'));
                return back();
            } elseif ($collection['available_time_ends'] === "") {
                Toastr::error(translate('Please fill available_time_ends field of row ') . ' ' . ($key + 2));
                return back();
            }
        }

        foreach ($collections as $collection) {
            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => 'def.png',
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'add_ons' => json_encode([]),
                'tax' => $collection['tax'],
                'available_time_starts' => $collection['available_time_starts'],
                'available_time_ends' => $collection['available_time_ends'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 1], ['id' => (string)$collection['sub_category_id'], 'position' => 2]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'set_menu' => $collection['set_menu'],
                'product_type' => $collection['product_type'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        $this->product->insert($data);

        Toastr::success(count($data) . ' - ' . translate('Products imported successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string|RedirectResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function bulk_export_data(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string|RedirectResponse
    {
        if ($request->type == 'date_wise') {
            $request->validate([
                'start_date' => 'required',
                'end_date' => 'required'
            ]);
        }
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $products = $this->product->when($request['type'] == 'date_wise', function ($query) use ($start_date, $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        })->get();

        $storage = [];

        if ($products->count() < 1) {
            Toastr::info(translate('no_product_found'));
            return back();
        }

        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } else if ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                }
            }

            if (!isset($item->name)) {
                $item->name = 'Demo Product';
            }

            if (!isset($item->description)) {
                $item->description = 'No description available';
            }

            $storage[] = [
                'name' => $item->name,
                'description' => $item->description,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'price' => $item->price,
                'tax' => $item->tax,
                'available_time_starts' => $item->available_time_starts,
                'available_time_ends' => $item->available_time_ends,
                'status' => $item->status,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'tax_type' => $item->tax_type,
                'set_menu' => $item->set_menu,
                'product_type' => $item->product_type,
            ];
        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function excel_import(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $storage = [];
        $search = $request['search'];
        $products = $this->product->when($search, function ($query) use ($search) {
            $key = explode(' ', $search);
            foreach ($key as $value) {
                $query->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('name', 'like', "%{$value}%");
            };
        })->get();

        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } elseif ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                }
            }
            if (!isset($item->name)) {
                $item->name = 'Demo Product';
            }
            if (!isset($item->description)) {
                $item->description = 'No description available';
            }
            $storage[] = array(
                'Name' => $item->name,
                'Description' => $item->description,
                'Category ID' => $category_id,
                'Sub Category ID' => $sub_category_id,
                'Price' => $item->price,
                'Tax' => $item->tax,
                'Available Time Starts' => $item->available_time_starts,
                'Available Time Ends' => $item->available_time_ends,
                'Status' => $item->status,
                'Discount' => $item->discount,
                'Discount Type' => $item->discount_type,
                'Tax Type' => $item->tax_type,
                'Set Menu' => $item->set_menu,
                'Product Type' => $item->product_type,
            );
        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * @return Renderable
     */
    public function bulk_export_index(): Renderable
    {
        return view('admin-views.product.bulk-export');
    }
}
