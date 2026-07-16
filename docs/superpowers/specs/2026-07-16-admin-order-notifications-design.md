# Admin Real-Time New Order Notifications — Design

**Date:** 2026-07-16  
**Status:** Approved for implementation planning  
**Scope:** Admin panel only (Blade dashboard). No customer-facing notifications.

## Decisions (locked)

| Topic | Choice |
|--------|--------|
| Recipients | Users with Spatie permission `orders-show` (not a `users.role` column) |
| Pusher credentials | `.env` only (do not use existing DB `pusher_setting` for this feature) |
| Existing polling order alert | Keep both — leave `admin-order-alert.js` / gear panel unchanged |
| When to notify | Only on successful completion: COD success path + after SSLCommerz paid (`markOrderAsPaid`) |
| Approach | Laravel `Notification` (`database` + `broadcast`) + Pusher + Echo CDN |

## Goals

- Real-time admin notification when a new order is successfully completed, without page refresh.
- Persist notifications in the DB for unread badge, latest-10 list, and mark-as-read.
- Work with standard Laravel web session auth (not Sanctum/API tokens).
- Support multiple admins, each on their own private channel `App.Models.User.{id}`.

## Non-goals

- Frontend/customer notifications
- Replacing or merging the existing polling order-alert sound UI
- Reading Pusher keys from the settings DB
- Vite/npm bundling of Echo (prefer CDN for admin assets)

## Architecture

```text
COD success (HomeController::storeOrder)
        \                                      ┌─ notifications table (per admin)
         ├─► NotifyAdminsOfNewOrder($order) ──┤
SSL paid (PaymentController::markOrderAsPaid)/ └─► PrivateChannel App.Models.User.{id}
                                                      │
                                                      ▼
                                              Pusher → Echo (.notification())
                                                      │
                              ┌───────────────────────┼───────────────────────┐
                              ▼                       ▼                       ▼
                         prepend list            bump badge                 toast + sound
```

### Recipient query

```php
User::permission('orders-show')->get()
```

Send once via:

```php
Notification::send($admins, new NewOrderNotification($order));
```

Do not loop `notify()` individually unless required for debugging.

### Trigger points

1. **COD:** After `Order::create` and successful COD handling in `HomeController::storeOrder` (same place that currently returns the COD success response).
2. **SSLCommerz:** Inside `PaymentController::markOrderAsPaid` after the order is marked paid (covers browser success callback and IPN). Guard so already-paid early return does not double-send; only notify when this call actually transitions to paid (or notify only once by checking we just updated).

Recommended: call the helper only after a successful paid transition (when `payment_status` was not already `paid` before update).

### Deduplication

- COD and SSL are mutually exclusive payment methods for a given order → no cross-path duplicate.
- IPN + success callback both call `markOrderAsPaid`; existing early return if already paid prevents a second notification if we notify only on the first successful transition.

## Backend components

### `NewOrderNotification`

- Implements `ShouldQueue` and broadcast via notification channels.
- `via($notifiable)`: `['database', 'broadcast']`.
- Payload (same shape in `toDatabase` and `toBroadcast`):
  - `order_id`
  - `message` — `"নতুন অর্ডার এসেছে"`
  - `created_at` — ISO/string timestamp
  - Read state uses Laravel’s `notifications.read_at` (not a custom `is_read` column).
- Broadcast event name: `new-order-notification` via `broadcastType()` / `broadcastAs()`.
- Channel: Laravel’s default notification broadcast channel (`$notifiable->receivesBroadcastNotificationsOn()` → `App.Models.User.{id}`). Override on `User` only if the default would diverge.

### Channel authorization (`routes/channels.php`)

Private channel `App.Models.User.{id}`:

- Allow only if `(int) $user->id === (int) $id` **and** `$user->can('orders-show')`.
- Non-permissioned users cannot subscribe even to their own user channel for this feature.

### Broadcasting / queue infra

- Install `pusher/pusher-php-server`.
- Publish/add `config/broadcasting.php` with Pusher block from env.
- `.env`:

  ```
  BROADCAST_CONNECTION=pusher
  PUSHER_APP_ID=
  PUSHER_APP_KEY=
  PUSHER_APP_SECRET=
  PUSHER_APP_CLUSTER=
  ```

- Local/dev: `QUEUE_CONNECTION=sync` so broadcasts run inline.
- Production: `QUEUE_CONNECTION=database` or `redis` + `php artisan queue:work`.
- Register channels + `/broadcasting/auth` with `web` + `auth` middleware (Laravel 11: `withRouting(channels: ...)` and `Broadcast::routes` in a provider or `AppServiceProvider`).
- Code comment noting Pusher free-plan limits: ~200k messages/day, ~100 concurrent connections.

### HTTP endpoints

