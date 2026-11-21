# Order Notification Sound System — Source-Code-Based Technical Report

**Project:** Degchi Dine Restaurant (Laravel 11, PHP 8.4)
**Scope:** Admin dashboard order notification + sound alert system
**Method:** Derived entirely from actual source code inspection (no assumptions)
**Report date:** 2026-11-15

---

## 1. System Overview

The admin dashboard does **not** use a single notification mechanism. Inspection of the source reveals **two independent, parallel subsystems** that both react to a newly created order, but with completely different technologies, triggers, and sound implementations:

| # | Subsystem | Technology | Sound | Requires Pusher? | File |
|---|-----------|------------|-------|------------------|------|
| **A** | **Order-alert poller** (legacy/existing) | AJAX polling every 3 s (`fetch` → `GET /orders/latest-id`) + `localStorage` cross-tab sync | **Web Audio API** — synthesized 3-tone square-wave chime (no audio file) | **No** — works standalone | `public/assets/js/admin-order-alert.js` |
| **B** | **Notification bell + realtime push** (newer) | Laravel `Notification` (`database` + `broadcast` channels) → **Pusher** → **Laravel Echo** (CDN) | **HTML5 Audio API** — hidden `<audio>` element playing `public/sounds/notification.wav` | **Yes** — live push silently disables itself if the Pusher key is empty | `public/js/admin-notifications.js` |

Both subsystems are loaded on **every admin page** for users holding the Spatie permission `orders-show`, via the shared layout `resources/views/components/admin-master.blade.php` (lines 601–608):

```blade
@auth
    @can('orders-show')
    <script src="{{ asset('assets/js/admin-order-alert.js') }}" defer></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="{{ asset('js/admin-notifications.js') }}" defer></script>
    @endcan
@endauth
```

**Current environment status (important):** in this checkout, `.env` has `BROADCAST_CONNECTION=pusher` but `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET` are **empty**. Consequently:

- Subsystem **A (polling + synthesized sound) is fully operational.**
- Subsystem **B** renders the bell/badge/list from the database (server-side Blade), but its **realtime leg is inert**: `admin-notifications.js` bails out at line 210 (`if (!userId || !pusherKey || !csrfMeta) { return; }`) because the `pusher-key` meta tag is empty, so Echo never subscribes and the WAV sound never plays until real Pusher credentials are added to `.env`.
- The admin **“Pusher Setting”** page (`SettingController::pusherSettingStore`, `resources/views/settings/pusher-setting.blade.php`) writes to the `settings` table (`setting_group = 'pusher_setting'`) but **nothing reads those values into Laravel config** (verified: no `Config::set` anywhere). The in-repo design doc explicitly states: *“Pusher credentials: `.env` only (do not use existing DB `pusher_setting` for this feature).”*

In-repo design/plan documents that match the implementation exactly:

- `docs/superpowers/specs/2026-07-16-admin-order-notifications-design.md`
- `docs/superpowers/plans/2026-07-16-admin-order-notifications.md`

---

## 2. Technology Stack & Dependencies

### 2.1 External packages (Composer)

| Package | Version | Role in this feature |
|---------|---------|----------------------|
| `pusher/pusher-php-server` | `^7.2` (`composer.json` line 18) | Server-side Pusher driver used by Laravel's `broadcast` channel |
| `laravel/framework` | `^11.39.1` | Native **Notification** system (`database` + `broadcast` channels), Broadcasting, `/broadcasting/auth`, private channels |
| `spatie/laravel-permission` | `^6.12` | `User::permission('orders-show')` recipient query + `@can('orders-show')` gating + route middleware |

No `laravel/echo` PHP package, **no Reverb, no Soketi, no third-party Laravel notification package** is used.

### 2.2 JavaScript libraries

| Library | Source | Role |
|---------|--------|------|
| **pusher-js 8.4.0** | CDN `https://js.pusher.com/8.4.0/pusher.min.js` (not in `package.json`) | WebSocket client, sets `window.Pusher` |
| **Laravel Echo 1.16.1** | CDN `https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js` (not in `package.json`) | Sets `window.Echo`; `.private().notification()` subscription |
| jQuery + Bootstrap 5 (bundle) | Local `public/assets/…` | Toast (`bootstrap.Toast`), modal, DataTables UI |
| toastr | Local | “New Order” toast (subsystem A) |
| Yajra DataTables | Local | Orders table refresh on `dd:new-order` |

`package.json` contains **no** `laravel-echo` / `pusher-js` npm dependency — the project deliberately uses CDN builds for admin assets (design doc: *“Vite/npm bundling of Echo — non-goal”*).

### 2.3 Browser APIs / native features (no plugin)

- **Web Audio API** — `window.AudioContext || window.webkitAudioContext`, `createOscillator()`, `createGain()`, `exponentialRampToValueAtTime()` (subsystem A sound).
- **HTML5 Audio API** — `HTMLAudioElement.play()` on `<audio id="adminNotifSound" preload="auto">` (subsystem B sound).
- **`fetch()`** with `credentials: 'same-origin'` and `X-Requested-With: XMLHttpRequest`.
- **`localStorage`** — mute/volume persistence (`dd_order_sound_enabled`, `dd_order_sound_volume`) and cross-tab ping (`dd_order_alert_ping`) + `storage` event.
- **`CustomEvent` / `window.dispatchEvent`** — internal `dd:new-order` event.
- **`document.hidden`** — background-tab behavior.
- **`document` first-gesture unlock** (`click` / `keydown` / `touchstart`, `{ once: true }`) — browser autoplay-policy workaround.
- **Browser Web Notifications API (`new Notification()`) is NOT used anywhere** (verified by grep). There is **no** `Notification.requestPermission()` — the system never asks for desktop-notification permission.

