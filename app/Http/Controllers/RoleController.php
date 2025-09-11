<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('users')->get();
        return Inertia::render('Role/Index', ['roles' => RoleResource::collection($roles)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data =  $request->validate([
            'role_name' => 'required|string'
        ]);

        $data['slug'] = Str::slug($request->role_name);

        Role::create($data);


        Session::flash('message', 'New role has been added.');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|string|min:4'
        ]);
        $role->update([
            'role_name' => $request->role_name,
            'slug' => Str::slug($request->role_name)
        ]);


        Session::flash('message', 'Role has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        Session::flash('message', 'Role has been deleted.');
    }
}
