<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
//use Barryvdh\DomPDF\Facade as PDF;



class QRCodeController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
        private Branch $branch
    )
    {}

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $branches = $this->branch->all();
        $data = Helpers::get_business_settings('qr_code');
        $qr = base64_encode(json_encode($data));
        $code = QrCode::size(180)->generate($data['website'].'?qrcode='.$qr);
        return view('admin-views.business-settings.qrcode-index', compact('branches', 'data', 'code'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'phone' => 'required',
            'website' => 'required',
            'social_media' => 'required',
        ]);

        $curr_logo = Helpers::get_business_settings('qr_code');
        $logo = $request->file('logo') ? Helpers::update('qrcode/', $curr_logo['logo']??'','png', $request->file('logo')) : $curr_logo['logo']??'';

        $data = [];

        $data['branch_id'] = $request->branch_id;
        $data['logo'] = $logo;
        $data['title'] = $request->title;
        $data['description'] = $request->description;
        $data['opening_time'] = $request->opening_time;
        $data['closing_time'] = $request->closing_time;
        $data['phone'] = $request->phone;
        $data['website'] = $request->website;
        $data['social_media'] = $request->social_media;

        $this->business_setting->updateOrInsert(['key' => 'qr_code'], [
            'value' => json_encode($data),
        ]);

        Toastr::success(translate('updated successfully'));
        return back();

    }

    /**
     * @return Response
     */
    public function download_pdf(): Response
    {
        $data = Helpers::get_business_settings('qr_code');
        $qr = base64_encode(json_encode($data));
        $code = QrCode::size(180)->generate($data['website'].'?qrcode='.$qr);
        $pdf = PDF::loadView('admin-views.business-settings.partials.qrcode-pdf', compact('data', 'code'));
        return $pdf->download('qr-code' . rand(00001, 99999) . '.pdf');
    }

    public function print_qrcode()
    {
        $data = Helpers::get_business_settings('qr_code');
        $qr = base64_encode(json_encode($data));
        $code = QrCode::size(180)->generate($data['website'].'?qrcode='.$qr);
        return view('admin-views.business-settings.partials.qrcode-print', compact('data', 'code'));
    }

}
