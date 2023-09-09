<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:branch', ['except' => ['logout']]);
    }

    /**
     * @param $tmp
     * @return void
     */
    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if (Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        Session::put('default_captcha_code_branch', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    /**
     * @return Renderable
     */
    public function login(): Renderable
    {
        return view('branch-views.auth.login');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        //recaptcha validation
        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;
                        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                        $response = \file_get_contents($url);
                        $response = json_decode($response);
                        if (!$response->success) {
                            $fail(translate('ReCAPTCHA Failed'));
                        }
                    },
                ],
            ]);
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code_branch'))) {
                return back()->withErrors(translate('Captcha Failed'));
            }
        }

        if (Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        //end recaptcha validation

        if (auth('branch')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ], $request->remember)) {
            return redirect()->route('branch.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('Credentials does not match.')]);
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth()->guard('branch')->logout();
        return redirect()->route('branch.auth.login');
    }
}