### 2.4 Third-party services

- **Pusher (SaaS)** — only for subsystem B. Free-plan limits are noted in `config/broadcasting.php` line 10: *“~200k messages/day, 100 concurrent connections.”*

### 2.5 Native Laravel features used

- Notification class with `via() = ['database', 'broadcast']` and `ShouldQueue`.
- Database channel → `notifications` table (migration `database/migrations/2025_01_28_113101_create_notifications_table.php`).
- Broadcast channel → `Illuminate\Notifications\Events\BroadcastNotificationCreated` (event name `Illuminate.Notifications.Events.BroadcastNotificationCreated`, payload carries `id` + `type` + custom data — verified in `vendor/…/BroadcastNotificationCreated.php` lines 101–111).
- Private channel authorization `routes/channels.php`.
- `/broadcasting/auth` endpoint registered by `bootstrap/app.php` → `->withBroadcasting(routes/channels.php, ['middleware' => ['web', 'auth']])`.

### 2.6 Environment variables & config files

```env
BROADCAST_CONNECTION=pusher      # .env line 37 (default would be 'null' per config/broadcasting.php:5)
QUEUE_CONNECTION=sync            # .env line 40 — notifications run inline in dev
PUSHER_APP_ID=                   # EMPTY in this checkout
PUSHER_APP_KEY=                  # EMPTY in this checkout
PUSHER_APP_SECRET=               # EMPTY in this checkout
PUSHER_APP_CLUSTER=ap2
```

- `config/broadcasting.php` — `default` from `BROADCAST_CONNECTION`; `connections.pusher` from `PUSHER_APP_*`.
- `routes/channels.php` — private channel rules.
- `bootstrap/app.php` — broadcasting route registration.
- `.env.example` lines 37–45 document the same keys.

### 2.7 Audio asset

- `public/sounds/notification.wav` — 6,444 bytes, RIFF PCM **16-bit mono 16 kHz WAV** (verified with `file`). Also `public/sounds/.gitkeep`.
- Loaded in `admin-master.blade.php` line 288: `<audio id="adminNotifSound" preload="auto" src="{{ asset('sounds/notification.wav') }}">` (rendered only inside `@can('orders-show')`).
- Subsystem A **does not use this file** — it synthesizes tones procedurally.

### 2.8 CSS assets

- `public/assets/css/admin-order-alert.css` (2,191 B) — gear button, settings panel, bottom-right gradient toast.
- `public/assets/css/admin-notifications.css` (1,772 B) — bell badge, dropdown list, top-right toast.

---

## 3. Complete Notification Workflow

### Step-by-step (happy path, COD order)

1. **Customer submits checkout.**
   `resources/views/frontend/checkout.blade.php` line 43 posts `#checkoutForm` to `route('frontend.order.store')` (`POST /order`) as **AJAX** (`fetch`, header `X-Requested-With: XMLHttpRequest`, line 903).

2. **Backend validates and creates the order.**
   `App\Http\Controllers\Frontend\HomeController::storeOrder()` — prices items (`OrderPricingService`), validates coupon, then `Order::create($orderData)` (line 632).