Extend existing empty `App\Http\Controllers\NotificationController`:

| Method | Route | Action |
|--------|--------|--------|
| POST | `/notifications/{id}/read` | Mark one as read for `auth()->user()` |
| POST | `/notifications/read-all` | Mark all unread as read |

Middleware: same as other dashboard routes (`auth`, `setLocale`, `user.active`) plus ensure user has `orders-show` (middleware or controller abort).

Existing `GET /notifications/` (`notify.index`) may remain stub or later list page; out of scope unless needed for empty controller cleanup.

### Notifications table

Migration already exists: `database/migrations/2025_01_28_113101_create_notifications_table.php`. Ensure migrated in each environment; no new notifications-table migration required if already applied.

## Admin UI

### Placement

- Bell + dropdown in `resources/views/components/header.blade.php` (topbar).
- CDN Echo/Pusher scripts, hidden `<audio>`, and `admin-notifications.js` loaded from `admin-master.blade.php` when `@can('orders-show')`.

### Behavior

- Initial state from DB: latest 10 notifications; unread count from unread notifications.
- Click item → AJAX mark read → update UI.
- Mark all as read → AJAX → clear badge / unread styles.
- Live event via Echo `.notification()` on private user channel:
  - Prepend to list, keep max 10 DOM items
  - Increment badge
  - Toast (top-right, auto-dismiss ~4–5s)
  - Play sound if unlocked

### Why `.notification()` instead of raw `.listen()`

Laravel’s Echo `.notification()` listens for the Notification broadcast contract (including the custom type name). It aligns with `toBroadcast()` / database channels without inventing a separate event class or mismatched listen names.

### Sound

- New file under `public/sounds/` (e.g. `notification.mp3`) — no sounds folder today; add asset + `.gitkeep` if needed.
- Unlock audio on first `document` click (`{ once: true }`); only then call `audio.play()`, with `.catch()` on rejection.
- Independent of existing Web Audio poller in `admin-order-alert.js`.

### CSS

- Dedicated small CSS file (e.g. `public/assets/css/admin-notifications.css`): badge, dropdown, toast animation. Match Velzon/header styling; do not redesign the whole admin chrome.

## Files checklist

**Add**

- `app/Notifications/NewOrderNotification.php`
- `app/Support/NotifyAdminsOfNewOrder.php` (thin helper)
- `routes/channels.php`
- `config/broadcasting.php` (if missing)
- `app/Providers/BroadcastServiceProvider.php` (or equivalent boot in `AppServiceProvider` + `bootstrap/app.php` channels path)
- `public/js/admin-notifications.js`
- `public/assets/css/admin-notifications.css` (or equivalent)
- `public/sounds/notification.mp3` (+ `.gitkeep` if binary omitted from repo temporarily)

**Edit**

- `app/Http/Controllers/Frontend/HomeController.php` (COD success)
- `app/Http/Controllers/Frontend/PaymentController.php` (`markOrderAsPaid`)
- `app/Http/Controllers/NotificationController.php`
- `app/Models/User.php` (broadcast channel override if needed)
- `routes/web.php`
- `bootstrap/app.php` and/or `bootstrap/providers.php`
- `resources/views/components/header.blade.php`
- `resources/views/components/admin-master.blade.php`
- `composer.json` (pusher package)
- `.env.example` (document keys; no secrets)

**Leave unchanged**

- `public/assets/js/admin-order-alert.js` and related CSS/markup
- Admin Pusher settings UI / DB `pusher_setting`

## Error handling

- If no users have `orders-show`, send is a no-op.
- Mark-as-read only for notifications belonging to the authenticated user (404/403 otherwise).
- Broadcast auth failure → Echo does not subscribe; DB list still works on page load.
- Audio autoplay blocked → silent failure via `.catch()`; UI still updates.

## Test plan (manual)

1. Configure Pusher `.env` keys; set `BROADCAST_CONNECTION=pusher`, `QUEUE_CONNECTION=sync`.
2. Confirm `notifications` table migrated.
3. Log in as user with `orders-show`; open any admin page; click once (unlock sound).
4. Place a COD order from another session → without refreshing admin: toast, badge +1, list prepend, sound.
5. Mark one / mark all via UI → badge updates via AJAX.
6. Complete an SSLCommerz paid order (sandbox) → one notification after first successful `markOrderAsPaid`.
7. Log in as user without `orders-show` → no Echo subscription / no bell (or inaccessible endpoints).

## Open implementation notes

- Prefer Laravel 11 idiomatic channel registration (`bootstrap/app.php` `channels:` + `Broadcast::routes(['middleware' => ['web', 'auth']])`).
- Pass `userId`, CSRF token, and Pusher key/cluster to JS via Blade meta tags or a small inline config object (no Vite env required for admin CDN setup).
