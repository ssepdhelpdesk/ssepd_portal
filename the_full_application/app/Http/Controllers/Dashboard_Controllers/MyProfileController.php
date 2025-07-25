<?php

namespace App\Http\Controllers\Dashboard_Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use File;
use Input;

class MyProfileController extends Controller
{
    function __construct()
    {
       $this->middleware('permission:my-profile-list|my-profile-create|my-profile-edit|my-profile-delete', ['only' => ['index','show', 'changePassword']]);
       $this->middleware('permission:my-profile-create', ['only' => ['create','store']]);
       $this->middleware('permission:my-profile-edit', ['only' => ['edit','update', 'changePasswordStore']]);
       $this->middleware('permission:my-profile-delete', ['only' => ['destroy']]);
   }

/**
* Display a listing of the resource.
*/
public function index(Request $request)
{
    $user = Auth::user();
    $ip = $request->ip();
    $currentUserInfo = Location::get($ip) ?? 'Location not available';
    return view('dashboard.users.profile', compact('user', 'currentUserInfo'));
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
public function edit(string $id)
{
//
}

/**
* Update the specified resource in storage.
*/
public function update(Request $request, string $id)
{
    $user = User::whereId($id)->firstOrFail();
    $validated = $request->validate([
        'name'=>'required',
        'email' => 'required|email|unique:users,email,'.$user->id.',id',
        'number' => 'digits:10|regex:/^([0-9\s\-\+\(\)]*)$/',
    ]);
    if($request->password != null){
        $request->validate([
            'password' => 'required|confirmed'
        ]);
        $validated['password'] = bcrypt($request->password);
    }

    if ($request->has('profile_photo')) {
        $destination = $user->profile_photo_path;

        if (File::exists($destination)) {
            File::delete($destination);
        }

        $image = $request->file('profile_photo');
        $profile_photo_image = strtolower(str_replace(' ', '_', $request->name)) . date('Ymd') . '_' . $image->getClientOriginalName();
        $profile_photo_path = $image->storeAs('profile-photos', $profile_photo_image, 'public');

        $image->move(public_path('profile-photos'), $profile_photo_image);
        $externalBasePath = dirname(base_path());
        $externalPath = $externalBasePath . "/storage/profile-photos";

        if (!File::exists($externalPath)) {
            File::makeDirectory($externalPath, 0755, true);
        }

        File::copy(public_path("profile-photos/{$profile_photo_image}"), $externalPath . '/' . $profile_photo_image);

        $user->profile_photo = $profile_photo_image;
        $user->profile_photo_path = "storage/" . $profile_photo_path;
        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
    } else {
        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
    }

    $user->save();
    return redirect()->back()->with('success','User updated successfully');
}

/**
* Remove the specified resource from storage.
*/
public function destroy(string $id)
{
//
}

public function changePassword(){
    $user = Auth::user();
    return view('dashboard.users.changepassword',compact('user'));
}

public function changePasswordStore(Request $request)
{
    $user = Auth::user();
    $request->validate([
        'oldpassword' => 'required',
        'password' => 'required|min:8',
        'password_confirm' => 'required|same:password',
    ], [
        'password_confirm.same' => 'New password and confirmation do not match.',
    ]);
    if (!Hash::check($request->oldpassword, $user->password)) {
        return back()->withErrors(['oldpassword' => 'The current password is incorrect.']);
    }
    $user->password = bcrypt($request->password);
    $user->save();
    Auth::logout();
    return redirect()->route('login')->with('success', 'Password changed successfully. Please log in again with your new password.');
}
}
