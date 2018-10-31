<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use \Woo\GridView\DataProviders\EloquentDataProvider;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index', [
            'dataProvider' => new EloquentDataProvider(User::query())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('users/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'    =>'required|email|unique:users,email,'. $request['userId'],
            'password' => 'required_with:password_confirmation|same:password_confirmation',
        ]);

        if($request['editUser']) {
            $newData = [
                'name' 	  =>  $request['name'],
                'email' =>  $request['email'],
            ];
            if($request['password']) {
                $newData = array_merge(['password' => Hash::make($request['password'])],$newData);
            }
            User::where('id', $request['userId'])->update($newData);
        } else {
            User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
        }

        return Redirect::to('users')->with('dataProvider',new EloquentDataProvider(User::query()));
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $users = User::findOrFail($user->id);
        $users->delete();

        return Redirect::to('users')->with('dataProvider',new EloquentDataProvider(User::query()));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $users = User::findOrFail($user->id);
        return View::make("users/edit")
            ->with("users", $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
