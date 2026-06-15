# Testing Guide - Student Approval System

## Quick Test Checklist

### ✅ Test 1: Student Registration
**Steps:**
1. Go to `/card-apply`
2. Fill in all fields
3. Check "Student" checkbox
4. Upload student card (PDF or image)
5. Submit form

**Expected Result:**
- Success message: "Your student membership will be reviewed by admin. First-order discount will be available once approved."
- Card number displayed
- Member created in database with `is_approved = false`

---

### ✅ Test 2: Regular Member Registration
**Steps:**
1. Go to `/card-apply`
2. Fill in all fields
3. **DO NOT** check "Student" checkbox
4. Submit form

**Expected Result:**
- Success message: "You can now use 30% first-order discount!"
- Card number displayed
- Member created in database with `is_approved = true`

---

### ✅ Test 3: Unapproved Student in Checkout
**Steps:**
1. Go to `/checkout` with items in cart
2. Enter unapproved student card number
3. Check the discount section

**Expected Result:**
- Message: "Your student membership is pending admin approval..."
- No discount applied
- No first-order offers shown
- Only custom dashboard offers visible (if any)

---

### ✅ Test 4: Admin Approval Process
**Steps:**
1. Login as admin
2. Go to `/admin/members`
3. Find student member with yellow "Pending" badge
4. Click "View Details" button (eye icon)
5. Verify student card is visible in modal
6. Close modal
7. Click green "Approve" button

**Expected Result:**
- Badge changes from yellow "Pending" to green "Approved"
- Button changes from "Approve" to "Revoke"
- Success message displayed
- Database `is_approved` = true

---

### ✅ Test 5: Approved Student in Checkout
**Steps:**
1. Go to `/checkout` with items in cart
2. Enter approved student card number
3. Check the discount section

**Expected Result:**
- Message: "Welcome back! 35% first-order discount applied to all food items."
- 35% discount shown
- First-order offers (Student Membership First Order) visible
- Offer discount calculated correctly

---

### ✅ Test 6: Place Order as Approved Student
**Steps:**
1. Continue from Test 5
2. Complete checkout form
3. Submit order

**Expected Result:**
- Order created successfully
- 35% discount applied to order total
- Order shows correct final amount
- Member's `first_order_discount_used` set to true

---

### ✅ Test 7: Revoke Approval
**Steps:**
1. Login as admin
2. Go to `/admin/members`
3. Find approved student with green "Approved" badge
4. Click red "Revoke" button

**Expected Result:**
- Badge changes from green "Approved" to yellow "Pending"
- Button changes from "Revoke" to "Approve"
- Success message displayed
- Database `is_approved` = false
- Student can no longer use discount

---

### ✅ Test 8: Regular Member in Checkout
**Steps:**
1. Go to `/checkout` with items in cart
2. Enter regular member card number (non-student)
3. Check the discount section

**Expected Result:**
- Message: "Welcome back! 30% first-order discount applied to all food items."
- 30% discount shown
- First-order offers (Membership First Order) visible
- No approval check needed

---

### ✅ Test 9: Real-time Offer Filtering
**Steps:**
1. Go to `/checkout` with items in cart
2. Start typing unapproved student card number
3. Watch the offers section as you type
4. Clear the input field

**Expected Result:**
- As you type: First-order offers should not appear
- When cleared: First-order offers should disappear
- Custom offers should always be visible
- Updates happen in real-time without page refresh

---

### ✅ Test 10: Member Details Modal
**Steps:**
1. Login as admin
2. Go to `/admin/members`
3. Click "View Details" button for any student

**Expected Result:**
- Modal opens with member details
- Shows approval status badge for students:
  - Green "Approved" with checkmark icon, OR
  - Yellow "Pending Approval" with clock icon
- Regular members don't show approval status
- Student card visible (PDF button or image thumbnail)

---

## Browser Console Testing

### Check Offer Filtering Logic
Open browser console (F12) while on checkout page:

1. **Without Membership Card:**
```
Calculating offer discount for items: [...]
Has membership card: false
✗ Skipping first-order offer "Student Membership First Order" - no membership card provided
✗ Skipping first-order offer "Membership First Order" - no membership card provided
```

