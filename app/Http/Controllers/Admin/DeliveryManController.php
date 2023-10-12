<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\DMSelfRegistration;
use App\Model\DeliveryMan;
use App\Model\DMReview;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class DeliveryManController extends Controller
{
    public function __construct(
        private DeliveryMan $delivery_man,
        private DMReview    $dm_review
    ){}

    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('admin-views.delivery-man.index');
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = $this->delivery_man->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $delivery_men = $this->delivery_man;
        }

        $delivery_men = $delivery_men
            ->withCount('orders')
            ->where('application_status', 'approved')
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.delivery-man.list', compact('delivery_men', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $delivery_men = $this->delivery_man->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();

        return response()->json([
            'view' => view('admin-views.delivery-man.partials._table', compact('delivery_men'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function reviews_list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = $this->delivery_man->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $reviews = $this->dm_review->whereIn('delivery_man_id', $delivery_men);
            $query_param = ['search' => $request['search']];
        } else {
            $reviews = $this->dm_review;
        }

        $reviews = $reviews->with(['delivery_man', 'customer'])->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.delivery-man.reviews-list', compact('reviews', 'search'));
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function preview($id): Renderable
    {
        $dm = $this->delivery_man->with(['reviews'])->where(['id' => $id])->first();
        $reviews = $this->dm_review->where(['delivery_man_id' => $id])->latest()->paginate(Helpers::getPagination());

        return view('admin-views.delivery-man.view', compact('dm', 'reviews'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
            'confirm_password' => 'same:password'
        ], [
            'f_name.required' => translate('First name is required!')
        ]);

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $id_img_names[] = Helpers::upload('delivery-man/', 'png', $img);
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
        $dm->image = Helpers::upload('delivery-man/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->application_status= 'approved';
        $dm->language_code = $request->language_code ?? 'en';
        $dm->save();

        try{
            //send email
            $emailServices = Helpers::get_business_settings('mail_config');
            $mail_status = Helpers::get_business_settings('registration_mail_status_dm');
            if(isset($emailServices['status']) && $emailServices['status'] == 1 && $mail_status == 1){
                Mail::to($dm->email)->send(new DMSelfRegistration('approved', $dm->f_name.' '.$dm->l_name, $dm->language_code));
            }

        }catch(\Exception $ex){
            info($ex);
        }

        Toastr::success(translate('Delivery-man added successfully!'));
        return redirect('admin/delivery-man/list');
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $delivery_man = $this->delivery_man->find($id);
        return view('admin-views.delivery-man.edit', compact('delivery_man'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajax_is_active(Request $request): JsonResponse
    {
        $delivery_man = $this->delivery_man->find($request->id);
        $delivery_man->is_active = $request->status;
        $delivery_man->save();

        return response()->json(translate('status changed successfully'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
        ], [
            'f_name.required' => translate('First name is required!')
        ]);

        if ($request->password) {
            $request->validate([
                'confirm_password' => 'same:password'
            ]);
        }

        $delivery_man = $this->delivery_man->find($id);

        if ($delivery_man['email'] != $request['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men',
            ]);
        }

        if ($delivery_man['phone'] != $request['phone']) {
            $request->validate([
                'phone' => 'required|unique:delivery_men',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($delivery_man['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                $img_keeper[] = Helpers::upload('delivery-man/', 'png', $img);
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $delivery_man['identity_image'];
        }
        $delivery_man->f_name = $request->f_name;
        $delivery_man->l_name = $request->l_name;
        $delivery_man->email = $request->email;
        $delivery_man->phone = $request->phone;
        $delivery_man->identity_number = $request->identity_number;
        $delivery_man->identity_type = $request->identity_type;
        $delivery_man->branch_id = $request->branch_id;
        $delivery_man->identity_image = $identity_image;
        $delivery_man->image = $request->has('image') ? Helpers::update('delivery-man/', $delivery_man->image, 'png', $request->file('image')) : $delivery_man->image;
        $delivery_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $delivery_man['password'];
        $delivery_man->save();

        Toastr::success(translate('Delivery-man updated successfully!'));
        return redirect('admin/delivery-man/list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $delivery_man = $this->delivery_man->find($request->id);
        if (Storage::disk('public')->exists('delivery-man/' . $delivery_man['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $delivery_man['image']);
        }

        foreach (json_decode($delivery_man['identity_image'], true) as $img) {
            if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                Storage::disk('public')->delete('delivery-man/' . $img);
            }
        }
        $delivery_man->delete();

        Toastr::success(translate('Delivery-man removed!'));
        return back();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function excel_export(): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $dm = $this->delivery_man->where('application_status', 'approved')->select('f_name as First Name', 'l_name as Last Name', 'phone as Phone', 'identity_type', 'identity_number')->get();
        return (new FastExcel($dm))->download('delivery_men.xlsx');

    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function pending_list(Request $request): Factory|View|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $delivery_men = $this->delivery_man->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $delivery_men = $this->delivery_man;
        }
        $delivery_men = $delivery_men->where('application_status', 'pending')->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.delivery-man.pending-list', compact('delivery_men','search'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function denied_list(Request $request): Factory|View|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $delivery_men = $this->delivery_man->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $delivery_men = $this->delivery_man;
        }
        $delivery_men = $delivery_men->where('application_status', 'denied')->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.delivery-man.denied-list', compact('delivery_men','search'));
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_application(Request $request): RedirectResponse
    {
        $delivery_man = $this->delivery_man->findOrFail($request->id);
        $delivery_man->application_status = $request->status;

        if ($request->status == 'approved') {
            $delivery_man->is_active = 1;
        }

        $delivery_man->save();

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            $approved_mail_status = Helpers::get_business_settings('approve_mail_status_dm');
            $denied_mail_status = Helpers::get_business_settings('deny_mail_status_dm');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                $mailType = ($request->status == 'approved') ? 'approved' : 'denied';
                $fullName = $delivery_man->f_name . ' ' . $delivery_man->l_name;
                $languageCode = $delivery_man->language_code;
                if ($mailType == 'approved' && $approved_mail_status == 1){
                    Mail::to($delivery_man->email)->send(new DMSelfRegistration($mailType, $fullName, $languageCode));
                }
                if ($mailType == 'denied' && $denied_mail_status == 1){
                    Mail::to($delivery_man->email)->send(new DMSelfRegistration($mailType, $fullName, $languageCode));
                }
            }
        } catch (\Exception $ex) {
            info($ex);
        }

        Toastr::success(translate('application_status_updated_successfully'));
        return back();
    }

}
