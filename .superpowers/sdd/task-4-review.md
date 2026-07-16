# Task 4 Review: Mark-as-read API

**Reviewer:** SDD review agent  
**Date:** 2026-07-16  
**Verdict:** Spec ✅ | Quality **Approved**

## Spec compliance

| Requirement | Status |
|---|---|
| `POST …/read` → `notify.read` → JSON `{ success: true }` | ✅ |
| `POST …/read-all` → `notify.readAll` → JSON `{ success: true }` | ✅ |
| `can:orders-show` middleware + controller guard | ✅ |
| Only own notifications (`auth()->user()->notifications()` / `unreadNotifications`) | ✅ |
| TDD tests appended; suite green | ✅ re-run PASS (3 passed, 7 assertions) |

## Findings

**None blocking.** Diff matches brief. `read-all` registered before `{id}/read`; foreign notification IDs 404 via scoped `firstOrFail`.

**Optional follow-ups:** assert JSON on `readAll` test; 403 / cross-user 404 negative tests; remove unused `Request` import.

## Files reviewed

- `app/Http/Controllers/NotificationController.php`
- `routes/web.php`
- `tests/Feature/NewOrderNotificationTest.php`
