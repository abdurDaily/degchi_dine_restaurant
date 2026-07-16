### Task 6: End-to-end verification checklist

**Files:** none (manual + test suite)

- [ ] **Step 1: Ensure migrations**

```bash
php artisan migrate
```

Confirm `notifications` table exists (migration `2025_01_28_113101_create_notifications_table` already in repo).

- [ ] **Step 2: Configure local `.env`**

```
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=sync
PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=...
```

Run `php artisan config:clear`.

- [ ] **Step 3: Run automated tests**

```bash
php artisan test --filter=NewOrderNotificationTest
```

Expected: all PASS.

- [ ] **Step 4: Manual E2E**

1. Log into admin as user with `orders-show`; open dashboard; click once anywhere (unlock sound).
2. In another browser/session, place a COD order.
3. Without refreshing admin: toast appears, badge increments, list prepends (max 10), sound plays.
4. Click a notification â†’ unread style clears, badge decrements (network `POST .../read` 200).
5. Mark all as read â†’ badge 0.
6. Complete SSLCommerz sandbox payment â†’ exactly one notification after first paid transition.
7. Confirm old polling order alert still works independently.

- [ ] **Step 5: Document production queue reminder** in PR/notes (no new markdown file unless asked):

- Production: `QUEUE_CONNECTION=database` or `redis` + `php artisan queue:work`
- Pusher free plan: ~200k messages/day, ~100 concurrent connections

---