3. **Order-completion detection (notification trigger #1 — COD).**
   `HomeController::storeOrder()` lines 648–659:
   ```php
   if ($request->payment_method === 'cod') {
       $order->creditMemberPurchase();
       NotifyAdminsOfNewOrder::send($order);          // ← line 651
       return OrderRedirect::respond($request, $order, 'Order placed successfully via Cash on Delivery!', true);
   }
   ```
   For **SSLCommerz**, the order row is created immediately, but notification is deferred until payment is verified:
   - Browser callback: `PaymentController::success()` → validates transaction → `markOrderAsPaid()` (line 65)
   - Server-to-server IPN: `PaymentController::ipn()` → `markOrderAsPaid()` (line 174)
   - `PaymentController::markOrderAsPaid()` lines 194–212 (dedup guard first):
     ```php
     private function markOrderAsPaid(Order $order, array $details): void
     {
         if ($order->payment_status === 'paid') {   // ← early return = no duplicate notification
             return;
         }
         $order->update([... 'payment_status' => 'paid', 'status' => 'confirmed' ...]);
         $order->creditMemberPurchase();
         NotifyAdminsOfNewOrder::send($order);      // ← line 209 (notification trigger #2)
         $this->sendPaymentConfirmationSms($order);
     }
     ```

4. **Recipient resolution + dispatch (backend).**
   `app/Support/NotifyAdminsOfNewOrder.php`:
   ```php
   $admins = User::permission('orders-show')->get();      // Spatie permission scope
   if ($admins->isEmpty()) { return; }                     // no-op if nobody has permission
   try {
       Notification::send($admins, new NewOrderNotification($order));
   } catch (\Throwable $e) {
       Log::error('Failed to notify admins of new order #'.$order->id.': '.$e->getMessage());
   }
   ```

5. **Two channels are written per admin.**
   `app/Notifications/NewOrderNotification.php` (`implements ShouldQueue`):
   - `via()` → `['database', 'broadcast']`
   - `toDatabase()/toArray()` → `{order_id, message: 'নতুন অর্ডার এসেছে', created_at: ISO-8601}` → **row in `notifications` table** (Laravel assigns one shared UUID to all channels — `NotificationSender::sendNow()` line 102).
   - `toBroadcast()` → `BroadcastMessage(payload)` on channel **`App.Models.User.{id}`** (from `User::receivesBroadcastNotificationsOn()`, `app/Models/User.php` line 118), `broadcastType()` = **`new-order-notification`**.
   - With `QUEUE_CONNECTION=sync` this all happens inline inside the HTTP request.

6. **Broadcast reaches Pusher → browser.**
   Laravel server pushes to Pusher. The browser is subscribed on `POST /broadcasting/auth` (authorized by `routes/channels.php`: same user id **and** `$user->can('orders-show')`).

7. **Admin dashboard receives it (subsystem B, realtime).**
   `public/js/admin-notifications.js` line 235:
   ```js
   window.Echo.private('App.Models.User.' + userId)
       .notification(function (notification) {
           if (type !== 'new-order-notification' && !type.includes('NewOrderNotification')) return;
           prependNotification({ id, order_id, message, created_at });  // list, max 10
           setBadge(getBadgeCount() + 1);                               // bell badge +1
           showToast(notification.message || 'নতুন অর্ডার এসেছে');
           playSound();                                                 // WAV, if unlocked
       });
   ```

8. **Admin dashboard detects it (subsystem A, ≤3 s later, independent of Pusher).**
   `public/assets/js/admin-order-alert.js` `pollLatest()` every `POLL_MS = 3000` calls `GET /orders/latest-id` → `OrderController::latestOrderId()`:
   ```php
   public function latestOrderId() {
       $latest = Order::forUserBranch()->latest('id')->value('id');
       return response()->json(['latest_id' => $latest ?? 0]);
   }
   ```
   If `newId > lastKnownId` → `fireNewOrderAlert(diff, newId)` → Web Audio chime + Bootstrap toast + `toastr.success` + `localStorage` ping to other tabs + `window.dispatchEvent(new CustomEvent('dd:new-order', {detail:{diff, latestId}}))`.

9. **Sound plays.**
   - A: `playNewOrderAlert()` synthesizes 880 Hz → 1174 Hz → 988 Hz square-wave tones through a master `GainNode` (volume from `localStorage dd_order_sound_volume`), muted via `dd_order_sound_enabled`. If the tab is hidden (`document.hidden`) it **repeats once after 700 ms**.
   - B: `playSound()` rewinds and plays `#adminNotifSound` (the WAV), only after the user has clicked anywhere once (`soundEnabled` latch), errors swallowed with `.catch(){}`.

10. **Admin opens the new order from the dashboard.**
    - On the Orders page (`resources/views/backend/orders/index.blade.php`), the `dd:new-order` listener (line 592) runs `table.draw(false)` + `refreshCounts()` so the new row appears (highlighted `row-new-order` when `is_new` = `viewed_at IS NULL`).
    - Clicking the bell dropdown renders the latest 10 from DB (`header.blade.php` lines 161–203). Clicking an item POSTs `/notifications/{id}/read` → `NotificationController::markAsRead()` → `markAsRead()` on that row; “Mark all as read” POSTs `/notifications/read-all`. Clicking “View” on a row opens `GET /orders/{order}` (`OrderController::show()`) which stamps `viewed_at`.

---

## 4. Backend Implementation Details

### 4.1 Trigger points (exactly two)

| Payment path | File / function | Line |
|--------------|-----------------|------|
| Cash on Delivery | `app/Http/Controllers/Frontend/HomeController.php::storeOrder()` | 651 |
| SSLCommerz paid (success callback **or** IPN) | `app/Http/Controllers/Frontend/PaymentController.php::markOrderAsPaid()` | 209 |

No Laravel **Event** (`OrderCreated` etc.) is dispatched anywhere — the helper is called directly (grep for `OrderCreated|OrderPlaced|::dispatch(` found no order events). Party bookings, failed/abandoned SSL checkouts, and admin-created orders do **not** trigger subsystem B.

### 4.2 Recipient logic

```php
User::permission('orders-show')->get();   // Spatie — NOT role-based
```

- Sent **once** to the whole collection via `Notification::send()` (not per-user `notify()` loops).
- Empty result → silent no-op.
- All exceptions are caught and logged (`Log::error(...)`) so order placement never fails because of notifications.
- **No branch filtering** — every `orders-show` user gets notified regardless of `branch_id` (contrast with subsystem A, which is branch-scoped via `Order::forUserBranch()`).

### 4.3 Notification class (`app/Notifications/NewOrderNotification.php`)

```php
class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public Order $order) {}
    public function via(object $notifiable): array { return ['database', 'broadcast']; }
    public function toDatabase(object $notifiable): array { return $this->payload(); }
    public function toBroadcast(object $notifiable): BroadcastMessage { return new BroadcastMessage($this->payload()); }
    public function broadcastType(): string { return 'new-order-notification'; }
    private function payload(): array {
        return ['order_id' => $this->order->id,
                'message'  => 'নতুন অর্ডার এসেছে',
                'created_at' => now()->toIso8601String()];
    }
}
```

- `ShouldQueue` + `QUEUE_CONNECTION=sync` ⇒ runs inline in dev.
- Production requirement: `QUEUE_CONNECTION=database|redis` **and** a running `php artisan queue:work`, otherwise **nothing is written to `notifications` and nothing is broadcast** (jobs would just pile up in `jobs` table).

### 4.4 Channel authorization (`routes/channels.php`)

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->can('orders-show');
});
```

Registered in `bootstrap/app.php`:

```php
->withBroadcasting(__DIR__ . '/../routes/channels.php', ['middleware' => ['web', 'auth']])
```

which exposes `POST /broadcasting/auth` under `web + auth` (session cookie + CSRF, Echo sends `X-CSRF-TOKEN`).

### 4.5 HTTP endpoints

| Method | Route name | URI | Controller | Guard |
|--------|-----------|-----|-----------|-------|
| GET | `orders.latestId` | `/orders/latest-id` | `OrderController::latestOrderId()` | constructor middleware `permission:orders-show` + route inside `auth` group |
| POST | `notify.read` | `/notifications/{id}/read` | `NotificationController::markAsRead()` | route middleware `can:orders-show`; `abort_unless(...)`; `firstOrFail()` on **own** notifications only |
| POST | `notify.readAll` | `/notifications/read-all` | `NotificationController::markAllAsRead()` | same |
| GET | `notify.index` | `/notifications/` | `NotificationController::index()` | **stub — `abort(404)`** |
| POST | — | `/broadcasting/auth` | Laravel Broadcast controller | `web + auth` |

All `notify.*` routes live in `routes/web.php` lines 107–116 inside `Route::middleware(['auth','setLocale','user.active'])`.

### 4.6 Deduplication (backend)

- **COD vs SSL** are mutually exclusive for one order → no cross-path duplicate.
- **IPN + success callback** can both call `markOrderAsPaid()` → the `if ($order->payment_status === 'paid') return;` guard ensures the notification is sent only on the first successful paid transition.
- Notification rows are per-user UUIDs; nothing server-side prevents a *user* seeing the same order twice (e.g., re-placed orders) — that is intentional.

---

## 5. Frontend & Sound Implementation

### 5.1 Subsystem A — polling + Web Audio (`public/assets/js/admin-order-alert.js`, 303 lines)

- **Gate:** exits immediately unless `<meta name="orders-latest-url">` exists (only emitted for `@auth @can('orders-show')`, `admin-master.blade.php` lines 60–64).
- **Bootstrap:** one `fetch` seeds `lastKnownId` from `latest_id`, then `setInterval(pollLatest, 3000)` starts (lines 266–281). The seed fetch does **not** alert (avoids a chime on every page load).
- **Alert condition:** `lastKnownId > 0 && newId > lastKnownId` → `diff = newId - lastKnownId` (lines 164–167). Additional guard inside `fireNewOrderAlert`: `if (newId <= lastKnownId) return;`.
- **Sound (Web Audio API, synthesized — no file):**
  ```js
  const tones = [
      { freq: 880,  start: 0,    dur: 0.28, peak: 0.55 },
      { freq: 1174, start: 0.22, dur: 0.32, peak: 0.6  },
      { freq: 988,  start: 0.48, dur: 0.38, peak: 0.65 },
  ];  // oscillator.type = 'square', master GainNode = volume 0.2–1
  ```
- **Autoplay handling:** `ensureAudioContext()` creates/resumes the `AudioContext` (it is `suspended` until a user gesture). One-time listeners on `click | keydown | touchstart` call it (lines 253–261). If never resumed, sound is simply silent — UI still updates.
- **Mute/volume UI:** gear button `#ddOrderAlertGearBtn`, panel `#ddOrderAlertPanel`, slider `#ddOrderAlertVolume` (20–100%), `#ddOrderAlertTestBtn`, persisted in `localStorage`.
- **Background tab:** if `document.hidden`, `playNewOrderAlert(false)` re-fires itself after 700 ms (lines 94–98).
- **Cross-tab sync:** writes `localStorage['dd_order_alert_ping'] = {id, diff, t}`; every other tab listens to the `storage` event and fires if `data.id > lastKnownId` (lines 143–152, 283–295) — so **all tabs of the same browser** chime once each (by design; CSS comment: “Global admin new-order alert (all tabs)”).
- **In-page event:** `window.dispatchEvent(new CustomEvent('dd:new-order', { detail: { diff, latestId } }))` → consumed by `resources/views/backend/orders/index.blade.php` line 592:
  ```js
  window.addEventListener('dd:new-order', function () { table.draw(false); refreshCounts(); });
  ```
