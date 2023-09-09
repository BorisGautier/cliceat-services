<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Rap2hpoutre\FastExcel\FastExcel;

class CustomRoleController extends Controller
{
    public function __construct(
        private AdminRole $admin_role,
        private Admin     $admin
    )
    {
    }

    public function create(Request $request): Renderable
    {
        $search = $request['search'];
        $rl = $this->admin_role
            ->whereNotIn('id', [1])
            ->when($search, function ($query) use ($search) {
                $params = explode(' ', $search);
                foreach ($params as $param) {
                    $query->where('name', 'like', "%" . $param . "%");
                }
            })
            ->latest()->get();

        return view('admin-views.custom-role.create', compact('rl'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:admin_roles',
        ], [
            'name.required' => translate('Role name is required!')
        ]);

        if ($request['modules'] == null) {
            Toastr::error(translate('Select at least one module permission'));
            return back();
        }

        $this->admin_role->insert([
            'name' => $request->name,
            'module_access' => json_encode($request['modules']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success(translate('Role added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $role = $this->admin_role->where(['id' => $id])->first(['id', 'name', 'module_access']);
        return view('admin-views.custom-role.edit', compact('role'));
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
        ], [
            'name.required' => translate('Role name is required!')
        ]);

        $this->admin_role->where(['id' => $id])->update([
            'name' => $request->name,
            'module_access' => json_encode($request['modules']),
            'status' => 1,
            'updated_at' => now()
        ]);

        Toastr::success(translate('Role updated successfully!'));
        return redirect(route('admin.custom-role.create'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $role_exist = $this->admin->where('admin_role_id', $request->id)->first();
        if ($role_exist) {
            Toastr::warning(translate('employee_assigned_on_this_role._Delete_failed'));
        } else {
            $action = $this->admin_role->destroy($request->id);
            if ($action) {
                Toastr::success(translate('role_deleted_sucessfully'));
            } else {
                Toastr::warning(translate('delete_failed'));
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
        $roles = $this->admin_role->select('id', 'name', 'module_access', 'status')->get();
        return (new FastExcel($roles))->download('employee_role.xlsx');
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function status_change($id, Request $request): JsonResponse
    {
        $role_exist = $this->admin->where('admin_role_id', $id)->first();

        if ($role_exist) {
            return response()->json(translate('employee_assigned_on_this_role._Update_failed'), 409);
        } else {
            $action = $this->admin_role->where('id', $id)->update(['status' => $request['status']]);
            if ($action) {
                return response()->json(translate('status_changed_successfully'), 200);
            } else {
                return response()->json(translate('status_update_failed'), 500);
            }
        }
    }
}
