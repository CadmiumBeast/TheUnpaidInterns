<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view complaints
    }

    public function view(User $user, Complaint $complaint): bool
    {
        // Users can view their own complaints
        if ($user->id === $complaint->user_id) {
            return true;
        }

        // Staff can view complaints assigned to their department
        if (in_array($user->type, ['admin', 'staff', 'doctor'])) {
            return $complaint->assigned_to === $user->id || 
                   $complaint->assignedUser?->type === $user->type;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->type === 'patient';
    }

    public function update(User $user, Complaint $complaint): bool
    {
        // Only assigned staff can update complaint status
        if (in_array($user->type, ['admin', 'staff', 'doctor'])) {
            return $complaint->assigned_to === $user->id || 
                   $complaint->assignedUser?->type === $user->type;
        }

        return false;
    }

    public function feedback(User $user, Complaint $complaint): bool
    {
        // Only the patient who created the complaint can submit feedback
        return $user->id === $complaint->user_id && $complaint->isResolved();
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        // Only admins can delete complaints
        return $user->type === 'admin';
    }
}