- Errors from `fetch` are swallowed (`.catch(function(){})`) — polling just resumes next tick.

### 5.2 Subsystem B — Pusher/Echo + HTML5 Audio (`public/js/admin-notifications.js`, 252 lines)

- **Bootstrap order:** bell elements (`#adminNotifList`, `#adminNotifBadge`, `#adminNotifMarkAll`) come from `resources/views/components/header.blade.php` (server-rendered latest 10 + unread count). If none exist → exit (line 15).
- **Config via meta tags** (`admin-master.blade.php` lines 119–129):
  `admin-user-id`, `pusher-key`, `pusher-cluster`, `csrf-token`, `notify-read-all-url`, `notify-read-url-template` (+ `orders-latest-url`).
- **Echo initialization** (lines 214–233):
  ```js
  window.Echo = new EchoConstructor({
      broadcaster: 'pusher', key: pusherKey,
      cluster: clusterMeta?.content || 'mt1', forceTLS: true,
      authEndpoint: '/broadcasting/auth',
      auth: { headers: { 'X-CSRF-TOKEN': csrfMeta.content } },
  });
  ```
  Handles both `window.Echo` as a function and `window.Echo.default` (echo.iife variants).
- **Subscription:** `.private('App.Models.User.' + userId).notification(cb)`; filters `notification.type === 'new-order-notification'`.
- **Sound (HTML5 Audio):**
  ```js
  let soundEnabled = false;
  document.addEventListener('click', () => { soundEnabled = true; }, { once: true });
  function playSound() {
      if (!soundEnabled || !audioEl) return;
      audioEl.currentTime = 0;
      const p = audioEl.play(); if (p && p.catch) p.catch(function () {});
  }
  ```
  → The WAV only plays **after the admin has clicked at least once** in the tab (browser autoplay policy), and any rejection is silently ignored.
