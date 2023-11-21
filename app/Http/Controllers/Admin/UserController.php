<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'string|max:255|required',
        ]);
        try {
            DB::beginTransaction();
            $user->update($request->only(['name', 'cellphone']));
            $user->syncRoles($request->role);
            $permissions = $request->except(['_token', 'cellphone', 'role', 'name', '_method']);
            $user->syncPermissions($permissions);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ویرایش کاربر', $ex->getMessage());
            return redirect()->back();
        }

        alert()->success('موفق', 'کاربر با موفقیت ویرایش شد');
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
