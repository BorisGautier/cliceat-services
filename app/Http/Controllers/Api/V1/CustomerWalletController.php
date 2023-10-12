<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\WalletBonus;
use App\Model\WalletTransaction;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerWalletController extends Controller
{
    public function __construct(
        private User              $user,
        private BusinessSetting   $business_setting,
        private WalletTransaction $wallet_transaction,
        private WalletBonus $wallet_bonus
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function transfer_loyalty_point_to_wallet(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'point' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        //user point check (if has sufficient amount)
        $user = $this->user->find($request->user()->id);
        if ($request['point'] > $user->point) {
            return response()->json(['errors' => [['code' => 'wallet', 'message' => translate('Your point in not sufficient!')]]], 401);
        }

        //minimum point check (for transferring)
        $min_point = $this->business_setting->where(['key' => 'loyalty_point_minimum_point'])->first()->value;
        if ($request['point'] < $min_point) {
            return response()->json(['errors' => [['code' => 'wallet', 'message' => translate('Your point in not sufficient!')]]], 401);
        }

        $loyalty_point_exchange_rate = $this->business_setting->where(['key' => 'loyalty_point_exchange_rate'])->first()->value;
        $loyalty_amount = $request['point'] / $loyalty_point_exchange_rate;

        //point transfer transaction
        CustomerLogic::loyalty_point_wallet_transfer_transaction($user->id, $request['point'], $loyalty_amount);

        return response()->json(['message' => translate('transfer success')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function wallet_transactions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $transaction_type = $request['transaction_type'];

        $paginator = $this->wallet_transaction
            ->when(isset($transaction_type) && ($transaction_type == 'add_fund_by_admin'), function ($query) {
                return $query->where('transaction_type', 'add_fund_by_admin');
            })
            ->when(isset($transaction_type) && ($transaction_type == 'add_fund'), function ($query) {
                return $query->where('transaction_type', 'add_fund');
            })
            ->when(isset($transaction_type) && ($transaction_type == 'loyalty_point_to_wallet'), function ($query) {
                return $query->where('transaction_type', 'loyalty_point_to_wallet');
            })
            ->when(isset($transaction_type) && ($transaction_type == 'referral_order_place'), function ($query) {
                return $query->where('transaction_type', 'referral_order_place');
            })
            ->when(isset($transaction_type) && ($transaction_type == 'add_fund_bonus'), function ($query) {
                return $query->where('transaction_type', 'add_fund_bonus');
            })
            ->when(isset($transaction_type) && ($transaction_type == 'order_place'), function ($query) {
                return $query->where('transaction_type', 'order_place');
            })
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->limit, ['*'], 'page', $request->offset);

        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request->limit,
            'offset' => $request->offset,
            'data' => $paginator->items()
        ];

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function wallet_bonus_list(): JsonResponse
    {
        $bonuses = $this->wallet_bonus->active()
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->latest()
            ->get();

        return response()->json($bonuses, 200);
    }
}
