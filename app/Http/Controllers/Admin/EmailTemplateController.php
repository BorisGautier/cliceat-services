<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Models\EmailTemplate;
use App\Model\Translation;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class EmailTemplateController extends Controller
{
    public function email_index(Request $request,$type,$tab)
    {
        $template = $request->query('template',null);

        //user
        if ($tab == 'new-order') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.place-order-format',compact('template'));
        }else if ($tab == 'forgot-password') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.forgot-pass-format',compact('template'));
        }else if ($tab == 'registration-otp') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.registration-otp-format',compact('template'));
        }
        //deliveryman
        else if ($tab == 'registration') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.registration-format',compact('template'));
        } else if ($tab == 'approve') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.approve-format',compact('template'));
        } else if ($tab == 'deny') {
            return view('admin-views.business-settings.email-format-setting.'.$type.'-email-formats.deny-format',compact('template'));
        }

    }

    public function update_email_index(Request $request,$type,$tab)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }

        //user
        if ($tab == 'new-order') {
            $email_type = 'new_order';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'new_order')->first();
        }elseif($tab == 'forget-password'){
            $email_type = 'forget_password';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'forget_password')->first();
        }elseif($tab == 'registration-otp'){
            $email_type = 'registration_otp';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'registration_otp')->first();
        }

        //deliveryman
        elseif($tab == 'registration'){
            $email_type = 'registration';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'registration')->first();
        }elseif($tab == 'approve'){
            $email_type = 'approve';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'approve')->first();
        }elseif($tab == 'deny'){
            $email_type = 'deny';
            $template = EmailTemplate::where('type',$type)->where('email_type', 'deny')->first();
        }



        if ($template == null) {
            $template = new EmailTemplate();
        }
        $template->title = $request->title[array_search('default', $request->lang)];
        $template->body = $request->body[array_search('default', $request->lang)];
        $template->button_name = $request->button_name?$request->button_name[array_search('default', $request->lang)]:'';
        $template->footer_text = $request->footer_text[array_search('default', $request->lang)];
        $template->copyright_text = $request->copyright_text[array_search('default', $request->lang)];
        $template->background_image = $request->has('background_image') ? Helpers::update('email_template/', $template->background_image, 'png', $request->file('background_image')) : $template->background_image;
        $template->image = $request->has('image') ? Helpers::update('email_template/', $template->image, 'png', $request->file('image')) : $template->image;
        $template->logo = $request->has('logo') ? Helpers::update('email_template/', $template->logo, 'png', $request->file('logo')) : $template->logo;
        $template->icon = $request->has('icon') ? Helpers::update('email_template/', $template->icon, 'png', $request->file('icon')) : $template->icon;
        $template->email_type = $email_type;
        $template->type = $type;
        $template->button_url = $request->button_url??'';
        $template->email_template = $request->email_template;
        $template->privacy = $request->privacy?'1':0;
        $template->refund = $request->refund?'1':0;
        $template->cancelation = $request->cancelation?'1':0;
        $template->contact = $request->contact?'1':0;
        $template->facebook = $request->facebook?'1':0;
        $template->instagram = $request->instagram?'1':0;
        $template->twitter = $request->twitter?'1':0;
        $template->linkedin = $request->linkedin?'1':0;
        $template->pinterest = $request->pinterest?'1':0;
        $template->save();

        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if ($default_lang == $key && !($request->title[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'title'
                        ],
                        ['value'                 => $template->title]
                    );
                }
            } else {

                if ($request->title[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'title'
                        ],
                        ['value'                 => $request->title[$index]]
                    );
                }
            }
            if ($default_lang == $key && !($request->body[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'body'
                        ],
                        ['value'                 => $template->body]
                    );
                }
            } else {

                if ($request->body[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'body'
                        ],
                        ['value'                 => $request->body[$index]]
                    );
                }
            }
            if ($default_lang == $key && !($request->button_name && $request->button_name[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'button_name'
                        ],
                        ['value'                 => $template->button_name]
                    );
                }
            } else {

                if ($request->button_name && $request->button_name[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'button_name'
                        ],
                        ['value'                 => $request->button_name[$index]]
                    );
                }
            }
            if ($default_lang == $key && !($request->footer_text[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'footer_text'
                        ],
                        ['value'                 => $template->footer_text]
                    );
                }
            } else {

                if ($request->footer_text[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'footer_text'
                        ],
                        ['value'                 => $request->footer_text[$index]]
                    );
                }
            }
            if ($default_lang == $key && !($request->copyright_text[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'copyright_text'
                        ],
                        ['value'                 => $template->copyright_text]
                    );
                }
            } else {

                if ($request->copyright_text[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type'  => 'App\Models\EmailTemplate',
                            'translationable_id'    => $template->id,
                            'locale'                => $key,
                            'key'                   => 'copyright_text'
                        ],
                        ['value'                 => $request->copyright_text[$index]]
                    );
                }
            }
        }

        Toastr::success(translate('template_added_successfully'));
        return back();

    }

    public function update_email_status(Request $request,$type,$tab,$status)
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('update_option_is_disable_for_demo'));
            return back();
        }

        //user
        if ($tab == 'place-order') {
            BusinessSetting::query()->updateOrInsert(['key' => 'place_order_mail_status_'.$type], [
                'value' => $status
            ]);
        }else if ($tab == 'forgot-password') {
            BusinessSetting::query()->updateOrInsert(['key' => 'forget_password_mail_status_'.$type], [
                'value' => $status
            ]);
        }else if ($tab == 'registration-otp') {
            BusinessSetting::query()->updateOrInsert(['key' => 'registration_otp_mail_status_'.$type], [
                'value' => $status
            ]);
        }

        //deliveryman
        else if ($tab == 'registration') {
            BusinessSetting::query()->updateOrInsert(['key' => 'registration_mail_status_'.$type], [
                'value' => $status
            ]);
        } else if ($tab == 'approve') {
            BusinessSetting::query()->updateOrInsert(['key' => 'approve_mail_status_'.$type], [
                'value' => $status
            ]);
        } else if ($tab == 'deny') {
            BusinessSetting::query()->updateOrInsert(['key' => 'deny_mail_status_'.$type], [
                'value' => $status
            ]);
        }


        Toastr::success(translate('email_status_updated'));
        return back();

    }
}
