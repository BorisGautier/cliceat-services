<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Madnest\Madzipper\Facades\Madzipper;
use Illuminate\Http\RedirectResponse;


class FileManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
//    public function index($folder_path = "cHVibGlj")
//    {
//        $file = Storage::files(base64_decode($folder_path));
//        $directories = Storage::directories(base64_decode($folder_path));
//
//        $folders = FileManagerLogic::format_file_and_folders($directories, 'folder');
//        $files = FileManagerLogic::format_file_and_folders($file, 'file');
//        // dd($files);
//        $data = array_merge($folders, $files);
//        return view('admin-views.file-manager.index', compact('data', 'folder_path'));
//    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function upload(Request $request): RedirectResponse
    {
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('This option is disabled for demo.'));
            return back();
        }

        $request->validate([
            'images' => 'required_without:file',
            'file' => 'required_without:images',
            'path' => 'required',
        ]);
        if ($request->hasfile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $name = $image->getClientOriginalName();
                Storage::disk('local')->put($request->path . '/' . $name, file_get_contents($image));
            }
        }
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName();

            Madzipper::make($file)->extractTo('storage/app/' . $request->path);
            // Storage::disk('local')->put($request->path.'/'. $name, file_get_contents($file));

        }

        Toastr::success(translate('image_uploaded_successfully'));
        return back()->with('success', translate('image_uploaded_successfully'));
    }


    /**
     * @param $file_name
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($file_name): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::download(base64_decode($file_name));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id): void
    {
        //
    }


    /**
     * @param $file_path
     * @return RedirectResponse
     */
    public function destroy($file_path): RedirectResponse
    {
        Storage::disk('local')->delete(base64_decode($file_path));
        Toastr::success(translate('image_deleted_successfully'));

        return back()->with('success', translate('image_deleted_successfully'));
    }
}
