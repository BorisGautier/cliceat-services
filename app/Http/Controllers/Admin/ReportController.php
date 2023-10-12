<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\OrderDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class ReportController extends Controller
{
    public function __construct(
        private Order       $order,
        private OrderDetail $order_detail,
    )
    {
    }

    /**
     * @return Renderable
     */
    public function order_index(): Renderable
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }

        return view('admin-views.report.order-index');
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function earning_index(Request $request): Renderable
    {
        $from = Carbon::parse($request->from)->startOfDay();
        $to = Carbon::parse($request->to)->endOfDay();

        if ($request->from > $request->to) {
            Toastr::warning(translate('Invalid date range!'));
        }

        $orders = $this->order->where(['order_status' => 'delivered'])
            ->when($request->from && $request->to, function ($q) use ($from, $to) {
                session()->put('from_date', $from);
                session()->put('to_date', $to);
                $q->whereBetween('created_at', [$from, $to]);
            })->get();

        $add_on_tax_amount = 0;

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $add_on_tax_amount += $detail->add_on_tax_amount;
            }
        }

        $product_tax = $orders->sum('total_tax_amount');
        $total_tax = $product_tax + $add_on_tax_amount;

        //$total_tax = $orders->sum('total_tax_amount');
        $total_sold = $orders->sum('order_amount');

        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }

        return view('admin-views.report.earning-index', compact('total_tax', 'total_sold', 'from', 'to'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function set_date(Request $request): RedirectResponse
    {
        $fromDate = Carbon::parse($request['from'])->startOfDay();
        $toDate = Carbon::parse($request['to'])->endOfDay();

        session()->put('from_date', $fromDate);
        session()->put('to_date', $toDate);

        return back();
    }

    /**
     * @return Renderable
     */
    public function deliveryman_report(): Renderable
    {
        $orders = $this->order->with(['customer', 'branch'])->paginate(25);
        return view('admin-views.report.driver-index', compact('orders'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deliveryman_filter(Request $request): JsonResponse
    {
        $fromDate = Carbon::parse($request->formDate)->startOfDay();
        $toDate = Carbon::parse($request->toDate)->endOfDay();

        $orders = $this->order
            ->where(['delivery_man_id' => $request['delivery_man']])
            ->where(['order_status' => 'delivered'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render(),
            'delivered_qty' => $orders->count()
        ]);
    }

    /**
     * @return Renderable
     */
    public function product_report(): Renderable
    {
        return view('admin-views.report.product-report');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function product_report_filter(Request $request): JsonResponse
    {
        $fromDate = Carbon::parse($request->from)->startOfDay();
        $toDate = Carbon::parse($request->to)->endOfDay();

        $orders = $this->order->when($request['branch_id'] != 'all', function ($query) use ($request) {
            $query->where('branch_id', $request['branch_id']);
        })
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->latest()
            ->get();

        $data = [];
        $total_sold = 0;
        $total_qty = 0;
        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                if ($request['product_id'] != 'all') {
                    if ($detail['product_id'] == $request['product_id']) {
                        $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
                        $ord_total = $price * $detail['quantity'];
                        $data[] = [
                            'order_id' => $order['id'],
                            'date' => $order['created_at'],
                            'customer' => $order->customer,
                            'price' => $ord_total,
                            'quantity' => $detail['quantity'],
                        ];
                        $total_sold += $ord_total;
                        $total_qty += $detail['quantity'];
                    }

                } else {
                    $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
                    $ord_total = $price * $detail['quantity'];
                    $data[] = [
                        'order_id' => $order['id'],
                        'date' => $order['created_at'],
                        'customer' => $order->customer,
                        'price' => $ord_total,
                        'quantity' => $detail['quantity'],
                    ];
                    $total_sold += $ord_total;
                    $total_qty += $detail['quantity'];
                }
            }
        }

        session()->put('export_data', $data);

        return response()->json([
            'order_count' => count($data),
            'item_qty' => $total_qty,
            'order_sum' => \App\CentralLogics\Helpers::set_symbol($total_sold),
            'view' => view('admin-views.report.partials._table', compact('data'))->render(),
        ]);
    }

    /**
     * @return mixed
     */
    public function export_product_report(): mixed
    {
        if (session()->has('export_data')) {
            $data = session('export_data');

        } else {
            $orders = $this->order->all();
            $data = [];
            $total_sold = 0;
            $total_qty = 0;
            foreach ($orders as $order) {
                foreach ($order->details as $detail) {
                    $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
                    $ord_total = $price * $detail['quantity'];
                    $data[] = [
                        'order_id' => $order['id'],
                        'date' => $order['created_at'],
                        'customer' => $order->customer,
                        'price' => $ord_total,
                        'quantity' => $detail['quantity'],
                    ];
                    $total_sold += $ord_total;
                    $total_qty += $detail['quantity'];
                }
            }
        }

        $pdf = PDF::loadView('admin-views.report.partials._report', compact('data'));
        return $pdf->download('report_' . rand(00001, 99999) . '.pdf');
    }

    /**
     * @return Renderable
     */
    public function sale_report(): Renderable
    {
        return view('admin-views.report.sale-report');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sale_filter(Request $request): JsonResponse
    {
        $fromDate = Carbon::parse($request->from)->startOfDay();
        $toDate = Carbon::parse($request->to)->endOfDay();

        if ($request['branch_id'] == 'all') {
            $orders = $this->order->whereBetween('created_at', [$fromDate, $toDate])->pluck('id')->toArray();

        } else {
            $orders = $this->order
                ->where(['branch_id' => $request['branch_id']])
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->pluck('id')
                ->toArray();
        }

        $data = [];
        $total_sold = 0;
        $total_qty = 0;

        foreach ($this->order_detail->whereIn('order_id', $orders)->latest()->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];
            $data[] = [
                'order_id' => $detail['order_id'],
                'date' => $detail['created_at'],
                'price' => $ord_total,
                'quantity' => $detail['quantity'],
            ];
            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        return response()->json([
            'order_count' => count($data),
            'item_qty' => $total_qty,
            'order_sum' => Helpers::set_symbol($total_sold),
            'view' => view('admin-views.report.partials._table', compact('data'))->render(),
        ]);
    }

    /**
     * @return mixed
     */
    public function export_sale_report(): mixed
    {
        $data = session('export_sale_data');
        $pdf = PDF::loadView('admin-views.report.partials._report', compact('data'));

        return $pdf->download('sale_report_' . rand(00001, 99999) . '.pdf');
    }
}
