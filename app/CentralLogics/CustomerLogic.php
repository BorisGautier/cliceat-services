<?php

namespace App\CentralLogics;

use App\Model\BusinessSetting;
use App\Model\PointTransitions;
use App\User;
use App\Model\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerLogic{
    public static function create_wallet_transaction($user_id, float $amount, $transaction_type, $referance)
    {

        if(BusinessSetting::where('key','wallet_status')->first()->value != 1) return false;

        $user = User::find($user_id);
        $current_balance = $user->wallet_balance;

        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $user->id;
        $wallet_transaction->transaction_id = Str::random('30');
        $wallet_transaction->reference = $referance;
        $wallet_transaction->transaction_type = $transaction_type;

        $debit = 0.0;
        $credit = 0.0;

        if(in_array($transaction_type, ['add_fund_by_admin','add_fund','order_refund','loyalty_point', 'referrer']))
        {
            $credit = $amount;
            if($transaction_type == 'add_fund')
            {
                $wallet_transaction->admin_bonus = $amount * BusinessSetting::where('key','wallet_add_fund_bonus')->first()->value/100;
            }
            else if($transaction_type == 'loyalty_point')
            {
                $credit = (int)($amount / BusinessSetting::where('key','loyalty_point_exchange_rate')->first()->value);
            }
        }
        else if($transaction_type == 'order_place')
        {
            $debit = $amount;
        }

        $wallet_transaction->credit = $credit;
        $wallet_transaction->debit = $debit;
        $wallet_transaction->balance = $current_balance + $credit - $debit;
        $wallet_transaction->created_at = now();
        $wallet_transaction->updated_at = now();
        $user->wallet_balance = $current_balance + $credit - $debit;

        //dd($wallet_transaction);

        try{
            DB::beginTransaction();
            $user->save();
            $wallet_transaction->save();
            DB::commit();
            if(in_array($transaction_type, ['loyalty_point','order_place','add_fund_by_admin', 'referrer'])) return $wallet_transaction;
            return true;
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollback();

            return false;
        }
        return false;
    }

    public static function create_loyalty_point_transaction($user_id, $referance, $amount, $transaction_type)
    {
        $settings = array_column(BusinessSetting::whereIn('key',['loyalty_point_status','loyalty_point_exchange_rate','loyalty_point_item_purchase_point'])->get()->toArray(), 'value','key');
        if($settings['loyalty_point_status'] != 1)
        {
            return true;
        }

        $credit = 0;
        $debit = 0;
        $user = User::find($user_id);

        $loyalty_point_transaction = new PointTransitions();
        $loyalty_point_transaction->user_id = $user->id;
        $loyalty_point_transaction->transaction_id = Str::random('30');
        $loyalty_point_transaction->reference = $referance;
        $loyalty_point_transaction->type = $transaction_type;

        if($transaction_type=='order_place')
        {
            $credit = (int)($amount * $settings['loyalty_point_item_purchase_point']/100);
        }
        else if($transaction_type=='point_to_wallet')
        {
            $debit = $amount;
        }

        $current_balance = $user->point + $credit - $debit;
        $loyalty_point_transaction->amount = $current_balance;
        $loyalty_point_transaction->credit = $credit;
        $loyalty_point_transaction->debit = $debit;
        $loyalty_point_transaction->created_at = now();
        $loyalty_point_transaction->updated_at = now();
        $user->point = $current_balance;

        try{
            DB::beginTransaction();
            $user->save();
            $loyalty_point_transaction->save();
            DB::commit();
            return true;
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollback();

            return false;
        }
        return false;
    }


    public static function referral_earning_wallet_transaction($user_id, $transaction_type, $referance)
    {
        $user = User::find($referance);
        $current_balance = $user->wallet_balance;

        $debit = 0.0;
        $credit = 0.0;
        $amount = BusinessSetting::where('key','ref_earning_exchange_rate')->first()->value?? 0;
        $credit = $amount;

        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $user->id;
        $wallet_transaction->transaction_id = Str::random('30');
        $wallet_transaction->reference = $user_id;
        $wallet_transaction->transaction_type = $transaction_type;
        $wallet_transaction->credit = $credit;
        $wallet_transaction->debit = $debit;
        $wallet_transaction->balance = $current_balance + $credit;
        $wallet_transaction->created_at = now();
        $wallet_transaction->updated_at = now();
        $user->wallet_balance = $current_balance + $credit;

        try{
            DB::beginTransaction();
            $user->save();
            $wallet_transaction->save();
            DB::commit();
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollback();

            return false;
        }
    }

    public static function loyalty_point_wallet_transfer_transaction($user_id, $point, $amount) {

        DB::transaction(function () use ($user_id, $point, $amount) {

            //Customer (loyalty_point update)
            $user = User::find($user_id);
            $current_wallet_balance = $user->wallet_balance;
            $current_point = $user->point;
            //dd($current_wallet_balance);

            $user->point -= $point;
            $user->wallet_balance += $amount;
            $user->save();

            WalletTransaction::create([
                'user_id' => $user_id,
                'transaction_id' => Str::random('30'),
                'reference' => null,
                'transaction_type' => 'loyalty_point_to_wallet',
                'debit' => 0,
                'credit' => $amount,
                'balance' => $current_wallet_balance + $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            PointTransitions::create([
                'user_id' => $user_id,
                'transaction_id' => Str::random('30'),
                'reference' => null,
                'type' => 'loyalty_point_to_wallet',
                'debit' => $point,
                'credit' => 0,
                'amount' => $current_point - $point,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

}
