<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

class PagesController extends Controller
{

    /**
     * Main page entry point.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showIndex()
    {
        return view('pages/home');
    }

    /**
     * Show the login form to the user.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return \Redirect::to('/');
        }

        return view('auth/login');
    }

    /**
     * Log the user in
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        // User is NOT logged in
        if (!Auth::check()) {
            $username = $request->input('username');
            $password = $request->input('password');

            if (!Auth::attempt(['username' => $username, 'password' => $password], true))
            {
                return \Redirect::back()
                    ->withErrors(trans('messages.loginCombo'));
            }
        }

        return \Redirect::to('/');
    }


    /**
     * Log the user out
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        // @todo clean any existing tokens on logout
        // ...

        Auth::logout();

        return redirect('/');
    }

    public function showRegister()
    {
        // If user is connected we redirect it to homepage
        if (!Auth::check()) {
            return view('auth/register');
        }

        return redirect('/');
    }


    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users',
            'username' => 'required|unique:users,username|email|min:3',
            'password' => 'required|confirmed|min:5'
        ], User::getFormMessages());

        User::create($request->all());

        return Redirect::to('/admin/users');
    }

}
