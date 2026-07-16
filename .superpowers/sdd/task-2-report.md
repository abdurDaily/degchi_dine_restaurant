# Task 2 Report: Notification class + helper + User channel

**Status:** DONE  
**Branch:** feature/admin-order-notifications  
**Date:** 2026-07-16

## Summary

Implemented `NewOrderNotification`, `NotifyAdminsOfNewOrder` helper, and `User::receivesBroadcastNotificationsOn()` per task brief. TDD followed (RED then GREEN). No commit per global constraint.

## TDD Evidence

### Step 1: Write failing feature test

Created `tests/Feature/NewOrderNotificationTest.php`. `OrderFactory` does not exist — order created inline with minimal required columns from `orders` migration (`customer_name`, `customer_phone`, `customer_address`, `payment_method`, `status`, `total_amount`, `final_amount`).

### Step 2: RED — expect fail

```bash
php artisan test --filter=NewOrderNotificationTest
```

```
   FAIL  Tests\Feature\NewOrderNotificationTest
  ⨯ notify admins sends to users with orders show only                                                          11.98s
  ────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
   FAILED  Tests\Feature\NewOrderNotificationTest > notify admins sends to users with orders show only          Error
  Class "App\Support\NotifyAdminsOfNewOrder" not found

  at tests\Feature\NewOrderNotificationTest.php:39

  Tests:    1 failed (0 assertions)
  Duration: 12.45s
```

Expected failure: helper/notification classes missing. DB (`RefreshDatabase` + MySQL) worked; no credential issues.

### Steps 3–5: Implementation

| File | Action |
|---|---|
| `app/Models/User.php` | Added `receivesBroadcastNotificationsOn()` → `App.Models.User.{id}` |
| `app/Notifications/NewOrderNotification.php` | `ShouldQueue`, `via(['database','broadcast'])`, payload keys, `broadcastType()` = `new-order-notification`, message `নতুন অর্ডার এসেছে` |
| `app/Support/NotifyAdminsOfNewOrder.php` | `send(Order $order)` queries `User::permission('orders-show')`, early return if empty |

**Note:** `ShouldBroadcast` not implemented on notification class (per brief — `broadcast` channel already emits `BroadcastNotificationCreated`).

### Step 6: GREEN — expect pass

```bash
php artisan test --filter=NewOrderNotificationTest
```

```
   PASS  Tests\Feature\NewOrderNotificationTest
  ✓ notify admins sends to users with orders show only                                                          11.86s

  Tests:    1 passed (2 assertions)
  Duration: 12.12s
```

### Step 7: Commit

**Skipped** per global constraint override (Do NOT commit).

## Interfaces Produced

| Interface | Value |
|---|---|
| Constructor | `new NewOrderNotification(Order $order)` |
| Helper | `NotifyAdminsOfNewOrder::send(Order $order): void` |
| Payload keys | `order_id`, `message`, `created_at` |
| `broadcastType()` | `new-order-notification` |
| Broadcast channel | `App.Models.User.{id}` via `User::receivesBroadcastNotificationsOn()` |
| Recipients | Users with Spatie `orders-show` permission |

## Self-Review

| Requirement | Status |
|---|---|
| TDD RED then GREEN | ✓ |
| `ShouldQueue` on notification | ✓ |
| `database` + `broadcast` channels | ✓ |
| No `ShouldBroadcast` on notification | ✓ |
| Permission filter `orders-show` only | ✓ (test asserts sent/not-sent) |
| Bengali message verbatim | ✓ |
| No commit | ✓ |

## Concerns

None. Tests run against project MySQL via `RefreshDatabase` without DB credential issues.

## Files Touched

- `tests/Feature/NewOrderNotificationTest.php` (created)
- `app/Notifications/NewOrderNotification.php` (created)
- `app/Support/NotifyAdminsOfNewOrder.php` (created)
- `app/Models/User.php` (modified)
