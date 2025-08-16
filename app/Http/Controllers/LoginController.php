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
        $request->validate([
            'nic' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('nic');

        // Allow login via email or NIC
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } else {
            $patient = Patient::where('nic', $identifier)->first();
            $user = $patient ? User::find($patient->user_id) : null;
        }

        $password = $request->input('password');

        if ($user && Hash::check($password, $user->password)) {
            auth()->login($user, $request->boolean('remember'));

            switch ($user->type) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'doctor':
                    return redirect()->route('doctor.dashboard');
                case 'staff':
                    // Adjust if you add a staff dashboard later
                    return redirect()->route('dashboard');
                default:
                    // Patients and any other types
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'nic' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('nic', 'remember'));
    }
}
