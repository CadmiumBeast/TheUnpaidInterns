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
        //check whether the input is a mail
        if (filter_var($nic, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $nic)->first();
        } else {
            $patient = Patient::where('nic', $nic)->first();
            $user = User::where('id', $patient->user_id)->first();
        }

        $password = $request->input('password');


        if ($user && Hash::check($password, $user->password)) {
            auth()->login($user);
            if($user->type == 'admin'){
                return redirect()->route('admin.dashboard')->with('success','');
            }else if($user->type == 'doctor'){
                return redirect()->route('doctor.dashboard')->with('success','');
            }
            else if($user->type == 'patient'){
                return redirect()->route('medicine')->with('success','');
            } else if($user->type == 'staff'){
                return redirect()->route('staff.dashboard')->with('success','');
            }
        }

        return back()->withErrors([
            'nic' => 'The provided credentials do not match our records.',
        ]);
    }
}