2. **With Membership Card:**
```
Calculating offer discount for items: [...]
Has membership card: true
Applicable offers after filtering: [...]
✓ Applied Student Membership First Order (35%): ৳XXX.XX
```

---

## Database Verification

### Check Member Approval Status
```sql
SELECT 
    id, 
    name, 
    unique_card_number, 
    is_student, 
    is_approved,
    created_at
FROM members
ORDER BY created_at DESC
LIMIT 10;
```

**Expected:**
- `is_student = 1` AND `is_approved = 0` → Pending student
- `is_student = 1` AND `is_approved = 1` → Approved student
- `is_student = 0` AND `is_approved = 1` → Regular member

### Check Order Discounts
```sql
SELECT 
    o.id,
    o.member_id,
    m.is_student,
    m.is_approved,
    o.subtotal,
    o.offer_discount,
    o.final_amount
FROM orders o
LEFT JOIN members m ON o.member_id = m.id
ORDER BY o.created_at DESC
LIMIT 10;
```

**Expected:**
- Unapproved students: `offer_discount = 0`
- Approved students: `offer_discount > 0` (35%)
- Regular members: `offer_discount > 0` (30%)

---

## Edge Cases to Test

### Edge Case 1: Student Already Used First-Order Discount
**Scenario:** Approved student who already used discount tries to order again

**Expected:** No first-order discount, only custom offers (if any)

---

### Edge Case 2: Approve Non-Student Member
**Scenario:** Admin tries to approve a regular member

**Expected:** Error message: "Only student members require approval."

---

### Edge Case 3: Approve Already Approved Student
**Scenario:** Admin clicks approve on already approved student

**Expected:** Error message: "Member is already approved."

---

### Edge Case 4: Order with Mixed Cart
**Scenario:** Cart has items with different offer eligibility

**Expected:** Each item gets best applicable offer based on:
1. Member type (student/regular)
2. Approval status
3. Discount already used
4. Offer type (first-order vs custom)

---

## Common Issues & Solutions

### Issue: "Nothing to migrate"
**Cause:** Migration already run  
**Solution:** Check `php artisan migrate:status` - should show migration in Batch 3

### Issue: Approval buttons not working
**Cause:** JavaScript not loaded or CSRF token missing  
**Solution:** Check browser console for errors, verify jQuery and CSRF token

### Issue: Student still gets discount after revoking approval
**Cause:** Cached data or frontend not updated  
**Solution:** 
1. Clear browser cache
2. Check localStorage for cached cart
3. Reload checkout page
4. Verify database `is_approved = 0`

### Issue: Regular members show "Pending" badge
**Cause:** Migration didn't set default correctly  
**Solution:** 
```sql
UPDATE members SET is_approved = 1 WHERE is_student = 0;
```

---

## Performance Testing

### Load Test Members List
**Goal:** Ensure approval column doesn't slow down page

**Steps:**
1. Create 100+ test members (mix of students and regular)
2. Load `/admin/members`
3. Check page load time
4. Click approve/revoke buttons

**Expected:** No noticeable performance degradation

---

## Security Testing

### Test 1: Unauthenticated Access
**Try:** Access `/admin/members/{id}/approve` without login  
**Expected:** Redirect to login page

### Test 2: Direct API Call
**Try:** POST to approve endpoint with unapproved student card in order  
**Expected:** Backend validation prevents discount application

### Test 3: Manipulated Frontend
**Try:** Modify JavaScript to bypass approval check  
**Expected:** Backend still validates, no discount applied

---

## Success Criteria

✅ All 10 tests pass  
✅ No console errors  
✅ Database updates correctly  
✅ Real-time UI updates work  
✅ No performance issues  
✅ Security checks pass  
✅ Edge cases handled properly  

---

## Reporting Issues

If you find any issues during testing, please note:
1. Which test failed
2. What you expected to see
3. What actually happened
4. Browser console errors (if any)
5. Database state before/after
6. Steps to reproduce
