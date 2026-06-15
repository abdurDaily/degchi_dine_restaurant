# Approval System - Testing Steps

## ✅ Fixed Issues
1. Added toastr.js and toastr.css to members index page
2. Fixed button URL generation (using `.attr('data-url', ...)` instead of `.data('url', ...)`)
3. Removed confirmation dialogs - buttons now work immediately with toastr notifications
4. Fixed toastr check (using `typeof toastr !== 'undefined'`)

## 🧪 Testing Flow

### Step 1: Create a Test Student Member
1. Open: `http://127.0.0.1:8000/card-apply`
2. Fill in:
   - Name: Test Student
   - Phone: 01712345678
   - Email: student@test.com
   - Date of Birth: 2000-01-01
   - Address: Test Address
   - ✅ Check "Student" checkbox
   - Upload any image/PDF as student card
3. Click Submit
4. **Expected:** Success message says "pending admin approval"
5. Note the card number (e.g., MEM0001_5678)

### Step 2: Verify Member in Admin Panel
1. Go to: `http://127.0.0.1:8000/members`
2. Find the test student member
3. **Expected:** 
   - "Student" column shows: Green badge "Student (35% first-order)"
   - "Approval" column shows: Yellow badge "Pending"
   - Button shows: Green "✓ Approve" button

### Step 3: Test Member Card Before Approval
1. Go to: `http://127.0.0.1:8000/checkout` (with items in cart)
2. Enter the student card number (e.g., MEM0001_5678)
3. **Expected:** Message says "Your student membership is pending admin approval"
4. **Expected:** No 35% discount shown
5. **Expected:** No "Student Membership First Order" offer visible
6. **Expected:** Only custom dashboard offers visible (if any)

### Step 4: Approve the Student
1. Go back to: `http://127.0.0.1:8000/members`
2. Find the test student
3. Click the green "✓ Approve" button
4. **Expected:**
   - ✅ Green toastr notification: "Student member approved successfully..."
   - Badge changes to: Green "Approved"
   - Button changes to: Red "✗ Revoke"
   - NO page reload needed

### Step 5: Test Member Card After Approval
1. Go to: `http://127.0.0.1:8000/checkout` (with items in cart)
2. Clear the membership card field if filled
3. Re-enter the student card number
4. **Expected:** Message says "Welcome back! 35% first-order discount applied"
5. **Expected:** 35% discount calculated and shown
6. **Expected:** "Student Membership First Order 35% OFF" offer visible
7. **Expected:** Discount amount shown in order summary

### Step 6: Place Order as Approved Student
1. Continue checkout with the student card
2. Fill delivery details
3. Submit order
4. **Expected:** Order placed successfully with 35% discount applied

### Step 7: Test Revoke Approval
1. Go back to: `http://127.0.0.1:8000/members`
2. Find the approved student
3. Click the red "✗ Revoke" button
4. **Expected:**
   - ✅ Green toastr notification: "Student member approval revoked"
   - Badge changes back to: Yellow "Pending"
   - Button changes back to: Green "✓ Approve"

### Step 8: Verify Revoked Access
1. Go to: `http://127.0.0.1:8000/checkout`
2. Enter the student card number again
3. **Expected:** Back to "pending admin approval" message
4. **Expected:** No discount available

## 🔧 Troubleshooting

### Buttons Not Working?
1. Open browser console (F12)
2. Click the Approve button
3. Check for errors
4. Common issues:
   - CSRF token missing
   - jQuery not loaded
   - Toastr not loaded
   - Wrong URL format

### Toastr Not Showing?
1. Check browser console for errors
2. Verify toastr.js is loaded:
   ```javascript
   console.log(typeof toastr);
   // Should output: "object"
   ```
3. Try manual test:
   ```javascript
   toastr.success('Test message');
   ```

### Discount Not Showing After Approval?
1. Clear browser cache
2. Clear localStorage:
   ```javascript
   localStorage.clear();
   ```
3. Refresh checkout page
4. Re-enter membership card number
5. Check browser console for offer filtering logs

### Badge Not Updating?
1. Check if AJAX request succeeded (Network tab in browser)
2. Check response from server
3. Verify jQuery selector matches the element ID
4. Try manual update:
   ```javascript
   $('#approval-badge-1').removeClass('bg-warning').addClass('bg-success').text('Approved');
   ```

## 🎯 Success Criteria

✅ Approve button works without page reload  
✅ Badge updates immediately  
✅ Button toggles between Approve/Revoke  
✅ Toastr notifications show (not alerts)  
✅ Checkout shows discount after approval  
✅ Checkout hides discount before approval  
✅ Backend validates approval status  
✅ Order applies discount correctly  

## 📊 Database Verification

After approval, check database:
```sql
SELECT id, name, unique_card_number, is_student, is_approved 
FROM members 
WHERE is_student = 1;
```

**Expected:**
- Approved student: `is_approved = 1`
- Pending student: `is_approved = 0`

After order placement:
```sql
SELECT o.id, m.name, m.is_student, m.is_approved, o.offer_discount, o.final_amount
FROM orders o
JOIN members m ON o.member_id = m.id
WHERE m.is_student = 1
ORDER BY o.created_at DESC
LIMIT 5;
```

**Expected:**
- Approved student orders: `offer_discount > 0`
- Unapproved student orders: `offer_discount = 0`