- **UI updates:** `prependNotification()` (HTML-escaped, `timeAgo`, trims DOM to 10 items, removes `#adminNotifEmpty`), `setBadge()` (`99+` cap, `.is-hidden` at 0), `showToast()` (`.admin-notif-toast`, auto-remove 4.5 s).
- **Mark-as-read:** `markOneRead()` POSTs `readUrlTemplate.replace('__ID__', id)` with `X-CSRF-TOKEN`; “Mark all” POSTs `readAllUrl`. Optimistic DOM/badge updates with rollback on failure.
- If Pusher key/cluster is missing or the CDN scripts are blocked → **graceful degradation**: server-rendered bell + AJAX read still work; no live push, no WAV.

### 5.3 Permission & autoplay summary

- **No browser desktop-notification permission is requested** (feature not implemented).
- Audio unlock strategy differs per subsystem: A resumes `AudioContext` on first gesture; B latches `soundEnabled` on first click.
- Everything is gated by the Laravel permission `orders-show` at four layers: Blade (`@can`), route middleware (`permission:orders-show` / `can:orders-show`), controller constructors/aborts, and broadcast channel auth.

---

## 6. Important Files and Their Responsibilities

| File | Responsibility |
|------|----------------|
| `app/Support/NotifyAdminsOfNewOrder.php` | Static `send(Order)`: resolve `orders-show` users → `Notification::send()` → try/catch + log |
| `app/Notifications/NewOrderNotification.php` | `ShouldQueue` notification; `['database','broadcast']`; payload; `broadcastType() = new-order-notification` |
| `app/Http/Controllers/Frontend/HomeController.php` (`storeOrder`, line 651) | COD trigger point after `Order::create` |
| `app/Http/Controllers/Frontend/PaymentController.php` (`markOrderAsPaid`, line 209) | SSLCommerz trigger point with already-paid dedup guard |
| `app/Http/Controllers/Backend/OrderController.php` (`latestOrderId`, lines 158–162) | JSON `{latest_id}` endpoint for poller (branch-scoped) |
| `app/Http/Controllers/NotificationController.php` | `markAsRead` / `markAllAsRead` (JSON); `index()` is `abort(404)` stub |
| `app/Models/User.php` (line 118) | `receivesBroadcastNotificationsOn()` → `App.Models.User.{id}`; uses `Notifiable`, `HasRoles` |
| `app/Models/Order.php` (line 69) | `scopeForUserBranch()` — poller visibility per branch |
| `routes/channels.php` | Private channel auth: own id + `orders-show` |
| `routes/web.php` (107–116, 152–153) | `notify.*` routes, `orders.latestId` route |
| `bootstrap/app.php` (`->withBroadcasting`) | Registers channels + `/broadcasting/auth` with `web+auth` |
| `config/broadcasting.php` | `BROADCAST_CONNECTION` default `null`; pusher connection from env |
| `resources/views/components/admin-master.blade.php` | Meta tags (lines 60–64, 119–129), alert UI + `<audio>` (258–291), script includes (601–608) |
| `resources/views/components/header.blade.php` (161–203) | Bell dropdown, server-rendered latest 10, badge, Mark-all button |
| `public/assets/js/admin-order-alert.js` | Subsystem A: 3 s poll, Web Audio chime, settings panel, localStorage cross-tab ping, `dd:new-order` dispatch |
| `public/js/admin-notifications.js` | Subsystem B: Echo `.notification()`, WAV playback, badge/list/toast, mark-read AJAX |
| `public/assets/css/admin-order-alert.css` | Gear/panel/bottom toast styling |
| `public/assets/css/admin-notifications.css` | Bell badge/dropdown/top toast styling |
| `public/sounds/notification.wav` | WAV played by subsystem B (16-bit mono 16 kHz) |
| `resources/views/backend/orders/index.blade.php` | `dd:new-order` → `table.draw(false)` + `refreshCounts()`; `row-new-order` highlight |
| `tests/Feature/NewOrderNotificationTest.php` | 3 tests: permission-scoped recipients, mark one read, mark all read |
| `docs/superpowers/specs/2026-07-16-admin-order-notifications-design.md` | Original design spec (matches implementation) |
| `settings/pusher-setting.blade.php` + `SettingController::pusherSettingStore` | **Unrelated admin UI** — stores DB settings that this feature never reads |

