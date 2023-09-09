<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Model\Admin;
use App\Model\BusinessSetting;
use App\Model\Conversation;
use App\Model\DcConversation;
use App\Model\DeliveryMan;
use App\Model\Message;
use App\Model\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function __construct(
        private Conversation         $conversation,
        private Admin                $admin,
        private BusinessSetting      $business_setting,
        private DcConversation       $dc_conversation,
        private Message              $message,
        private Order                $order,
        private DeliveryMan         $delivery_man
    )
    {
    }


    /**
     * @param Request $request
     * @return array
     */
    public function get_admin_message(Request $request): array
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;
        $messages = $this->conversation->where(['user_id' => $request->user()->id])->latest()->paginate($limit, ['*'], 'page', $offset);
        $messages = ConversationResource::collection($messages);

        return [
            'total_size' => $messages->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $messages->items()
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_admin_message(Request $request): JsonResponse
    {
        if ($request->message == null && $request->image == null) {
            return response()->json(['message' => translate('Message can not be empty')], 403);
        }

        try {
            //if image is given
            $id_img_names = [];
            if (!empty($request->file('image'))) {
                foreach ($request->image as $img) {
                    $image = Helpers::upload('conversation/', 'png', $img);
                    $image_url = asset('storage/app/public/conversation') . '/' . $image;
                    $id_img_names[] = $image_url;
                }
                $images = $id_img_names;
            } else {
                $images = null;
            }
            $conv = $this->conversation;
            $conv->user_id = $request->user()->id;
            $conv->message = $request->message;
            $conv->image = json_encode($images);
            $conv->save();

            //send notification
            $admin = $this->admin->first();
            $data = [
                'title' => $request->user()->f_name . ' ' . $request->user()->l_name . translate(' send a message'),
                'description' => $request->user()->id,
                'order_id' => '',
                'image' => asset('storage/app/public/restaurant') . '/' . $this->business_setting->where(['key' => 'logo'])->first()->value,
                'type' => 'order_status',
            ];

            try {
                Helpers::send_push_notif_to_device($admin->fcm_token, $data);
            } catch (\Exception $exception) {
            }

            return response()->json(['message' => translate('Successfully sent!')], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|array
     */
    public function get_message_by_order(Request $request): JsonResponse|array
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;

        $conversations = $this->dc_conversation->where('order_id', $request->order_id)->first();
        if (!isset($conversations)) {
            return ['total_size' => 0, 'limit' => (int)$limit, 'offset' => (int)$offset, 'messages' => []];
        }
        $conversations = $conversations->setRelation('messages', $conversations->messages()->latest()->paginate($limit, ['*'], 'page', $offset));
        $message = MessageResource::collection($conversations->messages);

        return [
            'total_size' => $message->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $message->items()
        ];
    }

    /**
     * @param Request $request
     * @param $sender_type
     * @return JsonResponse
     */
    public function store_message_by_order(Request $request, $sender_type): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $sender_id = null;
        $order = $this->order->with('delivery_man')->with('customer')->find($request->order_id);

        //if sender is deliveryman
        if ($sender_type == 'deliveryman') {
            $validator = Validator::make($request->all(), [
                'token' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $sender_id = $order->delivery_man->id;

        } //if sender is customer
        elseif ($sender_type == 'customer') {
            $sender_id = $order->customer->id;
        }

        //empty reply check
        if ($request->message == null && $request->image == null) {
            return response()->json(['message' => translate('Message can not be empty')], 400);
        }

        //store image
        $id_img_names = [];
        if (!empty($request->file('image'))) {
            foreach ($request->image as $img) {
                $image = Helpers::upload('conversation/', 'png', $img);
                $image_url = asset('storage/app/public/conversation') . '/' . $image;
                $id_img_names[] = $image_url;
            }
            $images = $id_img_names;
        } else {
            $images = null;
        }

        //if order id is not null
        if ($request->order_id != null) {
            DB::transaction(function () use ($request, $sender_type, $images, $sender_id) {
                $dcConversation = $this->dc_conversation->where('order_id', $request->order_id)->first();
                if (!isset($dcConversation)) {
                    $dcConversation = $this->dc_conversation;
                    $dcConversation->order_id = $request->order_id;
                    $dcConversation->save();
                }

                $message = $this->message;
                $message->conversation_id = $dcConversation->id;
                $message->customer_id = ($sender_type == 'customer') ? $sender_id : null;
                $message->deliveryman_id = ($sender_type == 'deliveryman') ? $sender_id : null;
                $message->message = $request->message ?? null;
                $message->attachment = json_encode($images);
                $message->save();
            });
        }

        //sender push notification
        if ($sender_type == 'customer') {
            $receiver_fcm_token = $order->delivery_man->fcm_token ?? null;

        } elseif ($sender_type == 'deliveryman') {
            $receiver_fcm_token = $order->customer->cm_firebase_token ?? null;
        }

        $data = [
            'title' => translate('New message arrived'),
            'description' => $request->reply,
            'order_id' => $request->order_id ?? null,
            'image' => '',
            'type' => 'order_status',
        ];

        try {
            Helpers::send_push_notif_to_device($receiver_fcm_token, $data);

        } catch (\Exception $exception) {
            return response()->json(['message' => translate('Push notification send failed')], 200);
        }

        return response()->json(['message' => translate('Message successfully sent')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|array
     */
    public function get_order_message_for_dm(Request $request): JsonResponse|array
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        //authentic check
        $deliveryMan = $this->delivery_man->where(['auth_token' => $request['token']])->first();
        if (!isset($deliveryMan)) {
            return response()->json(['errors' => 'Unauthenticated.'], 401);
        }

        //fetch message
        $limit = $request->has('limit') ? $request->limit : 10;
        $offset = $request->has('offset') ? $request->offset : 1;

        $conversations = $this->dc_conversation->where('order_id', $request->order_id)->first();
        if (!isset($conversations)) {
            return ['total_size' => 0, 'limit' => (int)$limit, 'offset' => (int)$offset, 'messages' => []];
        }
        $conversations = $conversations->setRelation('messages', $conversations->messages()->latest()->paginate($limit, ['*'], 'page', $offset));
        $message = MessageResource::collection($conversations->messages);

        return [
            'total_size' => $message->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $message->items()
        ];
    }

}
