# Task 4 Report: Mark-as-read API

## Status

**Complete** — mark single and mark-all notification endpoints implemented with TDD.

## Changes

| File | Change |
|------|--------|
| `app/Http/Controllers/NotificationController.php` | Added `index()` (404 stub), `markAsRead($id)`, `markAllAsRead()` with `orders-show` guard and JSON `{ success: true }`. |
| `routes/web.php` | Extended notifications group: `POST notifications/read-all` (`notify.readAll`), `POST notifications/{id}/read` (`notify.read`), middleware `can:orders-show`. |
| `tests/Feature/NewOrderNotificationTest.php` | Added `test_admin_can_mark_notification_read`, `test_admin_can_mark_all_notifications_read`, private `makeOrder()` helper. |

## TDD Evidence

### Step 1 — Failing tests (routes missing)

```
php artisan test --filter=NewOrderNotificationTest

FAIL  Tests\Feature\NewOrderNotificationTest
✓ notify admins sends to users with orders show only
⨯ admin can mark notification read          RouteNotFoundException: Route [notify.read] not defined.
⨯ admin can mark all notifications read       RouteNotFoundException: Route [notify.readAll] not defined.

Tests: 2 failed, 1 passed (2 assertions)
```

### Step 2 — Implementation → green

```
php artisan test --filter=NewOrderNotificationTest

PASS  Tests\Feature\NewOrderNotificationTest
✓ notify admins sends to users with orders show only
✓ admin can mark notification read
✓ admin can mark all notifications read

Tests: 3 passed (7 assertions), ~12s
```

`$admin->notify()` with `QUEUE_CONNECTION=sync` in `phpunit.xml` stored database notifications without `Notification::sendNow`.

## Concerns

- No test for 403 when user lacks `orders-show` (middleware handles it; could add negative case later).
- `index()` still returns 404 — list endpoint is a future task.

## Not Done (per instructions)

- No commit.
