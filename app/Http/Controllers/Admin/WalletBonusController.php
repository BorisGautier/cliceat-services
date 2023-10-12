<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\WalletBonus;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WalletBonusController extends Controller
{
    public function __construct(
        private WalletBonus $wallet_bonus
    ){}

    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $bonuses = $this->wallet_bonus->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('description', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $bonuses = $this->wallet_bonus;
        }

        $bonuses = $bonuses->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.wallet-bonus.index', compact('bonuses', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:191',
            'start_date' => 'required',
            'end_date' => 'required',
            'bonus_type' => 'required|in:percentage,amount',
            'bonus_amount' => 'required',
            'minimum_add_amount' => 'required',
            'maximum_bonus_amount' => 'required_if:bonus_type,percentage',
        ],[
            'title.required'=>translate('title_is_required'),
        ]);

        $bonus = $this->wallet_bonus;
        $bonus->title = $request->title;
        $bonus->description = $request->description;
        $bonus->bonus_type = $request->bonus_type;
        $bonus->start_date = $request->start_date;
        $bonus->end_date = $request->end_date;
        $bonus->minimum_add_amount = $request->minimum_add_amount != null ? $request->minimum_add_amount : 0;
        $bonus->maximum_bonus_amount = $request->maximum_bonus_amount != null ? $request->maximum_bonus_amount : 0;
        $bonus->bonus_amount = $request->bonus_amount;
        $bonus->status =  1;
        $bonus->save();

        Toastr::success(translate('bonus_added_successfully'));
        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $bonus = $this->wallet_bonus->find($id);
        return view('admin-views.customer.wallet-bonus.edit', compact('bonus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:191',
            'start_date' => 'required',
            'end_date' => 'required',
            'bonus_type' => 'required|in:percentage,amount',
            'bonus_amount' => 'required',
            'minimum_add_amount' => 'required',
            'maximum_bonus_amount' => 'required_if:bonus_type,percentage',
        ],[
            'title.required'=>translate('title_is_required'),
        ]);

        $bonus = $this->wallet_bonus->find($request->id);
        $bonus->title = $request->title;
        $bonus->description = $request->description;
        $bonus->bonus_type = $request->bonus_type;
        $bonus->start_date = $request->start_date;
        $bonus->end_date = $request->end_date;
        $bonus->minimum_add_amount = $request->minimum_add_amount != null ? $request->minimum_add_amount : 0;
        $bonus->maximum_bonus_amount = $request->maximum_bonus_amount != null ? $request->maximum_bonus_amount : 0;
        $bonus->bonus_amount = $request->bonus_amount;
        $bonus->save();

        Toastr::success(translate('bonus_updated_successfully'));
        return redirect()->route('admin.customer.wallet.bonus.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $bonus = $this->wallet_bonus->find($request->id);
        $bonus->status = $request->status;
        $bonus->save();

        Toastr::success(translate('Bonus status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $bonus = $this->wallet_bonus->find($request->id);
        $bonus->delete();

        Toastr::success(translate('Bonus removed!'));
        return back();
    }
}
