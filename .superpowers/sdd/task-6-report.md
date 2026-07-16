# Task 6 Report: End-to-end verification

## Status

DONE_WITH_CONCERNS — automated checks passed; full Pusher live E2E requires real credentials in `.env` (not available to verify in this session).

## Automated verification

### Tests
```
php artisan test --filter=NewOrderNotificationTest
→ 3 passed (7 assertions)
```

### Routes
- `broadcasting/auth` — middleware: `web`, `auth` ✅
- `POST notifications/{id}/read` → `notify.read` ✅
- `POST notifications/read-all` → `notify.readAll` ✅
- `GET notifications/` → `notify.index` (404 stub) ✅

### Migrations
Did **not** run `php artisan migrate` in this task (avoid side effects without explicit ask). Notifications table migration already exists in repo (`2025_01_28_113101_create_notifications_table.php`). Operator should confirm table exists in their environment.

## Manual E2E checklist (for human)

1. Set in `.env`:
   ```
   BROADCAST_CONNECTION=pusher
   QUEUE_CONNECTION=sync
   PUSHER_APP_ID=...
   PUSHER_APP_KEY=...
   PUSHER_APP_SECRET=...
   PUSHER_APP_CLUSTER=...
   ```
   Then `php artisan config:clear`.

2. Log in as user with `orders-show`; open admin; click once (unlock sound).

3. Place COD order from another session → without refresh: toast, badge +1, list prepend, sound.

4. Mark one / mark all → AJAX updates badge.

5. SSLCommerz paid order → one notification after first `markOrderAsPaid`.

6. Confirm existing polling `admin-order-alert` still works.

## Production notes

- Production: `QUEUE_CONNECTION=database` or `redis` + `php artisan queue:work`
- Pusher free plan: ~200k messages/day, ~100 concurrent connections (comment in `config/broadcasting.php`)

## Concerns

- Live Pusher toast/sound not exercised in CI/agent environment (needs real Pusher app keys).
- `.env` local values are operator-owned; `.env.example` documents keys.
