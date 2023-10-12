<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\WalletBonus;
use App\Model\WalletTransaction;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;

class CustomerWalletController extends Controller
{
    public function __construct(
        private WalletTransaction $wallet_transaction,
        private User              $user,
        private WalletBonus $wallet_bonus
    )
    {
    }

    /**
     * @return Renderable|RedirectResponse
     */
    public function add_fund_view(): Renderable|RedirectResponse
    {
        if (BusinessSetting::where('key', 'wallet_status')->first()->value != 1) {
            Toastr::error(translate('customer_wallet_status_is_disable'));
            return back();
        }

        return view('admin-views.customer.wallet.add-fund');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_fund(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'exists:users,id',
            'amount' => 'numeric|min:0|not_in:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_transaction = CustomerLogic::create_wallet_transaction($request->customer_id, $request->amount, 'add_fund_by_admin', $request->referance);

        if ($wallet_transaction) {
            return response()->json([], 200);
        }

        return response()->json(['errors' => [
            'message' => translate('failed_to_create_transaction')
        ]], 200);
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function report(Request $request): Renderable
    {
        $data = $this->wallet_transaction
            ->selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
            ->when(($request->from && $request->to), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            })
            ->when($request->transaction_type, function ($query) use ($request) {
                $query->where('transaction_type', $request->transaction_type);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('user_id', $request->customer_id);
            })
            ->get();

        $transactions = $this->wallet_transaction
            ->when(($request->from && $request->to), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            })
            ->when($request->transaction_type, function ($query) use ($request) {
                $query->where('transaction_type', $request->transaction_type);
            })
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('user_id', $request->customer_id);
            })
            ->latest()
            ->paginate(Helpers::getPagination());

        return view('admin-views.customer.wallet.report', compact('data', 'transactions'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_customers(Request $request): JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = $this->user
            ->where('user_type', null)
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->limit(8)
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);
        if ($request->all) $data[] = (object)['id' => false, 'text' => translate('all')];

        return response()->json($data);
    }
}
