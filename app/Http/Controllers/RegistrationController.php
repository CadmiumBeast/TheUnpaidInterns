<?php
namespace App\Http\Controllers;

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
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'district' => 'required|string',
            'hospital' => 'required|string',
        ]);

        // Create a patient as a User record (we don't have a separate Patient model/table)
        User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // will be hashed by model cast
            'type' => 'patient',
        ]);

        Session::flash('success', 'Patient registered successfully!');
    return redirect()->route('login');
    }
}
