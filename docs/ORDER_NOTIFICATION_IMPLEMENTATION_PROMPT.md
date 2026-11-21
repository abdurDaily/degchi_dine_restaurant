# Order Notification Sound System — Implementation Prompt (for a NEW Laravel Project)

> **How to use:** Copy everything inside the fenced block below and paste it into a fresh
> agent/developer session pointed at the target project. It is fully self-contained and
> references the working reference implementation at
> `F:/LARAVEL OFFICE PROJECT/degchi_dine_restaurant/` (same machine) as the source of truth.
>
> Edit the **CONFIG** block at the top (permission name, layout file, checkout path) to match
> the target project before running it.

````text
# TASK: Implement "New Order Notification + Sound Alert" in the Admin Dashboard

## ROLE
Act as a Senior Laravel Full-Stack Developer. Implement a complete admin order-notification
system in THIS project, mirroring a proven reference implementation. Do not refactor or
touch unrelated code. Read the reference files before writing anything.

## REFERENCE IMPLEMENTATION (read these first — copy their exact patterns)
Reference project root: `F:/LARAVEL OFFICE PROJECT/degchi_dine_restaurant/`

Backend:
- `app/Support/NotifyAdminsOfNewOrder.php`                  (dispatcher helper)
- `app/Notifications/NewOrderNotification.php`              (database + broadcast notification)
- `routes/channels.php`                                     (private channel auth)
- `bootstrap/app.php`                                       (`->withBroadcasting(...)` registration)
- `config/broadcasting.php`                                 (Pusher block from env)
- `app/Http/Controllers/NotificationController.php`         (mark one / mark all read)
- `app/Models/User.php`                                     (`receivesBroadcastNotificationsOn()`)
- `routes/web.php` lines ~107-116 and ~152-153              (notify.* + orders.latestId routes)
- Trigger points: `app/Http/Controllers/Frontend/HomeController.php::storeOrder()` (COD)
  and `app/Http/Controllers/Frontend/PaymentController.php::markOrderAsPaid()` (gateway paid)

Frontend:
- `resources/views/components/admin-master.blade.php`       (meta tags, <audio>, script includes,
                                                             alert panel markup — lines 60-64,
                                                             119-129, 258-291, 601-608)
- `resources/views/components/header.blade.php` lines 161-203 (notification bell dropdown)
- `public/assets/js/admin-order-alert.js`                   (subsystem A: poller + Web Audio sound)
- `public/js/admin-notifications.js`                        (subsystem B: Pusher/Echo + WAV sound)
- `public/assets/css/admin-order-alert.css`, `public/assets/css/admin-notifications.css`
- `public/sounds/notification.wav`                          (copy this exact asset)
- `resources/views/backend/orders/index.blade.php` line 592 (`dd:new-order` listener)

## CONFIG (adapt to THIS project)
- Admin layout file: <e.g. `resources/views/components/admin-master.blade.php` or equivalent>
- Topbar/header component: <e.g. `resources/views/components/header.blade.php` or equivalent>
- Orders index page: `resources/views/backend/orders/index.blade.php`
- Orders table model: `App\Models\Order` (must have auto-increment `id`)
- Permission that grants order access: `orders-show` (Spatie laravel-permission)
- Order-creation endpoint(s): <name the controller/method(s) that create/complete an order>
- Frontend checkout route that submits the order: <route name or URL>

## SYSTEM TO BUILD (two cooperating subsystems — build BOTH)

### Subsystem A — Polling chime (works with NO third-party services) — MANDATORY
1. Add meta tag in the admin layout inside `@auth @can('orders-show')`:
   `<meta name="orders-latest-url" content="{{ route('orders.latestId') }}">`
2. Route `GET /orders/latest-id` → `OrderController::latestOrderId()` returning
   `{"latest_id": <int>}` (`Order::max('id')`, or branch-scoped if the project has branches),
   protected by the same permission as the orders page.