---

## 7. Configuration & Environment Requirements

**Backend**

1. Laravel 11 + PHP 8.4, migrated DB including `notifications` table (`php artisan migrate`).
2. Composer: `pusher/pusher-php-server` (already in `composer.json`).
3. `.env`:
   ```
   BROADCAST_CONNECTION=pusher
   QUEUE_CONNECTION=sync            # dev; database|redis + queue:work in production
   PUSHER_APP_ID=...
   PUSHER_APP_KEY=...
   PUSHER_APP_SECRET=...
   PUSHER_APP_CLUSTER=...           # e.g. ap2
   ```
4. `config/broadcasting.php`, `routes/channels.php`, `bootstrap/app.php::withBroadcasting()` present (as in this project).
5. Spatie permission `orders-show` seeded (see `database/seeders/RestaurantPermissionSeeder.php`) and assigned to admin roles; `Gate::before` Super Admin bypass in `AppServiceProvider`.

**Frontend (admin layout)**

6. CDN reachability for `js.pusher.com/8.4.0/pusher.min.js` and `cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js` (no npm/Vite involvement).
7. Assets: `admin-order-alert.js`, `admin-notifications.js`, both CSS files, `sounds/notification.wav`.
8. Meta tags emitted in `admin-master.blade.php` (user id, pusher key/cluster, CSRF, latest-id URL, read URLs).

**Audio**

9. WAV file present at `public/sounds/notification.wav` (subsystem B) — subsystem A needs no asset.
10. Admin must interact (click/keypress) once per tab for sound to be allowed by browser autoplay policy.

---

## 8. Technical Limitations

1. **Pusher not configured in this environment** — realtime leg + WAV sound silently disabled (empty `PUSHER_APP_KEY`). Only polling subsystem A is audible today.
2. **DB “Pusher Setting” page is dead code for this feature** — saved values never reach `config('broadcasting.connections.pusher')`; an admin who fills that form gets no effect. Easy to misconfigure.
3. **Double alerts when Pusher IS configured** — subsystem A (≤3 s later: Web Audio chime + bottom toast + toastr) and subsystem B (instant: WAV + bell toast) fire for the same order with **no coordination** between the two scripts. Admins hear two different sounds and see two/three toasts.
4. **False positives from subsystem A on SSLCommerz** — the order row is created *before* payment; `latest_id` advances immediately, so the poller chimes for orders that later fail/cancel. Subsystem B only fires on verified payment.
5. **Branch-scope mismatch** — poller is branch-scoped (`Order::forUserBranch()`), notifications are **not** (`User::permission('orders-show')`): a branch-restricted admin gets bell/sound for other branches’ orders but their table won’t refresh. Also `diff = newId - lastKnownId` uses global IDs, so the “N new orders” count can over-count for branch users.
6. **No desktop notifications / permission flow** — `Notification API` unused; no badge on browser tab icon, no OS-level alert when the tab is closed or the laptop screen is locked (sound requires the page to be open; background-tab `setInterval` throttling can delay polling to ~1 min in intensive throttling).
7. **Offline / disconnect behavior** — polling failures are swallowed and resume next tick (late but self-healing); Pusher auto-reconnects but **missed events during the outage are not replayed** (no resume/replay), so subsystem B can miss notifications while offline — subsystem A’s id-delta logic compensates for the sound, but the bell badge stays stale until reload.
8. **Queue dependency in production** — with a non-`sync` queue and no worker, notifications are queued but never delivered (no DB rows, no broadcast). This is the #1 production failure mode.
9. **Error swallowing** — `NotifyAdminsOfNewOrder` catches all `Throwable`s and only logs; the customer/admin gets no indication if notifications break.
10. **No `orders-show` → no alerts at all** (by design), and `notify.index` is a 404 stub — there is no full notification history page, only the latest-10 dropdown.
11. **CDN dependency** — if CDNs are blocked (offline intranet), Echo/Pusher scripts never load → subsystem B degrades to server-rendered list only (handled gracefully).
12. **Pusher free-plan caps** (noted in config): ~200k messages/day, ~100 concurrent connections — every order = N messages (one per admin).
13. **Volume floor 0.2** and mute are stored in `localStorage` **per browser**, not per user/server.

---

## 9. Step-by-Step Reimplementation Guide (new Laravel 11 project)

> Goal: reproduce **both** subsystems (recommended) or only one. Steps mirror this project exactly.

### Phase 0 — Prerequisites
- Laravel 11, `spatie/laravel-permission`, authenticated admin area with a shared Blade layout, permission `orders-show`:
  ```bash
  composer require spatie/laravel-permission
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
  php artisan migrate
  # seed permission: Permission::create(['name' => 'orders-show', 'group' => 'orders'])
  ```

### Phase 1 — Subsystem B (realtime notification + WAV) backend

