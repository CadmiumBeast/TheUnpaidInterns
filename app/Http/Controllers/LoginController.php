<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function showForm()
    {
         return view('components.layouts.app.login');
    }

    public function login(Request $request)
    {
        $nic = $request->input('nic');
        $password = $request->input('password');

        $patient = Patient::where('nic', $nic)->first();

        $user = User::where('id', $patient->user_id)->first();

        if ($user && Hash::check($password, $user->password)) {
            auth()->login($user);
            return redirect()->intended('register');
        }

        return back()->withErrors([
            'nic' => 'The provided credentials do not match our records.',
        ]);
    }
}
