<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\DMSelfRegistration;
use App\Model\DeliveryMan;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeliveryManLoginController extends Controller
{
    public function __construct(
        private DeliveryMan $delivery_man,
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'required|max:100',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
            'password' => 'required|min:8',
            'image' => 'required|max:2048',
            'identity_type' => 'required|in:passport,driving_license,nid,restaurant_id',
            'identity_number' => 'required',
            'identity_image' => 'required|max:2048',
            'branch_id' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
            'email.required' => translate('Email is required!'),
            'email.unique' => translate('Email is already registered'),
            'phone.required' => translate('Phone is required!'),
            'phone.unique' => translate('Phone number is already registered'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $image_name = Helpers::upload('delivery-man/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('delivery-man/', 'png', $img);
                $id_img_names[] = $identity_image;
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = $this->delivery_man;
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->branch_id = $request->branch_id;
        $dm->identity_image = $identity_image;
        $dm->image = $image_name;
        $dm->is_active = 0;
        $dm->password = bcrypt($request->password);
        $dm->application_status= 'pending';
        $dm->language_code = $request->header('X-localization') ?? 'en';
        $dm->save();

        try{
            //send email
            $emailServices = Helpers::get_business_settings('mail_config');
            $mail_status = Helpers::get_business_settings('registration_mail_status_dm');
            if(isset($emailServices['status']) && $emailServices['status'] == 1 && $mail_status == 1){
                Mail::to($dm->email)->send(new DMSelfRegistration('pending', $dm->f_name.' '.$dm->l_name, $dm->language_code));
            }

        }catch(\Exception $ex){
            info($ex);
        }

        return response()->json(['message' => translate('deliveryman_added_successfully')], 200);
    }



    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = $this->delivery_man
            ->where(['email' => $request->email])
            ->first();


        if (isset($dm) && $dm->application_status != 'approved'){
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Not verified.'];
            return response()->json([
                'errors' => $errors
            ], 401);
        }

        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true
        ];

        $max_login_hit = Helpers::get_business_settings('maximum_login_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_login_block_time') ?? 600; // seconds

        if (isset($dm)){
            if (auth('delivery_men')->attempt($data)) {
                if(isset($dm->temp_block_time ) && Carbon::parse($dm->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time = $temp_block_time - Carbon::parse($dm->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors[] = ['code' => 'login_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                $token = Str::random(120);
                $dm->auth_token = $token;
                $dm->login_hit_count = 0;
                $dm->is_temp_blocked = 0;
                $dm->temp_block_time = null;
                $dm->updated_at = now();
                $dm->save();
                return response()->json(['token' => $token], 200);
            }
            else{
                if(isset($dm->temp_block_time ) && Carbon::parse($dm->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($dm->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors[] = [
                        'code' => 'login_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                if($dm->is_temp_blocked == 1 && Carbon::parse($dm->temp_block_time)->DiffInSeconds() >= $temp_block_time){
                    $dm->login_hit_count = 0;
                    $dm->is_temp_blocked = 0;
                    $dm->temp_block_time = null;
                    $dm->updated_at = now();
                    $dm->save();
                }

                if($dm->login_hit_count >= $max_login_hit &&  $dm->is_temp_blocked == 0){
                    $dm->is_temp_blocked = 1;
                    $dm->temp_block_time = now();
                    $dm->updated_at = now();
                    $dm->save();

                    $time= $temp_block_time - Carbon::parse($dm->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors[] = [
                        'code' => 'login_temp_blocked',
                        'message' => translate('Too_many_attempts. please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }
            }
            $dm->login_hit_count += 1;
            $dm->temp_block_time = null;
            $dm->updated_at = now();
            $dm->save();
        }

        $errors = [];
        $errors[] = ['code' => 'auth-001', 'message' => 'Invalid credentials.'];
        return response()->json(['errors' => $errors], 401);
    }
}
