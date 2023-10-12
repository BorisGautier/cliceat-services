<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class LanguageController extends Controller
{
    public function __construct(private BusinessSetting $business_setting,)
    {
    }


    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('admin-views.business-settings.language.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ],[
            'code' => translate('Country code select is required'),
        ]);

        $language = Helpers::get_business_settings('language');
        if (!isset($language)) {
            $this->business_setting->updateOrInsert(['key' => 'language'], [
                'value' => '[{"id":"1","name":"english","direction":"ltr","code":"en","status":1,"default":true}]'
            ]);
            $language = Helpers::get_business_settings('language');
        }

        $lang_array = [];
        $codes = [];
        foreach ($language as $key => $data) {
            if ($data['code'] != $request['code']) {
                if (!array_key_exists('default', $data)) {
                    $default = array('default' => ($data['code'] == 'en') ? true : false);
                    $data = array_merge($data, $default);
                }
                $lang_array[] = $data;
                $codes[] = $data['code'];
            }
        }
        $codes[] = $request['code'];

        if (!file_exists(base_path('resources/lang/' . $request['code']))) {
            mkdir(base_path('resources/lang/' . $request['code']), 0777, true);
        }

        $lang_file = fopen(base_path('resources/lang/' . $request['code'] . '/' . 'messages.php'), "w") or die("Unable to open file!");
        $read = file_get_contents(base_path('resources/lang/en/messages.php'));
        fwrite($lang_file, $read);

        $lang_array[] = [
            'id' => count($language) + 1,
            'name' => $request['name'],
            'code' => $request['code'],
            //'direction' => $request['direction'],
            'direction' => 'ltr',   //since no rtl in version 1.0
            'status' => 0,
            'default' => false,
        ];

        $this->business_setting->updateOrInsert(['key' => 'language'], [
            'value' => $lang_array
        ]);

        Toastr::success(translate('Language Added!'));
        return back();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update_status(Request $request): mixed
    {
        $language = Helpers::get_business_settings('language');
        $lang_array = [];
        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'] == 1 ? 0 : 1,
                    'default' => (array_key_exists('default', $data) ? $data['default'] : (($data['code'] == 'en') ? true : false)),
                ];
                $lang_array[] = $lang;
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => (array_key_exists('default', $data) ? $data['default'] : (($data['code'] == 'en') ? true : false)),
                ];
                $lang_array[] = $lang;
            }
        }
        $businessSetting = $this->business_setting->where('key', 'language')->update([
            'value' => $lang_array
        ]);

        return $businessSetting;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_default_status(Request $request): RedirectResponse
    {
        $language = Helpers::get_business_settings('language');
        $lang_array = [];

        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => 1,
                    'default' => true,
                ];
                $lang_array[] = $lang;
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => false,
                ];
                $lang_array[] = $lang;
            }
        }

        $this->business_setting->where('key', 'language')->update([
            'value' => $lang_array
        ]);

        Toastr::success(translate('Default Language Changed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $language = Helpers::get_business_settings('language');
        $lang_array = [];
        foreach ($language as $key => $data) {
            if ($data['code'] == $request['code']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $request['name'],
                    'direction' => $request['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'] ?? 0,
                    'default' => (array_key_exists('default', $data) ? $data['default'] : (($data['code'] == 'en') ? true : false)),
                ];
                $lang_array[] = $lang;
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => (array_key_exists('default', $data) ? $data['default'] : (($data['code'] == 'en') ? true : false)),
                ];
                $lang_array[] = $lang;
            }
        }
        $this->business_setting->where('key', 'language')->update([
            'value' => $lang_array
        ]);

        Toastr::success(translate('Language updated!'));
        return back();
    }

    /**
     * @param $lang
     * @return Renderable
     */
    public function translate($lang): Renderable
    {
        $full_data = include(base_path('resources/lang/' . $lang . '/messages.php'));
        $lang_data = [];
        ksort($full_data);
        foreach ($full_data as $key => $data) {
            $lang_data[] = ['key' => $key, 'value' => $data];
        }

        return view('admin-views.business-settings.language.translate', compact('lang', 'lang_data'));
    }

    /**
     * @param Request $request
     * @param $lang
     * @return void
     */
    public function translate_key_remove(Request $request, $lang): void
    {
        $full_data = include(base_path('resources/lang/' . $lang . '/messages.php'));
        unset($full_data[$request['key']]);
        $str = "<?php return " . var_export($full_data, true) . ";";
        file_put_contents(base_path('resources/lang/' . $lang . '/messages.php'), $str);
    }

    /**
     * @param Request $request
     * @param $lang
     * @return void
     */
    public function translate_submit(Request $request, $lang): void
    {
        $full_data = include(base_path('resources/lang/' . $lang . '/messages.php'));
        $full_data[urldecode($request['key'])] = $request['value'];
        $str = "<?php return " . var_export($full_data, true) . ";";
        file_put_contents(base_path('resources/lang/' . $lang . '/messages.php'), $str);
    }

    /**
     * @param $lang
     * @return RedirectResponse
     */
    public function delete($lang): RedirectResponse
    {
        $language = Helpers::get_business_settings('language');

        $del_default = false;
        foreach ($language as $key => $data) {
            if ($data['code'] == $lang && array_key_exists('default', $data) && $data['default'] == true) {
                $del_default = true;
            }
        }

        $lang_array = [];
        foreach ($language as $key => $data) {
            if ($data['code'] != $lang) {
                $lang_data = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => ($del_default == true && $data['code'] == 'en') ? 1 : $data['status'],
                    'default' => ($del_default == true && $data['code'] == 'en') ? true : (array_key_exists('default', $data) ? $data['default'] : (($data['code'] == 'en') ? true : false)),
                ];
                $lang_array[] = $lang_data;
            }
        }

        $this->business_setting->where('key', 'language')->update([
            'value' => $lang_array
        ]);

        $dir = base_path('resources/lang/' . $lang);
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);

        Toastr::success(translate('Removed Successfully!'));
        return back();
    }

    /**
     * @param $local
     * @return RedirectResponse
     */
    public function lang($local): RedirectResponse
    {
        $direction = 'ltr';
        $language = Helpers::get_business_settings('language');

        foreach ($language as $key => $data) {
            if ($data['code'] == $local) {
                $direction = $data['direction'] ?? 'ltr';
            }
        }

        session()->forget('language_settings');
        Helpers::language_load();
        session()->put('local', $local);
        Session::put('direction', $direction);

        return redirect()->back();
    }
}
