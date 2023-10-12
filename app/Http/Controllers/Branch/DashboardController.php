<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function __construct(
        private Order  $order,
        private Branch $branch,
    )
    {
    }


    /**
     * @return Renderable
     */
    public function dashboard(): Renderable
    {
        //update daily stock
        Helpers::update_daily_product_stock();

        $data = self::order_stats_data();

        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $earning = [];
        $earning_data = $this->order->where([
            'order_status' => 'delivered',
            'branch_id' => auth('branch')->id()
        ])->select(
            DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupby('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= 12; $inc++) {
            $earning[$inc] = 0;
            foreach ($earning_data as $match) {
                if ($match['month'] == $inc) {
                    $earning[$inc] = Helpers::set_price($match['sums']);
                }
            }
        }


        $order_statistics_chart = [];
        $order_statistics_chart_data = $this->order->where(['order_status' => 'delivered'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
//            ->whereBetween('created_at', [$from, $to])
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupby('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= 12; $inc++) {
            $order_statistics_chart[$inc] = 0;
            foreach ($order_statistics_chart_data as $match) {
                if ($match['month'] == $inc) {
                    $order_statistics_chart[$inc] = $match['total'];
                }
            }
        }

        $donut = [];
        $donut_data = $this->order->where('branch_id', auth('branch')->id())->get();
        $donut['pending'] = $donut_data->where('order_status', 'pending')->count();
        $donut['ongoing'] = $donut_data->whereIn('order_status', ['confirmed', 'processing', 'out_for_delivery'])->count();
        $donut['delivered'] = $donut_data->where('order_status', 'delivered')->count();
        $donut['canceled'] = $donut_data->where('order_status', 'canceled')->count();
        $donut['returned'] = $donut_data->where('order_status', 'returned')->count();
        $donut['failed'] = $donut_data->where('order_status', 'failed')->count();

        $data['recent_orders'] = $this->order->latest()
            ->where('branch_id', auth('branch')->id())
            ->take(5)
            ->get();

        return view('branch-views.dashboard', compact('data', 'earning', 'order_statistics_chart', 'donut'));
    }

    /**
     * @return Renderable
     */
    public function settings(): Renderable
    {
        return view('branch-views.settings');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settings_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ]);

        $branch = $this->branch->find(auth('branch')->id());

        if ($request->has('image')) {
            $image_name = Helpers::update('branch/', $branch->image, 'png', $request->file('image'));
        } else {
            $image_name = $branch['image'];
        }

        $branch->name = $request->name;
        $branch->image = $image_name;
        $branch->phone = $request->phone;
        $branch->save();

        Toastr::success(translate('Branch updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settings_password_update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8|max:255',
            'confirm_password' => 'required|max:255',
        ]);

        $branch = $this->branch->find(auth('branch')->id());
        $branch->password = bcrypt($request['password']);
        $branch->save();

        Toastr::success(translate('Branch password updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function order_stats(Request $request): JsonResponse
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::order_stats_data();

        return response()->json([
            'view' => view('branch-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    /**
     * @return array
     */
    public function order_stats_data(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $pending = $this->order
            ->where(['order_status' => 'pending', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $confirmed = $this->order
            ->where(['order_status' => 'confirmed', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $processing = $this->order
            ->where(['order_status' => 'processing', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $out_for_delivery = $this->order
            ->where(['order_status' => 'out_for_delivery', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $delivered = $this->order
            ->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $canceled = $this->order
            ->where(['order_status' => 'canceled', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $all = $this->order
            ->where(['branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $returned = $this->order
            ->where(['order_status' => 'returned', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $failed = $this->order
            ->where(['order_status' => 'failed', 'branch_id' => auth('branch')->id()])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $data = [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $out_for_delivery,
            'delivered' => $delivered,
            'all' => $all,
            'returned' => $returned,
            'failed' => $failed
        ];

        return $data;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function order_statistics(Request $request): JsonResponse
    {
        $dateType = $request->type;

        $order_data = array();
        if ($dateType == 'yearOrder') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
                ->select(
                    DB::raw('(count(id)) as total'),
                    DB::raw('YEAR(created_at) year, MONTH(created_at) month')
                )
//                ->whereBetween('created_at', [$from, $to])
                ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
                ->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $order_data[$inc] = 0;
                foreach ($orders as $match) {
                    if ($match['month'] == $inc) {
                        $order_data[$inc] = $match['total'];
                    }
                }
            }
            $key_range = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

        } elseif ($dateType == 'MonthOrder') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d', strtotime($to));
            $key_range = range(1, $number);

            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
                ->select(
                    DB::raw('(count(id)) as total'),
                    DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
                )
//                ->whereBetween('created_at', [$from, $to])
                ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
                ->groupby('created_at')
                ->get()
                ->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $order_data[$inc] = 0;
                foreach ($orders as $match) {
                    if ($match['day'] == $inc) {
                        $order_data[$inc] += $match['total'];
                    }
                }
            }

        } elseif ($dateType == 'WeekOrder') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek();
            $to = Carbon::now()->endOfWeek();
            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
                ->whereBetween('created_at', [$from, $to])->get();

            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $order_data = [];
            foreach ($date_range as $date) {

                $order_data[] = $orders->whereBetween('created_at', [$date, Carbon::parse($date)->endOfDay()])->count();
            }
        }

        $label = $key_range;
        $order_data_final = $order_data;

        $data = array(
            'orders_label' => $label,
            'orders' => array_values($order_data_final),
        );

        return response()->json($data);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function earning_statistics(Request $request): JsonResponse
    {
        $dateType = $request->type;

        $earning_data = array();
        if ($dateType == 'yearEarn') {

            $earning = [];
            $earning_data = $this->order->where([
                'order_status' => 'delivered', 'branch_id' => auth('branch')->id()
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
                ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
                ->groupby('year', 'month')->get()->toArray();
            for ($inc = 1; $inc <= 12; $inc++) {
                $earning[$inc] = 0;
                foreach ($earning_data as $match) {
                    if ($match['month'] == $inc) {
                        $earning[$inc] = Helpers::set_price($match['sums']);
                    }
                }
            }
            $key_range = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
            $order_data = $earning;

        } elseif ($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d', strtotime($to));
            $key_range = range(1, $number);

            $earning = $this->order
                ->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
                ->select(DB::raw('IFNULL(sum(order_amount),0) as sums'), DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day'))
                ->whereBetween('created_at', [Carbon::parse(now())->startOfMonth(), Carbon::parse(now())->endOfMonth()])
                ->groupby('created_at')
                ->get()
                ->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $earning_data[$inc] = 0;
                foreach ($earning as $match) {
                    if ($match['day'] == $inc) {
                        $earning_data[$inc] += $match['sums'];
                    }
                }
            }

            $order_data = $earning_data;
        } elseif ($dateType == 'WeekEarn') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek();
            $to = Carbon::now()->endOfWeek();
            $orders = $this->order
                ->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
                ->whereBetween('created_at', [$from, $to])->get();

            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $order_data = [];
            foreach ($date_range as $date) {
                $order_data[] = $orders->whereBetween('created_at', [$date, Carbon::parse($date)->endOfDay()])->sum('order_amount');
            }
        }

        $label = $key_range;
        $earning_data_final = $order_data;

        $data = array(
            'earning_label' => $label,
            'earning' => array_values($earning_data_final),
        );

        return response()->json($data);
    }

}

