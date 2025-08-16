<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $pharmacies = Pharmacy::with('medicines')->get();
        return view('components.layouts.app.Medicine', compact('pharmacies'));
    }
}
