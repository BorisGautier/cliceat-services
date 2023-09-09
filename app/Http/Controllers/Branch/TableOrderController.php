<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Table;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CentralLogics\translate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class TableOrderController extends Controller
{
    public function __construct(
        private Order $order,
        private Table $table,
    )
    {
    }


    /**
     * @param $status
     * @param Request $request
     * @return Renderable
     */
    public function order_list($status, Request $request): Renderable
    {
        $from = $request['from'];
        $to = $request['to'];
        $this->order->where(['checked' => 0, 'branch_id' => auth('branch')->id()])->update(['checked' => 1]);

        if ($status == 'all') {
            $orders = $this->order
                ->with(['customer', 'branch', 'table'])
                ->where(['branch_id' => auth('branch')->id()])
                ->when($from && $to, function ($q) use ($from, $to) {
                    $q->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                });

        } else {
            $orders = $this->order
                ->with(['customer', 'branch', 'table'])
                ->where(['order_status' => $status, 'branch_id' => auth('branch')->id()]);
        }

        $query_param = [];
        $search = $request['search'];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = $this->order
                ->where(['branch_id' => auth('branch')->id()])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        }

        $order_count = [
            'confirmed' => $this->order
                ->dineIn()
                ->where(['order_status' => 'confirmed'])
                ->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'cooking' => $this->order
                ->dineIn()->where(['order_status' => 'cooking'])
                ->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'done' => $this->order
                ->dineIn()
                ->where(['order_status' => 'done'])->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'completed' => $this->order
                ->dineIn()
                ->where(['order_status' => 'completed'])->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'canceled' => $this->order
                ->dineIn()
                ->where(['order_status' => 'canceled'])->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),

            'on_table' => $this->order
                ->with('table_order')
                ->whereHas('table_order', function ($q) {
                    $q->where('branch_table_token_is_expired', 0);
                })
                ->where(['branch_id' => auth('branch')->id()])
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count()
        ];

        $orders = $orders->dineIn()->latest()->paginate(Helpers::getPagination())->appends($query_param);
        session()->put('order_data_export', $orders);

        return view('branch-views.table.order.list', compact('orders', 'search', 'status', 'from', 'to', 'order_count'));
    }

    /**
     * @param $id
     * @return Renderable|RedirectResponse
     */
    public function order_details($id): Renderable|RedirectResponse
    {
        $order = $this->order
            ->with('details')
            ->where(['id' => $id])
            ->first();

        if (!isset($order)) {
            Toastr::info(translate('No more orders!'));
            return back();
        }

        //remaining delivery time
        $delivery_date_time = $order['delivery_date'] . ' ' . $order['delivery_time'];
        $ordered_time = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($delivery_date_time)));
        $remaining_time = $ordered_time->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remaining_time;

        return view('branch-views.order.order-view', compact('order'));
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function table_running_order(Request $request): Renderable
    {
        $table_id = $request->table_id;
        $tables = $this->table
            ->with('order')
            ->whereHas('order', function ($q) {
                $q->whereHas('table_order', function ($q) {
                    $q->where('branch_table_token_is_expired', 0);
                });
            })
            ->where(['branch_id' => auth('branch')->id()])
            ->get();

        $orders = $this->order
            ->with('table_order')
            ->whereHas('table_order', function ($q) {
                $q->where('branch_table_token_is_expired', 0);
            })
            ->where(['branch_id' => auth('branch')->id()])
            ->when(!is_null($table_id), function ($query) use ($table_id) {
                return $query->where('table_id', $table_id);
            })
            ->latest()
            ->paginate(Helpers::getPagination());

        return view('branch-views.table.order.table_running_order', compact('tables', 'orders', 'table_id'));
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function running_order_invoice(Request $request): Renderable
    {
        $table_id = $request->table_id;
        $orders = $this->order
            ->with('table_order')
            ->whereHas('table_order', function ($q) {
                $q->where('branch_table_token_is_expired', 0);
            })
            ->when(!is_null($table_id), function ($query) use ($table_id) {
                return $query->where('table_id', $table_id);
            })->get();

        return view('branch-views.table.order.running_order_invoice', compact('orders', 'table_id'));
    }

    /**
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export_excel(): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $data = [];
        $orders = session('order_data_export');
        foreach ($orders as $key => $order) {
            $data[$key]['SL'] = ++$key;
            $data[$key]['Order ID'] = $order->id;
            $data[$key]['Order Date'] = date('d M Y h:m A', strtotime($order['created_at']));
            $data[$key]['Customer Info'] = $order['user_id'] == null ? 'Walk in Customer' : ($order->customer == null ? 'Customer Unavailable' : $order->customer['f_name'] . ' ' . $order->customer['l_name']);
            $data[$key]['Branch'] = $order->branch ? $order->branch->name : 'Branch Deleted';
            $data[$key]['Total Amount'] = Helpers::set_symbol($order['order_amount']);
            $data[$key]['Payment Status'] = $order->payment_status == 'paid' ? 'Paid' : 'Unpaid';
            $data[$key]['Order Status'] = $order['order_status'] == 'pending' ? 'Pending' : ($order['order_status'] == 'confirmed' ? 'Confirmed' : ($order['order_status'] == 'processing' ? 'Processing' : ($order['order_status'] == 'delivered' ? 'Delivered' : ($order['order_status'] == 'picked_up' ? 'Out For Delivery' : str_replace('_', ' ', $order['order_status'])))));
        };

        return (new FastExcel($data))->download('orders.xlsx');
    }

}
