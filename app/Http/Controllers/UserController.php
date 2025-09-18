<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $users = User::query()
            ->when($request->role, function ($query, $role) {
                $query->whereHas('role', function ($queryRole) use ($role) {
                    $queryRole->where('slug', $role);
                });
            })
            ->when($request->search, function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->paginate(10)
            ->withQueryString();

        $roles = Role::query()->get(['id', 'role_name', 'slug']);
        return Inertia::render(
            'User/Index',
            [
                'users' => UserResource::collection($users),
                'roles' => $roles
            ]
        );
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:7', 'confirmed'],
            'role_id' => ['required']
        ]);


        User::create($validateData);
        Session::flash('message', 'Success create user!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => "required|string",
            'email' => "required|email|" . Rule::unique('users', 'email')->ignore($id),
            'role_id' => 'required'
        ]);

        User::whereId($id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ]);

        Session::flash('message', 'User data has been Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }

        Session::flash('message', 'Data has been deleted!');
    }
}
