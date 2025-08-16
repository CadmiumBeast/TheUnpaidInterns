<?php
namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class RegistrationController extends Controller
{
    public function showForm()
    {
        return view('components.layouts.app.Registration');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'nic' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'district' => 'required|string',
            'hospital' => 'required|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user =  User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'type' => 'patient',
            ]);

            Patient::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'nic' => $validated['nic'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'emergency_contact' => $validated['emergency_contact'],
                'address' => $validated['address'],
                'district' => $validated['district'],
                'hospital' => $validated['hospital'],
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $user->id,
            ]);
        });

        Session::flash('success', 'Patient registered successfully!');
        return redirect()->route('login');
    }
}
