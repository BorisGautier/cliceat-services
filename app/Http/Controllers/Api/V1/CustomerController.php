<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\CustomerAddress;
use App\Model\Newsletter;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\PointTransitions;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerAddress  $customer_address,
        private Order            $order,
        private OrderDetail      $order_detail,
        private User             $user,
        private PointTransitions $point_transitions,
        private Newsletter       $newsletter
    )
    {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function address_list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => auth('api')->user() ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request['guest_id'];
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        return response()->json($this->customer_address->where(['user_id' => $user_id, 'is_guest' => $user_type])->latest()->get(), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_new_address(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'guest_id' => auth('api')->user() ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request['guest_id'];
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        $this->customer_address->insert([
            //'user_id' => $request->user()->id,
            'user_id' => $user_id,
            'is_guest' => $user_type,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'floor' => $request->floor,
            'house' => $request->house,
            'road' => $request->road,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => translate('added_success')], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update_address(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'guest_id' => auth('api')->user() ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request['guest_id'];
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        $this->customer_address->where('id', $id)->update([
            'user_id' => $user_id,
            'is_guest' => $user_type,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'floor' => $request->floor,
            'house' => $request->house,
            'road' => $request->road,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => translate('update_success')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_address(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'guest_id' => auth('api')->user() ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request['guest_id'];
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        if ($this->customer_address->where(['id' => $request['address_id'], 'user_id' => $user_id, 'is_guest' => $user_type])->first()) {
            $this->customer_address->where(['id' => $request['address_id'], 'user_id' => $user_id, 'is_guest' => $user_type])->delete();
            return response()->json(['message' => translate('successfully removed!')], 200);
        }

        return response()->json(['message' => translate('no_data_found')], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_list(Request $request): JsonResponse
    {
        $orders = $this->order->where(['user_id' => $request->user()->id])->get();
        return response()->json($orders, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = $this->order_detail->where(['order_id' => $request['order_id']])->get();
        foreach ($details as $det) {
            $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
        }

        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        return response()->json($request->user(), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_profile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => translate('first_name_required'),
            'l_name.required' => translate('last_name_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'image' => $request->has('image') ? Helpers::update('profile/', $request->user()->imagee, 'png', $request->file('image')) : $request->user()->image,
            'password' => $pass,
            'updated_at' => now(),
        ];

        $this->user->where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => translate('update_success')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_cm_firebase_token(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $this->user->where('id', $request->user()->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token'],
            'language_code' => $request->header('X-localization') ?? 'en'
        ]);

        return response()->json(['message' => translate('update_success')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_transaction_history(Request $request): JsonResponse
    {
        try {
            return response()->json($this->point_transitions->latest()->where(['user_id' => $request->user()->id])->get(), 200);

        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribe_newsletter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $newsLetter = $this->newsletter->where('email', $request->email)->first();
        if (!isset($newsLetter)) {
            $newsLetter = $this->newsletter;
            $newsLetter->email = $request->email;
            $newsLetter->save();

            return response()->json(['message' => translate('Successfully subscribed')], 200);

        } else {
            return response()->json(['message' => translate('Email Already exists')], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_account(Request $request): JsonResponse
    {
        $customer = $this->user->find($request->user()->id);

        if (isset($customer)) {
            Helpers::file_remover('customer/', $customer->image);
            $customer->delete();

        } else {
            return response()->json(['status_code' => 404, 'message' => translate('Not found')], 200);
        }

        return response()->json(['status_code' => 200, 'message' => translate('Successfully deleted')], 200);
    }
}
