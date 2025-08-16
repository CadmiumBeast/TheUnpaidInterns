# GovCare+ Complaint & Feedback System

This document describes the implementation of the complaint and feedback system for the GovCare+ hospital management platform, based on the provided sequence diagram.

## System Overview

The complaint and feedback system allows patients to submit complaints, staff to handle them, and patients to provide feedback once resolved. The system follows a role-based access control model with different permissions for patients, staff, doctors, and administrators.

## Features

### For Patients
- Submit new complaints with category, description, and optional photo
- View all submitted complaints with current status
- Provide feedback and rating once complaints are resolved
- Track complaint progress

### For Staff/Doctors/Admins
- View complaints assigned to their department
- Update complaint status (New → In Progress → Resolved → Closed)
- View patient information and complaint details
- Handle complaints based on category and expertise

### System Features
- Automatic complaint assignment based on category
- Status tracking and workflow management
- Photo upload support
- Rating and feedback system
- Dashboard widgets showing complaint statistics
- Role-based access control

## Database Schema

### Complaints Table
```sql
CREATE TABLE complaints (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    category VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    photo_path VARCHAR(255) NULL,
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    assigned_to BIGINT NULL,
    rating INT NULL,
    feedback TEXT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);
```

### Categories
- **General**: General inquiries and questions
- **Billing**: Billing and insurance issues
- **Appointment**: Appointment scheduling problems
- **Treatment**: Medical treatment concerns
- **Facility**: Facility and equipment issues
- **Staff**: Staff behavior and professionalism
- **Other**: Miscellaneous issues

### Status Flow
1. **New**: Complaint submitted, awaiting assignment
2. **In Progress**: Complaint assigned and being worked on
3. **Resolved**: Issue resolved, awaiting patient feedback
4. **Closed**: Complaint fully closed after feedback

## API Endpoints

### Patient Routes (Authenticated, Patient type)
- `GET /complaints` - List patient's complaints
- `GET /complaints/create` - Show complaint creation form
- `POST /complaints` - Submit new complaint
- `GET /complaints/{id}` - View complaint details
- `POST /complaints/{id}/feedback` - Submit feedback for resolved complaint

### Staff/Doctor/Admin Routes (Authenticated, respective types)
- `GET /complaints` - List complaints assigned to department
- `GET /complaints/{id}` - View complaint details
- `POST /complaints/{id}/status` - Update complaint status

## File Structure

```
app/
├── Http/Controllers/
│   └── ComplaintController.php      # Main complaint logic
├── Models/
│   ├── Complaint.php                # Complaint model
│   └── User.php                     # Updated with relationships
├── Policies/
│   └── ComplaintPolicy.php          # Authorization rules
└── Providers/
    └── AppServiceProvider.php       # Policy registration

database/
├── migrations/
│   └── create_complaints_table.php  # Database schema
└── seeders/
    └── ComplaintSeeder.php          # Sample data

resources/views/
├── complaints/
│   ├── index.blade.php              # Complaint list
│   ├── create.blade.php             # Complaint form
│   └── show.blade.php               # Complaint details
├── components/
│   └── complaints-dashboard-widget.blade.php  # Dashboard widget
└── dashboard.blade.php               # Updated dashboard

routes/
└── web.php                          # Complaint routes
```

## Implementation Details

### Auto-Assignment Logic
Complaints are automatically assigned based on category:
- **Billing** → Admin department
- **Treatment** → Doctor department  
- **Others** → Staff department

### Authorization Rules
- Patients can only view and manage their own complaints
- Staff can view and update complaints assigned to their department
- Admins have full access to all complaints
- Feedback can only be submitted by the patient who created the complaint

### Notification System
The system includes placeholder notification logic that can be integrated with:
- Email notifications
- SMS notifications
- In-app notifications
- Push notifications

## Setup Instructions

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Sample Data**
   ```bash
   php artisan db:seed --class=ComplaintSeeder
   ```

3. **Create Storage Link** (for photo uploads)
   ```bash
   php artisan storage:link
   ```

4. **Verify Routes**
   ```bash
   php artisan route:list --name=complaints
   ```

## Usage Examples

### Submitting a Complaint
1. Patient logs in and navigates to Complaints
2. Clicks "Submit Complaint"
3. Selects category and fills description
4. Optionally uploads photo
5. Submits complaint (automatically assigned to appropriate department)

### Handling a Complaint
1. Staff/Doctor/Admin views assigned complaints
2. Updates status as work progresses
3. Marks as resolved when complete
4. Patient receives notification of resolution

### Providing Feedback
1. Patient receives notification that complaint is resolved
2. Rates experience (1-5 stars)
3. Optionally provides written feedback
4. Complaint marked as closed

## Security Features

- CSRF protection on all forms
- Role-based access control
- Policy-based authorization
- Input validation and sanitization
- File upload restrictions (images only, max 2MB)

## Future Enhancements

- Real-time notifications using WebSockets
- Email/SMS integration
- Advanced reporting and analytics
- Mobile app support
- Integration with other hospital systems
- Escalation workflows for urgent complaints
- SLA tracking and management

## Troubleshooting

### Common Issues
1. **Photos not displaying**: Ensure storage link is created
2. **Permission denied**: Check user type and policy rules
3. **Complaints not showing**: Verify user authentication and type
4. **Status updates failing**: Check authorization policies

### Debug Commands
```bash
# Check complaint policies
php artisan tinker
>>> Gate::forUser(User::find(1))->can('view', Complaint::find(1))

# View complaint relationships
php artisan tinker
>>> Complaint::with(['user', 'assignedUser'])->first()
```

## Support

For technical support or questions about the complaint system, please refer to the system documentation or contact the development team.