3. Port `public/assets/js/admin-order-alert.js` + its CSS + the `#ddOrderAlertRoot`
   markup (toast, gear button, volume/mute panel) verbatim, keeping these behaviors:
   - Poll with `fetch()` every 3000 ms; one seed fetch on page load that sets `lastKnownId`
     WITHOUT alerting (never chime on page load/reload).
   - Fire only when `newId > lastKnownId` (dedup); update `lastKnownId`.
   - Sound = Web Audio API synthesized 3-tone square-wave chime
     (880 Hz / 1174 Hz / 988 Hz, master GainNode volume 0.2–1) — NOT an audio file.
   - Resume/create `AudioContext` on the first user gesture
     (`click`/`keydown`/`touchstart`, `{once:true, passive:true}`) — autoplay-policy unlock.
   - Mute + volume persisted in `localStorage` (`dd_order_sound_enabled`,
     `dd_order_sound_volume`), with a "Test sound" button.
   - If `document.hidden`, replay the alert once after 700 ms.
   - Cross-tab sync via `localStorage['dd_order_alert_ping']` + `storage` event listener.
   - Dispatch `window.dispatchEvent(new CustomEvent('dd:new-order', {detail:{diff, latestId}}))`.
   - Show Bootstrap toast + toastr on alert. All fetch errors swallowed silently.
4. In `resources/views/backend/orders/index.blade.php`, add
   `window.addEventListener('dd:new-order', () => { table.draw(false); refreshCounts(); });`
   (or refresh whatever list shows orders on the target page).

### Subsystem B — Realtime notification + WAV sound (Pusher) — MANDATORY
1. `composer require pusher/pusher-php-server` (no laravel/echo PHP package, no Reverb,
   no npm echo/pusher-js — browser side uses CDN scripts only).
2. `.env` (+ `.env.example` docs):
   ```
   BROADCAST_CONNECTION=pusher
   QUEUE_CONNECTION=sync
   PUSHER_APP_ID=
   PUSHER_APP_KEY=
   PUSHER_APP_SECRET=
   PUSHER_APP_CLUSTER=ap2
   ```
   Add the `pusher` connection block to `config/broadcasting.php` exactly like the reference.
3. Create the `notifications` table (`php artisan notifications:table && php artisan migrate`).
4. `bootstrap/app.php`: `->withBroadcasting(__DIR__.'/../routes/channels.php', ['middleware'=>['web','auth']])`
   so `POST /broadcasting/auth` exists.
5. `routes/channels.php`:
   `Broadcast::channel('App.Models.User.{id}', fn($user, $id) => (int)$user->id === (int)$id && $user->can('orders-show'));`
6. `App\Models\User`: use `Notifiable` (already default) and override
   `receivesBroadcastNotificationsOn(): string { return 'App.Models.User.'.$this->id; }`
7. Create `app/Notifications/NewOrderNotification.php` copying the reference:
   - `implements ShouldQueue`, `use Queueable`
   - `via()` → `['database','broadcast']`
   - payload: `order_id`, `message` (localized/new-order text), `created_at => now()->toIso8601String()`
   - `toBroadcast()` → `new BroadcastMessage($this->payload())`
   - `broadcastType(): string { return 'new-order-notification'; }`
8. Create `app/Support/NotifyAdminsOfNewOrder.php` copying the reference:
   `User::permission('orders-show')->get()` → empty → return →
   `Notification::send($admins, new NewOrderNotification($order))` inside try/catch + `Log::error`.
9. Trigger EXACTLY at order completion, both paths:
   - Cash-on-delivery: immediately after successful `Order::create()` + before the success
     response/redirect in the checkout controller method.
   - Paid gateway: only inside the "mark as paid" routine, AFTER an early-return guard
     `if ($order->payment_status === 'paid') return;` so success-callback + IPN can never
     double-send. Never notify on order creation alone for gateway orders.
   - No Laravel Event/Listener class is needed — call the helper directly (same as reference).
10. Routes + controller (permission-gated with `can:orders-show`, inside the authenticated
    admin group):
    - `POST /notifications/{id}/read` → mark ONE (query ONLY `auth()->user()->notifications()`,
      `firstOrFail`, `markAsRead()`, return `{"success": true}`)
    - `POST /notifications/read-all` → mark ALL (`auth()->user()->unreadNotifications->markAsRead()`)
    - `GET /notifications/` may stay a 404 stub (reference does this).
11. Admin layout — inside `@auth @can('orders-show')` add (mirror reference exactly):
    ```blade
    <meta name="admin-user-id" content="{{ auth()->id() }}">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="notify-read-all-url" content="{{ route('notify.readAll') }}">
    <meta name="notify-read-url-template" content="{{ url('/notifications/__ID__/read') }}">
    ```
    plus the alert-panel markup, and:
    ```blade
    <audio id="adminNotifSound" preload="auto" src="{{ asset('sounds/notification.wav') }}"></audio>
    <div class="admin-notif-toast-wrap" id="adminNotifToastWrap" aria-live="polite"></div>
    ```
    and at the end of `<body>`:
    ```blade
    @auth @can('orders-show')
    <script src="{{ asset('assets/js/admin-order-alert.js') }}" defer></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="{{ asset('js/admin-notifications.js') }}" defer></script>
    @endcan @endauth
    ```
