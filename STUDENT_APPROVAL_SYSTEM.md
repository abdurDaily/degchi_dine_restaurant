# Student Membership Approval System

## Overview
Student members now require admin approval before they can use first-order discounts (35% off). Regular members are auto-approved and can use their 30% first-order discount immediately.

## Implementation Details

### Database Changes
**Migration:** `2026_06_15_100000_add_is_approved_to_members_table.php`

Added `is_approved` boolean column to `members` table:
- Default: `true` for regular members (auto-approved)
- Default: `false` for student members (needs admin approval)
- Existing student members in database set to `false` (unapproved)

### Backend Changes

#### 1. Member Model (`app/Models/Member.php`)
- Added `is_approved` to `$fillable` array
- Added `is_approved` to `$casts` array with boolean casting

#### 2. Member Controller (`app/Http/Controllers/Backend/MemberController.php`)
Added two new methods:
- `approve(Member $member)` - Approves a student member
- `reject(Member $member)` - Revokes approval for a student member
- Both methods validate that only student members can be approved/rejected
- Added `is_approved` field to member details JSON response

#### 3. Home Controller (`app/Http/Controllers/Frontend/HomeController.php`)

**registerMember() method:**
- Sets `is_approved = false` for students, `true` for regular members
- Updated success message to inform students about approval requirement
- Returns `is_approved` status in AJAX response

**checkMemberCard() method:**
- Checks `is_approved` status for student members
- Returns appropriate message if student is pending approval
- Prevents discount eligibility for unapproved students

**storeOrder() method:**
- Added approval check when applying first-order offers
- Skip first-order discount if student member is not approved
```php
if ($member->is_student && !$member->is_approved) {
    continue; // Skip first-order discount for unapproved students
}
```

### Frontend Changes

#### Members Index View (`resources/views/backend/members/index.blade.php`)

**Table Structure:**
- Added "Approval" column showing approval status badge
- For students: Shows "Approved" (green) or "Pending" (yellow) badge
- For regular members: Shows "N/A"

**Approval Buttons:**
- Unapproved students: Green "Approve" button
- Approved students: Red "Revoke" button
- Buttons dynamically switch when clicked

**JavaScript Handlers:**
- `.approve-member-btn` - Sends POST request to approve student
- `.reject-member-btn` - Sends POST request to revoke approval
- Updates badge and button in real-time after successful action
- Shows success/error messages using toastr

**Modal Details:**
- Added approval status badge in member details modal
- Shows approval status only for student members

### Routes
Added to `routes/web.php`:
```php
Route::post('members/{member}/approve', [MemberController::class, 'approve'])->name('members.approve');
Route::post('members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');
```

## User Flow

### For Regular Members
1. User registers as regular member (not student)
2. Member is auto-approved (`is_approved = true`)
3. Can immediately use 30% first-order discount
4. No admin action required

### For Student Members
1. User registers as student member with student card upload
2. Member created with `is_approved = false`
3. Receives message: "Your student membership will be reviewed by admin"
4. **Cannot use 35% first-order discount yet**
5. Admin reviews student card in members list
6. Admin clicks "Approve" button
7. Member can now use 35% first-order discount

### Admin Approval Process
1. Navigate to `/admin/members`
2. Find student members with "Pending" badge in Approval column
3. Click "View Details" to see student card
4. Click "Approve" button to approve
5. Status changes to "Approved" immediately
6. Student can now order with discount

### Revoking Approval
1. Admin can click "Revoke" button on approved students
2. Status changes back to "Pending"
3. Student loses first-order discount eligibility

## API Response Examples

### Member Check - Unapproved Student
```json
{
    "eligible": false,
    "member_name": "John Doe",
    "total_purchase": 0,
    "discount_rate": 0,
    "is_student": true,
    "is_approved": false,
    "message": "Your student membership is pending admin approval. First-order discount will be available once approved."
}
```

### Member Check - Approved Student
```json
{
    "eligible": true,
    "member_name": "John Doe",
    "total_purchase": 0,
    "discount_rate": 35,
    "is_student": true,
    "is_approved": true,
    "message": "Welcome back! 35% first-order discount applied to all food items."
}
```

### Member Check - Regular Member
```json
{
    "eligible": true,
    "member_name": "Jane Smith",
    "total_purchase": 0,
    "discount_rate": 30,
    "is_student": false,
    "is_approved": true,
    "message": "Welcome back! 30% first-order discount applied to all food items."
}
```

## Testing Checklist

### Registration
- [ ] Register as regular member → Auto-approved
- [ ] Register as student member → Pending approval
- [ ] Check success message mentions approval for students

### Member Card Check (Checkout)
- [ ] Unapproved student enters card → Shows pending message, no discount
- [ ] Approved student enters card → Shows eligible message, 35% discount
- [ ] Regular member enters card → Shows eligible message, 30% discount

### Admin Panel
- [ ] Students show "Pending" badge initially
- [ ] Click "Approve" → Badge changes to "Approved"
- [ ] Click "Revoke" → Badge changes to "Pending"
- [ ] Regular members show "N/A" in approval column

### Order Placement
- [ ] Unapproved student places order → No first-order discount applied
- [ ] Approved student places order → 35% first-order discount applied
- [ ] Regular member places order → 30% first-order discount applied

## Migration Instructions

### Local Development
Already completed - migration has run (Batch 3)

### Live/Production Server
Run the migration:
```bash
php artisan migrate
```

This will:
1. Add `is_approved` column to `members` table
2. Set all existing students to `is_approved = false`
3. Set all existing regular members to `is_approved = true`

**Important:** After running migration on live server, review all student members and approve legitimate ones.

## Security Considerations
- Only admin users can access approval endpoints
- Approval status is validated on both frontend and backend
- Cannot approve non-student members
- Cannot use first-order discount without approval (backend validation)

## Future Enhancements
- Email/SMS notification to student when approved
- Approval notes/comments by admin
- Batch approval functionality
- Auto-approval based on verified institution email domains