1. **Pusher server SDK + config**
   ```bash
   composer require pusher/pusher-php-server
   ```
   `.env`:
   ```
   BROADCAST_CONNECTION=pusher
   QUEUE_CONNECTION=sync
   PUSHER_APP_ID=xxx
   PUSHER_APP_KEY=xxx
   PUSHER_APP_SECRET=xxx
   PUSHER_APP_CLUSTER=ap2
   ```
   `config/broadcasting.php` (copy this project’s file) — `default => env('BROADCAST_CONNECTION','null')` plus the `pusher` connection block.

2. **Notifications table** — `php artisan notifications:table && php artisan migrate` (this project ships `2025_01_28_113101_create_notifications_table.php`).

3. **Broadcast routes** — `bootstrap/app.php`:
   ```php
   ->withBroadcasting(__DIR__ . '/../routes/channels.php', ['middleware' => ['web', 'auth']])
   ```

4. **Channel auth** — `routes/channels.php`:
   ```php
   Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
       return (int) $user->id === (int) $id && $user->can('orders-show');
   });
   ```

5. **User channel override** — `app/Models/User.php`:
   ```php
   use Illuminate\Notifications\Notifiable;
   // ...
   public function receivesBroadcastNotificationsOn(): string {
       return 'App.Models.User.'.$this->id;
   }
   ```

6. **Notification class** — copy `app/Notifications/NewOrderNotification.php` verbatim (see §4.3): `ShouldQueue`, `via() = ['database','broadcast']`, `broadcastType() = 'new-order-notification'`, payload `{order_id, message, created_at}`.

7. **Dispatcher helper** — copy `app/Support/NotifyAdminsOfNewOrder.php` (see §4.2).

8. **Call it from your success paths** (exactly two in this project):
   - COD: right after `Order::create()` + success response (`HomeController::storeOrder`, line 651).
   - Gateway paid: only on the *first* transition to `paid` (`PaymentController::markOrderAsPaid`, line 209, guarded by early return).

9. **Read endpoints** — routes + `NotificationController::markAsRead/markAllAsRead` (copy `routes/web.php` lines 107–116 and the controller). Guard with `can:orders-show` and scope `auth()->user()->notifications()`.

### Phase 2 — Subsystem B frontend

10. **Layout meta tags** (inside `@auth @can('orders-show')`):
    ```blade
    <meta name="admin-user-id"      content="{{ auth()->id() }}">
    <meta name="pusher-key"         content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster"     content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="notify-read-all-url"    content="{{ route('notify.readAll') }}">
    <meta name="notify-read-url-template" content="{{ url('/notifications/__ID__/read') }}">
    ```
    plus `csrf-token` meta (already standard).

11. **Audio element** (before the scripts):
    ```blade
    <audio id="adminNotifSound" preload="auto" src="{{ asset('sounds/notification.wav') }}"></audio>
    <div class="admin-notif-toast-wrap" id="adminNotifToastWrap" aria-live="polite"></div>
    ```

12. **Scripts** (end of `admin-master`):
    ```blade
    <script src="{{ asset('assets/js/admin-order-alert.js') }}" defer></script>   <!-- subsystem A -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="{{ asset('js/admin-notifications.js') }}" defer></script>       <!-- subsystem B -->
    ```

13. **Bell UI** — copy the `@can('orders-show')` block from `header.blade.php` lines 161–203 (server-side latest 10 + unread count + `#adminNotifBadge`, `#adminNotifList`, `#adminNotifMarkAll`, `#adminNotifEmpty`).

14. **JS** — copy `public/js/admin-notifications.js` unchanged (Echo init → `.private('App.Models.User.'+userId).notification(cb)` → prepend/badge/toast/playSound; first-click `soundEnabled` latch; `postJson` with `X-CSRF-TOKEN`).

15. **Sound asset** — place a short chime at `public/sounds/notification.wav` (or adjust `src`). Keep it short (this one is ~6 KB, <1 s) so repeat alerts don’t overlap.

16. **CSS** — copy `public/assets/css/admin-notifications.css`.

### Phase 3 — Subsystem A (polling chime)

17. Meta tag: `<meta name="orders-latest-url" content="{{ route('orders.latestId') }}">` (inside `@can('orders-show')`).
18. Route + endpoint:
    ```php
    Route::get('orders/latest-id', [OrderController::class, 'latestOrderId'])->name('orders.latestId');
    // ...
    public function latestOrderId() {
        $latest = Order::forUserBranch()->latest('id')->value('id'); // or simply Order::max('id')
        return response()->json(['latest_id' => $latest ?? 0]);
    }
    ```
19. Copy `public/assets/js/admin-order-alert.js` (3 s poll, `lastKnownId` dedup, Web Audio tones, gear/volume/mute panel, `localStorage` cross-tab ping, `dd:new-order` CustomEvent) + `admin-order-alert.css` + the `#ddOrderAlertRoot` markup from `admin-master.blade.php` lines 260–287.
20. Optional (orders page): listen for `dd:new-order` → `table.draw(false); refreshCounts();`.

### Phase 4 — Queue & ops

21. Dev: `QUEUE_CONNECTION=sync`. Production: `QUEUE_CONNECTION=database` + `php artisan queue:work --tries=3` (supervisor) — otherwise notifications never leave the queue.
22. Pusher dashboard: create app, take Key/Secret/Cluster; respect free-plan limits (≈200k messages/day, ≈100 connections → size accordingly, one message per admin per order).

