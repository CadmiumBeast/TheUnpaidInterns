<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $complaints = collect();

        if ($user->type === 'patient') {
            $complaints = Complaint::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif (in_array($user->type, ['admin', 'staff', 'doctor'])) {
            $complaints = Complaint::byDepartment($user->type)
                ->with(['user', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $categories = [
            'general' => 'General Inquiry',
            'billing' => 'Billing Issue',
            'appointment' => 'Appointment Problem',
            'treatment' => 'Treatment Concern',
            'facility' => 'Facility Issue',
            'staff' => 'Staff Behavior',
            'other' => 'Other'
        ];

        return view('complaints.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'user_id' => Auth::id(),
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'new'
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('complaints', 'public');
            $data['photo_path'] = $path;
        }

        $complaint = Complaint::create($data);

        // Auto-assign to appropriate department based on category
        $this->autoAssignComplaint($complaint);

        // Send notification to assigned department
        $this->notifyDepartment($complaint);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted successfully!');
    }

    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        
        return view('complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed'
        ]);

        $complaint->update([
            'status' => $request->status,
            'resolved_at' => $request->status === 'resolved' ? now() : null
        ]);

        // Notify patient about status update
        $this->notifyPatient($complaint);

        return back()->with('success', 'Complaint status updated successfully!');
    }

    public function submitFeedback(Request $request, Complaint $complaint)
    {
        $this->authorize('feedback', $complaint);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|min:5'
        ]);

        $complaint->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback
        ]);

        return back()->with('success', 'Feedback submitted successfully!');
    }

    private function autoAssignComplaint(Complaint $complaint)
    {
        $categoryMapping = [
            'billing' => 'admin',
            'appointment' => 'staff',
            'treatment' => 'doctor',
            'facility' => 'staff',
            'staff' => 'admin',
            'general' => 'staff',
            'other' => 'staff'
        ];

        $department = $categoryMapping[$complaint->category] ?? 'staff';
        
        // Find first available user in the department
        $assignedUser = User::where('type', $department)->first();
        
        if ($assignedUser) {
            $complaint->update(['assigned_to' => $assignedUser->id]);
        }
    }

    private function notifyDepartment(Complaint $complaint)
    {
        // This would integrate with your notification service
        // For now, we'll just log it
        \Log::info("New complaint assigned to department", [
            'complaint_id' => $complaint->id,
            'department' => $complaint->assignedUser?->type
        ]);
    }

    private function notifyPatient(Complaint $complaint)
    {
        // This would integrate with your notification service
        // For now, we'll just log it
        \Log::info("Patient notified about complaint status update", [
            'complaint_id' => $complaint->id,
            'patient_email' => $complaint->user->email
        ]);
    }
}
