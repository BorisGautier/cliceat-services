<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        private Branch $branch
    )
    {
    }


    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('admin-views.branch.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255|unique:branches',
            'email' => 'required|max:255|unique:branches',
            'password' => 'required|min:8|max:255',
            'image' => 'required|max:255',
        ], [
            'name.required' => translate('Name is required!'),
        ]);

        //image upload
        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('branch/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        if (!empty($request->file('cover_image'))) {
            $cover_image_name = Helpers::upload('branch/', 'png', $request->file('cover_image'));
        } else {
            $cover_image_name = 'def.png';
        }

        $branch = $this->branch;
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ?? 0;
        $branch->address = $request->address;
        $branch->phone = $request->phone ?? null;
        $branch->password = bcrypt($request->password);
        $branch->image = $image_name;
        $branch->cover_image = $cover_image_name;
        $branch->save();

        Toastr::success(translate('Branch added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $branch = $this->branch->find($id);
        return view('admin-views.branch.edit', compact('branch'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'unique:branches,email,' . $id . ',id']
        ], [
            'name.required' => translate('Name is required!'),
        ]);

        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ], [
            'name.required' => translate('Name is required!'),
        ]);

        $branch = $this->branch->find($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ?? 0;
        $branch->address = $request->address;
        $branch->image = $request->has('image') ? Helpers::update('branch/', $branch->image, 'png', $request->file('image')) : $branch->image;
        $branch->cover_image = $request->has('cover_image') ? Helpers::update('branch/', $branch->cover_image, 'png', $request->file('cover_image')) : $branch->cover_image;
        if ($request['password'] != null) {
            $branch->password = bcrypt($request->password);
        }
        $branch->phone = $request->phone ?? '';
        $branch->save();

        Toastr::success(translate('Branch updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->delete();

        Toastr::success(translate('Branch removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->status = $request->status;
        $branch->save();

        Toastr::success(translate('Branch status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    public function list(Request $request): Renderable
    {
        $search = $request['search'];
        $query = $this->branch
            ->when($search, function ($q) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            });

        $query_param = ['search' => $request['search']];
        $branches = $query->orderBy('id', 'DESC')->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.branch.list', compact('branches', 'search'));
    }
}
