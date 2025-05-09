<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(20);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255|required',
            'display_name' => 'string|max:255|required',
        ]);

        Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'guard_name' => 'web'
        ]);
        alert()->success('موفق', 'مجوز با موفقیت ثبت شد');
        return redirect()->route('admin.permissions.index');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'string|max:255|required',
            'display_name' => 'string|max:255|required',
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
        ]);
        alert()->success('موفق', 'مجوز با موفقیت ویرایش شد');
        return redirect()->route('admin.permissions.index');
    }
}
