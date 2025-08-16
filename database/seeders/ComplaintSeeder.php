<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\User;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for seeding
        $patients = User::where('type', 0)->take(3)->get(); // patient type
        $staff = User::where('type', 3)->take(2)->get(); // staff type
        $doctors = User::where('type', 2)->take(2)->get(); // doctor type
        $admins = User::where('type', 1)->take(1)->get(); // admin type

        if ($patients->isEmpty() || $staff->isEmpty() || $doctors->isEmpty() || $admins->isEmpty()) {
            $this->command->warn('Not enough users found. Please run UserSeeder first.');
            return;
        }

        $categories = ['general', 'billing', 'appointment', 'treatment', 'facility', 'staff', 'other'];
        $statuses = ['new', 'in_progress', 'resolved', 'closed'];

        // Create sample complaints
        foreach ($patients as $patient) {
            $numComplaints = rand(1, 3);
            
            for ($i = 0; $i < $numComplaints; $i++) {
                $status = $statuses[array_rand($statuses)];
                $category = $categories[array_rand($categories)];
                
                // Auto-assign based on category
                $assignedTo = null;
                if ($category === 'billing' && $admins->isNotEmpty()) {
                    $assignedTo = $admins->random();
                } elseif ($category === 'treatment' && $doctors->isNotEmpty()) {
                    $assignedTo = $doctors->random();
                } elseif ($staff->isNotEmpty()) {
                    $assignedTo = $staff->random();
                }

                $complaint = Complaint::create([
                    'user_id' => $patient->id,
                    'category' => $category,
                    'description' => $this->getSampleDescription($category),
                    'status' => $status,
                    'assigned_to' => $assignedTo?->id,
                    'rating' => $status === 'resolved' ? rand(3, 5) : null,
                    'feedback' => $status === 'resolved' ? $this->getSampleFeedback() : null,
                    'resolved_at' => $status === 'resolved' ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        $this->command->info('Sample complaints created successfully!');
    }

    private function getSampleDescription($category): string
    {
        $descriptions = [
            'general' => [
                'I have a general question about the hospital services and would like more information.',
                'Need clarification on hospital policies and procedures.',
                'General inquiry about visiting hours and parking facilities.'
            ],
            'billing' => [
                'Received an incorrect bill for services that were not provided.',
                'Insurance claim was denied without proper explanation.',
                'Billing statement shows duplicate charges for the same service.'
            ],
            'appointment' => [
                'Appointment was cancelled without prior notice.',
                'Unable to schedule appointment through the online system.',
                'Doctor was running very late for scheduled appointment.'
            ],
            'treatment' => [
                'Concerned about the prescribed medication and its side effects.',
                'Treatment plan was changed without proper consultation.',
                'Follow-up appointment was not scheduled as promised.'
            ],
            'facility' => [
                'Hospital room was not properly cleaned before admission.',
                'Equipment in the waiting area is not functioning properly.',
                'Parking lot lighting is insufficient during evening hours.'
            ],
            'staff' => [
                'Staff member was rude and unprofessional during visit.',
                'Nurse did not respond to call button in a timely manner.',
                'Receptionist provided incorrect information about procedures.'
            ],
            'other' => [
                'Lost personal belongings during hospital stay.',
                'Food quality in cafeteria needs improvement.',
                'WiFi connection in patient rooms is very slow.'
            ]
        ];

        $categoryDescriptions = $descriptions[$category] ?? $descriptions['general'];
        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }

    private function getSampleFeedback(): string
    {
        $feedbacks = [
            'The issue was resolved quickly and professionally. Staff was very helpful.',
            'Good communication throughout the process. Satisfied with the resolution.',
            'Problem was addressed satisfactorily. Would recommend the service.',
            'Resolution exceeded expectations. Staff went above and beyond.',
            'Timely response and effective solution. Thank you for your help.'
        ];

        return $feedbacks[array_rand($feedbacks)];
    }
}
