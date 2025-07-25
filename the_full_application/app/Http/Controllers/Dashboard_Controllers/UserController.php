<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use DataTables;

class UserController extends Controller
{
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
function __construct()
{
    $this->middleware('permission:user-access|user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
    $this->middleware('permission:user-create', ['only' => ['create','store']]);
    $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
    $this->middleware('permission:user-delete', ['only' => ['destroy']]);
}

/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index(Request $request)
{
    if ($request->ajax()) {
        $users = User::with('roles')->select('users.*');

        return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('roles', function ($user) {
            return $user->roles->pluck('name')->map(function ($role) {
                return "<label class='badge bg-success'>$role</label>";
            })->implode(' ');
        })
        ->addColumn('action', function ($user) {
            $actions = '';
            if (auth()->user()->can('user-show')) {
                $actions .= "<a href='" . route('admin.users.show', $user->id) . "'><span class='label label-warning'>View</span></a> ";
            }
            if (auth()->user()->can('user-edit')) {
                $actions .= "<a href='" . route('admin.users.edit', $user->id) . "'><span class='label label-success'>Edit</span></a> ";
            }
            if (auth()->user()->can('user-delete')) {
                $actions .= "<a href='" . route('admin.users.destroy', $user->id) . "' id='delete'><span class='label label-danger'>Delete</span></a>";
            }
            return $actions;
        })
        ->rawColumns(['roles', 'action'])
        ->make(true);
    }
    return view('dashboard.users.index');
}   
/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/
public function create(): View
{
    $roles = Role::pluck('name','name')->all();
    return view('dashboard.users.create',compact('roles'));
}

/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function store(Request $request): RedirectResponse
{
    $this->validate($request, [
        'name' => 'required',
        'user_id' => 'required|unique:users,user_id',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|same:confirm-password',
        'roles' => 'required|array|min:1'
    ]);

    $input = $request->all();

    $input['password'] = Hash::make($input['password']);
    $roleNames = $request->input('roles');
    $roles = Role::whereIn('name', $roleNames)->get();

    if (!$roles) {
        return redirect()->back()->with('error', 'Invalid role selected.');
    }

    $input['role_id'] = $roles->pluck('id')->implode(',');
    $input['role_name'] = $roles->pluck('name')->implode(',');
    $user = User::create($input);
    $user->assignRole($request->input('roles'));

    return redirect()->route('admin.users.index')
    ->with('success','User created successfully');
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function show($id): View
{
    $user = User::find($id);
    return view('dashboard.users.show',compact('user'));
}

/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function edit($id): View
{
    $user = User::find($id);
    $roles = Role::pluck('name','name')->all();
    $userRole = $user->roles->pluck('name','name')->all();

    return view('dashboard.users.edit',compact('user','roles','userRole'));
}

/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function update(Request $request, $id): RedirectResponse
{
    $this->validate($request, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email,'.$id,
        'password' => 'same:confirm-password',
        'roles' => 'required'
    ]);

    $input = $request->all();
    if(!empty($input['password'])){ 
        $input['password'] = Hash::make($input['password']);
    }else{
        $input = Arr::except($input,array('password'));    
    }

    $roleNames = $request->input('roles');
    $roles = Role::whereIn('name', $roleNames)->get();

    if (!$roles) {
        return redirect()->back()->with('error', 'Invalid role selected.');
    }

    $input['role_id'] = $roles->pluck('id')->implode(',');
    $input['role_name'] = $roles->pluck('name')->implode(',');

    $user = User::find($id);
    $user->update($input);
    DB::table('model_has_roles')->where('model_id',$id)->delete();

    $user->assignRole($request->input('roles'));

    return redirect()->route('admin.users.index')
    ->with('success','User updated successfully');
}

/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function destroy($id): RedirectResponse
{
    $user = User::find($id);
    if ($user->hasRole('SuperAdmin')) {
        return redirect()->route('admin.users.index')
        ->with('error', 'SuperAdmin users cannot be deleted.');
    }

    $user->delete();
    return redirect()->route('admin.users.index')
    ->with('success', 'User deleted successfully');
}
}