12. Bell dropdown in the topbar component (copy reference block): server-render latest 10
    (`auth()->user()->notifications()->latest()->take(10)->get()`), unread count badge
    `#adminNotifBadge`, list `#adminNotifList`, `#adminNotifMarkAll` button, `#adminNotifEmpty`
    state, `@can('orders-show')` gate.
13. Port `public/js/admin-notifications.js` verbatim, keeping these behaviors:
    - Exit early if bell elements or config meta missing (graceful degradation).
    - `soundEnabled = false` until the FIRST document `click` (`{once:true}`) — autoplay unlock;
      `audioEl.currentTime = 0; audioEl.play().catch(()=>{})`.
    - Init `window.Echo = new (window.Echo?.default || window.Echo)({ broadcaster:'pusher',
      key, cluster, forceTLS:true, authEndpoint:'/broadcasting/auth',
      auth:{headers:{'X-CSRF-TOKEN': csrf}} })`.
    - `Echo.private('App.Models.User.'+userId).notification(cb)` filtered to
      `type === 'new-order-notification'` → prepend item (escape HTML, timeAgo, cap at 10),
      badge +1 (99+ cap), toast, `playSound()`.
    - Mark-one/mark-all via `fetch` POST with `X-CSRF-TOKEN`, optimistic UI + rollback.
14. Copy both CSS files and `public/sounds/notification.wav`.

## HARD CONSTRAINTS
- Do NOT modify any existing code beyond the files listed; no refactoring, no renaming,
  no formatting-only edits.
- Do NOT use Laravel Reverb/Soketi/custom WebSocket servers, npm laravel-echo/pusher-js,
  or Vite bundling for these scripts (CDN only, same versions as reference).
- Do NOT use the browser Web Notifications API (`new Notification()`) or request desktop
  notification permission — not part of this system.
- Do NOT wire any admin "settings page" DB values into broadcasting config — `.env` only.
- Everything (scripts, audio, bell, routes, channel auth) MUST be gated by the
  `orders-show` permission at every layer: Blade `@can`, route middleware, controller
  abort, and broadcast channel auth.
- If the target project has branch-scoped orders, scope `latest_id` with its branch scope,
  but keep notification recipients as all `orders-show` users (same as reference).
- Sound must never break order placement: all notification errors caught + logged.
- Expected production note to include in your summary:
  `QUEUE_CONNECTION=database|redis` requires a running `php artisan queue:work`,
  otherwise notifications never send. Pusher free plan ≈200k msgs/day, 100 connections.

## ACCEPTANCE CRITERIA (verify each before finishing)
1. `php artisan route:list` shows `/orders/latest-id`, `/notifications/{id}/read`,
   `/notifications/read-all`, `/broadcasting/auth`.
2. Place a COD order (or create an `Order` row while an admin session is open):
   within ≤3 s the admin page chimes, shows the toast, and
   - orders table: new row appears (redraw) + counts refresh; if `viewed_at` column exists,
     the row gets the "new" highlight until opened.
3. Page load/reload produces NO sound (seed fetch only).
4. Mute + volume + "Test sound" work and survive reload (localStorage).
5. Two open tabs both chime once per order (localStorage ping), no duplicate chime within
   one tab for one order (id-delta dedup).
6. With valid Pusher keys in `.env`: admin hears the WAV too, bell badge +1, list prepends
   the order, toast shows — all WITHOUT page refresh; `POST /broadcasting/auth` returns 200
   and a `wss://…pusher.com` connection is established.
7. With empty Pusher keys: no JS errors, no live push, bell still renders from DB,
   subsystem A still chimes (graceful degradation).
8. A user WITHOUT `orders-show` gets no bell, no scripts, 403 on notify routes and on
   `/broadcasting/auth`.
9. Gateway double-callback (success + IPN) results in EXACTLY ONE notification.
10. `php artisan test` (or Pest) passes; add a feature test mirroring
    `tests/Feature/NewOrderNotificationTest.php`: recipients = only `orders-show` users,
    mark-one-read, mark-all-read.
11. Documented manual test: open admin in one browser, place order from another/incognito.

## OUTPUT FORMAT
1. Short summary table of files created vs. modified.
2. Any adaptations you had to make for THIS project (layout names, branch scoping,
   checkout path) — with reasons.
3. The acceptance-checklist results.
4. Deployment notes: `.env` keys to fill, queue worker command, cache clears
   (`php artisan config:clear && php artisan cache:clear`).
````