### What you do NOT need
- No Laravel Reverb, no Soketi, no npm `laravel-echo`/`pusher-js` (CDN), no custom WebSocket server, no cron/poller for subsystem B, no Web Notifications API permission flow, no `EventServiceProvider` order events (helper is called directly).

---

## 10. Testing & Troubleshooting Checklist

### Automated tests (already in this repo)
`tests/Feature/NewOrderNotificationTest.php`:
- `test_notify_admins_sends_to_users_with_orders_show_only` — asserts only `orders-show` users receive it.
- `test_admin_can_mark_notification_read` — POST `/notifications/{id}/read` → `read_at` set.
- `test_admin_can_mark_all_notifications_read` — POST `/notifications/read-all` → 0 unread.

Run: `php artisan test --filter=NewOrderNotificationTest`.

### Manual test plan (per subsystem)

**Subsystem A (always testable, no Pusher):**
1. Log in as an `orders-show` admin, open any admin page, click once (unlock audio).
2. Place a COD order in another browser/session.
3. Within ≤3 s: bottom-right gradient toast, bottom-left gear turns, chime plays, toastr appears; on the Orders page the table refreshes and the row is highlighted (`viewed_at` NULL).
4. Open a second tab → both tabs chime (localStorage ping).
5. Gear panel: mute → no sound on next order; volume slider changes loudness; “Test sound” always plays.
6. Reload the page → **no** sound on load (seed fetch doesn’t alert).

**Subsystem B (requires real Pusher keys):**
1. Fill `PUSHER_APP_KEY/SECRET/CLUSTER` in `.env` (`BROADCAST_CONNECTION=pusher`, `QUEUE_CONNECTION=sync`), `php artisan config:clear`.
2. Log in as `orders-show` admin, **click once** in the page (sound latch), open DevTools → Network → `wss://…pusher.com` connected, `POST /broadcasting/auth` → 200.
3. Place COD order elsewhere → instantly: bell badge +1, list prepends “Order #…”, top toast, WAV plays.
4. Click item → becomes read, badge −1; “Mark all as read” → badge 0.
5. Log in as a user **without** `orders-show` → no bell, no scripts, `POST /broadcasting/auth` → 403, `/notifications/*` → 403.
6. SSLCommerz sandbox: pay successfully → exactly one notification (IPN + success callback both fire → guard works).

### Troubleshooting matrix

| Symptom | Likely cause | Fix |
|---------|--------------|-----|
| No sound at all (A) | No user gesture yet → `AudioContext` suspended; or muted in `localStorage` (`dd_order_sound_enabled='0'`) | Click once; open gear panel → “Unmute alerts” / “Test sound” |
| No sound at all (B) | Never clicked in tab (`soundEnabled=false`); or Pusher key empty so Echo never initialized | Click once; set `PUSHER_APP_KEY` in `.env`, clear config |
| No live push, bell only shows old items | Empty `pusher-key` meta; CDN blocked; wrong cluster; session expired on `/broadcasting/auth` (401/419) | Check meta tags & DevTools; ensure CDN reachable; re-login; `SESSION_DOMAIN` correct for your host |
| Nothing at all in production with queue ≠ sync | Queue worker not running → notifications stuck in `jobs` | `php artisan queue:work` under supervisor |
| Notifications missing for admins | Users lack `orders-show` permission | `php artisan permission:cache-reset`, assign permission/role |
| Poller silent for branch users | `latestOrderId` scoped by `Order::forUserBranch()` and no new order in *their* branch | Expected; verify branch assignment of orders |
| Poller chimes for failed SSL orders | Order row created before payment (by design) | Consider triggering A only on paid if undesired |
| Two sounds / double toast per order | Both subsystems active and uncoordinated | Expected today; if unwanted, disable one (e.g., don’t load `admin-order-alert.js`, or remove `broadcast` from `via()`) |
| 419 on mark-read or `/broadcasting/auth` | CSRF token missing/expired | Ensure `csrf-token` meta present; Echo auth headers |
| Stale badge after offline period | No replay of missed Pusher events | Reload page (DB re-render) — polling still catches the sound |
| DB Pusher Setting page has no effect | Values stored in `settings` table are never read into config | Use `.env` only (per design doc) |
| Config cached with wrong keys | `config:cache` baked empty PUSHER keys | `php artisan config:clear` after editing `.env` |

---

### Appendix — Verified facts vs. common assumptions

| Assumption | Reality (verified in source) |
|------------|------------------------------|
| “It uses Laravel Reverb / custom WebSocket server” | **No** — Pusher SaaS + CDN pusher-js/Echo |
| “It polls an API for notifications” | Only subsystem A polls (`/orders/latest-id`); subsystem B is true push via Pusher |
| “The sound is an MP3/HTML5 file” | Only subsystem B uses a file (`notification.wav`); subsystem A **synthesizes** tones with Web Audio |
| “The admin Pusher settings page configures it” | **No** — credentials come from `.env` only; DB settings are unused for this feature |
| “It asks for browser notification permission” | **No** — Web Notifications API is not used at all |
| “It uses Laravel Events/Listeners for new orders” | **No** — direct static helper call at 2 code sites |
| “Echo is bundled by Vite/npm” | **No** — CDN `<script>` tags in the admin layout |
