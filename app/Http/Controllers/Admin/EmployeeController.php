<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class EmployeeController extends Controller
{
    public function __construct(
        private Admin     $admin,
        private AdminRole $admin_role
    ){}

    /**
     * @return Renderable
     */
    public function add_new(): Renderable
    {
        $rls = $this->admin_role->whereNotIn('id', [1])->get();
        return view('admin-views.employee.add-new', compact('rls'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'image' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required',
            'phone' => 'required',
            'identity_image' => 'required',
            'identity_type' => 'required',
            'identity_number' => 'required',
            'confirm_password' => 'same:password'
        ], [
            'name.required' => translate('Role name is required!'),
            'role_name.required' => translate('Role id is Required'),
            'email.required' => translate('Email id is Required'),
            'image.required' => translate('Image is Required'),

        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('Access Denied!'));
            return back();
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $id_img_names[] = Helpers::upload('admin/', 'png', $img);
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $this->admin->insert([
            'f_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'admin_role_id' => $request->role_id,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identity_image,
            'password' => bcrypt($request->password),
            'status' => 1,
            'image' => Helpers::upload('admin/', 'png', $request->file('image')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Employee added successfully!'));
        return redirect()->route('admin.employee.list');
    }

    /**
     * @param Request $request
     * @return Renderable
     */
    function list(Request $request): Renderable
    {
        $search = $request['search'];
        $key = explode(' ', $request['search']);

        $query = $this->admin->with(['role'])
            ->when($search != null, function ($query) use ($key) {
                $query->whereNotIn('id', [1])->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            }, function ($query) {
                $query->whereNotIn('id', [1]);
            });

        $em = $query->paginate(Helpers::getPagination());

        return view('admin-views.employee.list', compact('em', 'search'));
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $e = $this->admin->where(['id' => $id])->first();
        $rls = $this->admin_role->whereNotIn('id', [1])->get();

        return view('admin-views.employee.edit', compact('rls', 'e'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            'phone' => 'required',
            'identity_type' => 'required',
            'identity_number' => 'required',
        ], [
            'name.required' => translate('Role name is required!'),
        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('Access Denied!'));
            return back();
        }

        $e = $this->admin->find($id);
        $identity_image = $e['identity_image'];

        if ($request['password'] == null) {
            $pass = $e['password'];
        } else {
            $request->validate([
                'confirm_password' => 'same:password'
            ]);
            if (strlen($request['password']) < 7) {
                Toastr::warning(translate('Password length must be 8 character.'));
                return back();
            }
            $pass = bcrypt($request['password']);
        }


        if ($request->has('image')) {
            $e['image'] = Helpers::update('admin/', $e['image'], 'png', $request->file('image'));
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $id_img_names[] = Helpers::upload('admin/', 'png', $img);
            }
            $identity_image = json_encode($id_img_names);
        }

        $this->admin->where(['id' => $id])->update([
            'f_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'admin_role_id' => $request->role_id,
            'password' => $pass,
            'image' => $e['image'],
            'updated_at' => now(),
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identity_image,
        ]);

        Toastr::success(translate('Employee updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $employee = $this->admin->find($request->id);
        $employee->status = $request->status;
        $employee->save();

        Toastr::success(translate('Employee status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        if ($request->id == 1) {
            Toastr::warning(translate('Master_Admin_can_not_be_deleted'));

        } else {
            $action = $this->admin->destroy($request->id);
            if ($action) {
                Toastr::success(translate('employee_deleted_successfully'));
            } else {
                Toastr::error(translate('employee_is_not_deleted'));
            }
        }

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
        $employees = $this->admin->whereNotIn('id', [1])->get(['id', 'f_name', 'l_name', 'email', 'admin_role_id', 'status']);
        return (new FastExcel($employees))->download('employees.xlsx');
    }
}
