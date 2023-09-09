<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\Model\Newsletter;
use App\Model\Order;
use App\Model\PointTransitions;
use App\Model\BusinessSetting;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function __construct(
        private User             $customer,
        private PointTransitions $point_transitions,
        private Order            $order,
        private Newsletter       $newsletter,
        private Conversation     $conversation,
        private BusinessSetting  $business_setting
    )
    {
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function add_point(Request $request, $id): JsonResponse
    {
        DB::transaction(function () use ($request, $id) {
            $user = $this->customer->find($id);
            $credit = $request['point'];
            $debit = 0;
            $current_amount = $user->point + $credit;

            $loyalty_point_transaction = $this->point_transitions;
            $loyalty_point_transaction->user_id = $user->id;
            $loyalty_point_transaction->transaction_id = Str::random('30');
            $loyalty_point_transaction->reference = 'admin';
            $loyalty_point_transaction->type = 'point_in';
            $loyalty_point_transaction->amount = $current_amount;
            $loyalty_point_transaction->credit = $credit;
            $loyalty_point_transaction->debit = $debit;
            $loyalty_point_transaction->created_at = now();
            $loyalty_point_transaction->updated_at = now();
            $loyalty_point_transaction->save();

            $user->point = $current_amount;
            $user->save();
        });

        if ($request->ajax()) {
            return response()->json([
                'updated_point' => $this->customer->where(['id' => $id])->first()->point
            ]);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function set_point_modal_data($id): JsonResponse
    {
        $customer = $this->customer->find($id);

        return response()->json([
            'view' => view('admin-views.customer.partials._add-point-modal-content', compact('customer'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function customer_list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = $this->customer->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = $this->customer;
        }

        $customers = $customers->with(['orders'])->where('user_type', null)->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.list', compact('customers', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $customers = $this->customer->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();

        return response()->json([
            'view' => view('admin-views.customer.partials._table', compact('customers'))->render(),
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Renderable
     */
    public function view($id, Request $request): RedirectResponse|Renderable
    {
        $search = $request->search;
        $customer = $this->customer->find($id);

        if (!isset($customer)) {
            Toastr::error(translate('Customer not found!'));
            return back();
        }

        $orders = $this->order->latest()->where(['user_id' => $id])
            ->when($search, function ($query) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $query->where('id', 'like', "%$value%");
                }
            })
            ->paginate(Helpers::getPagination())
            ->appends(['search' => $search]);

        return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function AddPoint(Request $request, $id): RedirectResponse
    {
        $point = $this->customer->where(['id' => $id])->first()->point;

        $requestPoint = $request['point'];
        $point += $requestPoint;

        $this->customer->where(['id' => $id])->update([
            'point' => $point,
        ]);

        $this->point_transitions->insert([
            'user_id' => $request['id'],
            'description' => 'admin Added point',
            'type' => 'point_in',
            'amount' => $request['point'],
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        Toastr::success(translate('Point Added Successfully !'));
        return back();

    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function transaction(Request $request): Renderable
    {
        $query_param = ['search' => $request['search']];
        $search = $request['search'];

        $transition = $this->point_transitions->with(['customer'])->latest()
            ->when($request->has('search'), function ($q) use ($search) {
                $q->whereHas('customer', function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%");
                    }
                });
            })
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.customer.transaction-table', compact('transition', 'search'));
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function subscribed_emails(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = $this->newsletter->
            where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $newsletters = $this->newsletter;
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function customer_transaction($id, Request $request): Renderable
    {
        $search = $request['search'];
        $query_param = ['search' => $search];

        $transition = $this->point_transitions->with(['customer'])
            ->where(['user_id' => $id])
            ->when($request->has('search'), function ($query) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $query->where('transaction_id', 'like', "%{$value}%");
                }
            })
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.customer.transaction-table', compact('transition', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_user_info(Request $request): JsonResponse
    {
        $user = $this->customer->find($request['id']);
        $unchecked = $this->conversation->where(['user_id' => $request['id'], 'checked' => 0])->count();

        $output = [
            'id' => $user->id ?? '',
            'f_name' => $user->f_name ?? '',
            'l_name' => $user->l_name ?? '',
            'email' => $user->email ?? '',
            'image' => ($user && $user->image) ? asset('storage/app/public/profile') . '/' . $user->image : asset('/assets/admin/img/160x160/img1.jpg'),
            'cm_firebase_token' => $user->cm_firebase_token ?? '',
            'unchecked' => $unchecked ?? 0

        ];

        $result = get_headers($output['image']);
        if (!stripos($result[0], "200 OK")) {
            $output['image'] = asset('/assets/admin/img/160x160/img1.jpg');
        }

        return response()->json($output);
    }

    /**
     * @param Request $request
     * @return bool|array
     */
    public function message_notification(Request $request): bool|array
    {
        $user = $this->customer->find($request['id']);
        $fcm_token = $user->cm_firebase_token;

        $data = [
            'title' => 'New Message' . ($request->has('image_length') && $request->image_length > 0 ? (' (with ' . $request->image_length . ' attachment)') : ''),
            'description' => $request->message,
            'order_id' => '',
            'image' => $request->has('image_length') ? $request->image_length : null,
            'type' => 'order_status',
        ];

        try {
            Helpers::send_push_notif_to_device($fcm_token, $data);
            return $data;
        } catch (\Exception $exception) {
            return false;
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function chat_image_upload(Request $request): JsonResponse
    {
        $id_img_names = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image = Helpers::upload('conversation/', 'png', $img);
                $image_url = asset('storage/app/public/conversation') . '/' . $image;
                $id_img_names[] = $image_url;
            }
            $images = $id_img_names;
        } else {
            $images = null;
        }
        return response()->json(['image_urls' => $images], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update_status(Request $request, $id): JsonResponse
    {
        $this->customer->findOrFail($id)->update(['is_active' => $request['status']]);
        return response()->json($request['status']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $this->customer->findOrFail($request['id'])->delete();
            Toastr::success(translate('user_deleted_successfully!'));

        } catch (\Exception $e) {
            Toastr::error(translate('user_not_found!'));
        }
        return back();
    }

    /**
     * @return StreamedResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function excel_import(): StreamedResponse|string
    {
        $users = $this->customer->select('f_name as First Name', 'l_name as Last Name', 'email as Email', 'is_active as Active', 'phone as Phone', 'point as Point')->get();
        return (new FastExcel($users))->download('customers.xlsx');
    }

    /**
     * @return Renderable
     */
    public function settings(): Renderable
    {
        $data = $this->business_setting->where('key', 'like', 'wallet_%')
            ->orWhere('key', 'like', 'loyalty_%')
            ->orWhere('key', 'like', 'ref_earning_%')
            ->orWhere('key', 'like', 'ref_earning_%')->get();
        $data = array_column($data->toArray(), 'value', 'key');

        return view('admin-views.customer.settings', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_settings(Request $request): RedirectResponse
    {

        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('update_option_is_disable_for_demo'));
            return back();
        }

        $request->validate([
            'add_fund_bonus' => 'nullable|numeric|max:100|min:0',
            'loyalty_point_exchange_rate' => 'nullable|numeric',
            'ref_earning_exchange_rate' => 'nullable|numeric',
        ]);

        $this->business_setting->updateOrInsert(['key' => 'wallet_status'], [
            'value' => $request['customer_wallet'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'loyalty_point_status'], [
            'value' => $request['customer_loyalty_point'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'ref_earning_status'], [
            'value' => $request['ref_earning_status'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'loyalty_point_exchange_rate'], [
            'value' => $request['loyalty_point_exchange_rate'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'ref_earning_exchange_rate'], [
            'value' => $request['ref_earning_exchange_rate'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'loyalty_point_item_purchase_point'], [
            'value' => $request['item_purchase_point'] ?? 0
        ]);
        $this->business_setting->updateOrInsert(['key' => 'loyalty_point_minimum_point'], [
            'value' => $request['minimun_transfer_point'] ?? 0
        ]);

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }


}
