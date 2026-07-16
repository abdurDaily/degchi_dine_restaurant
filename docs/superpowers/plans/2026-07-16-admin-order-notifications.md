# Admin Real-Time New Order Notifications Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give every admin with `orders-show` a real-time Pusher notification (DB + broadcast) when an order successfully completes, with a navbar bell, unread badge, mark-as-read AJAX, toast, and unlockable sound — without replacing the existing polling order alert.

**Architecture:** `NotifyAdminsOfNewOrder` sends `NewOrderNotification` (`database` + `broadcast`) to `User::permission('orders-show')` after COD success and after the first successful `markOrderAsPaid`. Echo (CDN) subscribes to `App.Models.User.{id}` via session-auth `/broadcasting/auth` and listens with `.notification()`.

**Tech Stack:** Laravel 11, Spatie Permission, Pusher (`pusher/pusher-php-server`), Laravel Echo + pusher-js (CDN), Blade admin (Velzon), vanilla JS, `QUEUE_CONNECTION=sync` locally.

**Spec:** `docs/superpowers/specs/2026-07-16-admin-order-notifications-design.md`

## Global Constraints

- Recipients: Spatie permission `orders-show` only (never `users.role = 'admin'`).
- Pusher credentials: `.env` only (ignore DB `pusher_setting`).
- Keep existing `admin-order-alert.js` / gear panel unchanged.
- Notify only on successful completion (COD path + first paid transition in `markOrderAsPaid`).
- Private channel name must be exactly `App.Models.User.{id}`.
- Bangla message text: `নতুন অর্ডার এসেছে`.
- Broadcast type / event name: `new-order-notification`.
- Echo via CDN (not Vite) for admin layout.
- Do not commit unless the user explicitly asks (skip commit steps during execution if not requested).

## File Structure

| File | Responsibility |
|------|----------------|
| `config/broadcasting.php` | Pusher + broadcast driver config from env |
| `routes/channels.php` | Authorize `App.Models.User.{id}` for self + `orders-show` |
| `app/Providers/BroadcastServiceProvider.php` | `Broadcast::routes` with `web`+`auth`; load channels |
| `bootstrap/providers.php` | Register BroadcastServiceProvider |
| `bootstrap/app.php` | Register `channels` routing path |
| `app/Notifications/NewOrderNotification.php` | DB + broadcast payload |
| `app/Support/NotifyAdminsOfNewOrder.php` | Single send helper |
| `app/Models/User.php` | Optional `receivesBroadcastNotificationsOn()` |
| `app/Http/Controllers/Frontend/HomeController.php` | COD trigger |
| `app/Http/Controllers/Frontend/PaymentController.php` | Paid trigger |
| `app/Http/Controllers/NotificationController.php` | Mark read / mark all |
| `routes/web.php` | Notification POST routes |
| `resources/views/components/header.blade.php` | Bell + dropdown markup |
| `resources/views/components/admin-master.blade.php` | CDN, CSS, audio, JS, meta config |
| `public/js/admin-notifications.js` | Echo, DOM, toast, sound, AJAX |
| `public/assets/css/admin-notifications.css` | Bell/badge/dropdown/toast styles |
| `public/sounds/notification.mp3` | Alert audio asset |
| `.env.example` | Document BROADCAST/PUSHER keys |
| `tests/Feature/NewOrderNotificationTest.php` | Helper + mark-read coverage |

---

### Task 1: Broadcasting infrastructure

**Files:**
- Create: `config/broadcasting.php`
- Create: `routes/channels.php`
- Create: `app/Providers/BroadcastServiceProvider.php`
- Modify: `bootstrap/providers.php`
- Modify: `bootstrap/app.php`
- Modify: `.env.example`
- Modify: `composer.json` / `composer.lock` (via composer require)

**Interfaces:**
- Consumes: none
- Produces: working `/broadcasting/auth` under `web`+`auth`; channel callback for `App.Models.User.{id}`

- [ ] **Step 1: Install Pusher PHP SDK**

Run:

```bash
composer require pusher/pusher-php-server
```

Expected: package added to `composer.json` / lockfile without errors.

- [ ] **Step 2: Create `config/broadcasting.php`**

Create the full file (Laravel 11-style). Include this Pusher block and a free-plan limit comment:

```php
<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

        'pusher' => [
            // Pusher free plan approx limits: 200k messages/day, 100 concurrent connections.
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
            'client_options' => [],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
```

- [ ] **Step 3: Create `routes/channels.php`**

```php
<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->can('orders-show');
});
```

- [ ] **Step 4: Create `app/Providers/BroadcastServiceProvider.php`**

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web', 'auth']]);

        require base_path('routes/channels.php');
    }
}
```

- [ ] **Step 5: Register provider**

In `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
];
```

- [ ] **Step 6: Register channels path in `bootstrap/app.php`**

Change `withRouting` to include `channels:` (keep existing `then` admin group):

```php
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
```

Note: `BroadcastServiceProvider` already `require`s `channels.php`. Loading via both `channels:` and the provider is Laravel’s common pattern; if double-registration of channel callbacks becomes an issue, keep the provider’s `Broadcast::routes` + `require` and still pass `channels:` for framework discovery — or load channels only once. Prefer: provider handles `Broadcast::routes` + `require channels.php`; `channels:` in `withRouting` is still recommended by Laravel 11 docs. If channels register twice in testing, remove the `require` from the provider and rely on `channels:` only, keeping `Broadcast::routes` in the provider.

**Preferred final split to avoid double registration:**

- `bootstrap/app.php`: pass `channels: __DIR__.'/../routes/channels.php'`
- `BroadcastServiceProvider::boot`: only `Broadcast::routes(['middleware' => ['web', 'auth']]);` (no require)

- [ ] **Step 7: Update `.env.example`**

Replace/extend broadcast section:

```
BROADCAST_CONNECTION=pusher
# Local/dev: use sync so ShouldQueue notifications broadcast immediately.
# Production: database or redis + run `php artisan queue:work`
QUEUE_CONNECTION=sync

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=ap2
```

(Keep other `.env.example` keys intact; if `QUEUE_CONNECTION=database` already exists, change the documented default for local to `sync` with the production comment above.)

- [ ] **Step 8: Smoke-check config loads**

Run:

```bash
php artisan config:clear
php artisan route:list --path=broadcasting
```

Expected: `POST broadcasting/auth` listed (or equivalent broadcast auth route).

- [ ] **Step 9: Commit** (only if user requested commits)

```bash
git add composer.json composer.lock config/broadcasting.php routes/channels.php app/Providers/BroadcastServiceProvider.php bootstrap/providers.php bootstrap/app.php .env.example
git commit -m "$(cat <<'EOF'
Add Pusher broadcasting infrastructure for admin notifications.

EOF
)"
```

---

### Task 2: Notification class + helper + User channel

**Files:**
- Create: `app/Notifications/NewOrderNotification.php`
- Create: `app/Support/NotifyAdminsOfNewOrder.php`
- Modify: `app/Models/User.php`
- Create: `tests/Feature/NewOrderNotificationTest.php`

**Interfaces:**
- Consumes: `App\Models\Order`, `App\Models\User`, Spatie `permission('orders-show')`
- Produces:
  - `new NewOrderNotification(Order $order)`
  - `NotifyAdminsOfNewOrder::send(Order $order): void`
  - Payload keys: `order_id`, `message`, `created_at`
  - `broadcastType()` / type: `new-order-notification`
  - Channel: `App.Models.User.{id}` via `User::receivesBroadcastNotificationsOn()`

- [ ] **Step 1: Write the failing feature test**

Create `tests/Feature/NewOrderNotificationTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Support\NotifyAdminsOfNewOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NewOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notify_admins_sends_to_users_with_orders_show_only(): void
    {
        Permission::findOrCreate('orders-show', 'web');

        $admin = User::factory()->create(['status' => true]);
        $admin->givePermissionTo('orders-show');

        $other = User::factory()->create(['status' => true]);

        $order = Order::factory()->create(); // if OrderFactory missing, create via Order::create([...minimal...])

        Notification::fake();

        NotifyAdminsOfNewOrder::send($order);

        Notification::assertSentTo($admin, NewOrderNotification::class);
        Notification::assertNotSentTo($other, NewOrderNotification::class);
    }
}
```

**If `OrderFactory` does not exist:** inspect `Order` model `$fillable` / migrations and create the order with required columns inline in the test (minimal pending COD-like row). Do not invent unrelated factories.

- [ ] **Step 2: Run test — expect fail**

Run:

```bash
php artisan test --filter=NewOrderNotificationTest
```

Expected: FAIL (class/helper missing or Order create issues). Fix Order creation in the test if schema requires fields before continuing.

- [ ] **Step 3: Add `receivesBroadcastNotificationsOn` on User**

In `app/Models/User.php` add:

```php
public function receivesBroadcastNotificationsOn(): string
{
    return 'App.Models.User.'.$this->id;
}
```

- [ ] **Step 4: Create `NewOrderNotification`**

```php
<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    public function broadcastType(): string
    {
        return 'new-order-notification';
    }

    private function payload(): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'নতুন অর্ডার এসেছে',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
```

Note: Laravel notification broadcasting uses `broadcastType()` (not a separate Event `broadcastAs`). Echo `.notification()` receives `notification.type === 'new-order-notification'`. Do **not** also implement `ShouldBroadcast` on the Notification class — the `broadcast` channel already emits `BroadcastNotificationCreated`; implementing both is redundant and can confuse the queue.

- [ ] **Step 5: Create helper**

```php
<?php

namespace App\Support;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewOrder
{
    public static function send(Order $order): void
    {
        $admins = User::permission('orders-show')->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewOrderNotification($order));
    }
}
```

- [ ] **Step 6: Run test — expect pass**

```bash
php artisan test --filter=NewOrderNotificationTest
```

Expected: PASS. If Spatie permission tables need seeding under `RefreshDatabase`, ensure permission migrations run (they should with full migrate). If `status` column has no default, factory must set it.

- [ ] **Step 7: Commit** (only if user requested)

```bash
git add app/Notifications/NewOrderNotification.php app/Support/NotifyAdminsOfNewOrder.php app/Models/User.php tests/Feature/NewOrderNotificationTest.php
git commit -m "$(cat <<'EOF'
Add NewOrderNotification and admin notify helper.

EOF
)"
```

---

### Task 3: Trigger on COD and paid SSLCommerz

**Files:**
- Modify: `app/Http/Controllers/Frontend/HomeController.php` (COD block ~537–545)
- Modify: `app/Http/Controllers/Frontend/PaymentController.php` (`markOrderAsPaid`)
- Modify: `tests/Feature/NewOrderNotificationTest.php` (optional trigger assertions with `Notification::fake`)

**Interfaces:**
- Consumes: `NotifyAdminsOfNewOrder::send(Order $order): void`
- Produces: notification on COD success; notification once when order first becomes paid

- [ ] **Step 1: Wire COD path**

In `HomeController::storeOrder`, inside the COD branch, call the helper **after** `creditMemberPurchase()` and **before** `OrderRedirect::respond`:

```php
if ($request->payment_method === 'cod') {
    $order->creditMemberPurchase();

    \App\Support\NotifyAdminsOfNewOrder::send($order);

    return OrderRedirect::respond(
        $request,
        $order,
        'Order placed successfully via Cash on Delivery!',
        true
    );
}
```

Prefer a proper `use App\Support\NotifyAdminsOfNewOrder;` at the top of the file instead of FQCN.

- [ ] **Step 2: Wire `markOrderAsPaid` without double-send**

Replace `markOrderAsPaid` body so notify runs only on first paid transition:

```php
private function markOrderAsPaid(Order $order, array $details): void
{
    if ($order->payment_status === 'paid') {
        return;
    }

    $order->update([
        'payment_status'  => 'paid',
        'payment_date'    => now(),
        'status'          => 'confirmed',
        'payment_details' => json_encode($details),
    ]);

    $order->creditMemberPurchase();

    NotifyAdminsOfNewOrder::send($order);

    $this->sendPaymentConfirmationSms($order);
}
```

Add `use App\Support\NotifyAdminsOfNewOrder;` at top of `PaymentController`.

- [ ] **Step 3: Quick sanity with Notification::fake (optional but recommended)**

Extend the feature test or add a unit-style test that instantiates PaymentController via reflection only if heavy; otherwise manually verify later. Minimum: keep Task 2 test green.

```bash
php artisan test --filter=NewOrderNotificationTest
```

- [ ] **Step 4: Commit** (only if user requested)

```bash
git add app/Http/Controllers/Frontend/HomeController.php app/Http/Controllers/Frontend/PaymentController.php
git commit -m "$(cat <<'EOF'
Notify admins when COD or paid orders complete.

EOF
)"
```

---

### Task 4: Mark-as-read API

**Files:**
- Modify: `app/Http/Controllers/NotificationController.php`
- Modify: `routes/web.php` (notifications group ~103–106)
- Modify: `tests/Feature/NewOrderNotificationTest.php`

**Interfaces:**
- Consumes: `auth()->user()->notifications()`
- Produces:
  - `POST /notifications/{id}/read` → `notify.read` → JSON `{ success: true }`
  - `POST /notifications/read-all` → `notify.readAll` → JSON `{ success: true }`

- [ ] **Step 1: Write failing HTTP tests**

Append to `NewOrderNotificationTest`:

```php
public function test_admin_can_mark_notification_read(): void
{
    Permission::findOrCreate('orders-show', 'web');
    $admin = User::factory()->create(['status' => true]);
    $admin->givePermissionTo('orders-show');

    $admin->notify(new NewOrderNotification(
        Order::query()->first() ?? $this->makeOrder()
    ));

    $notification = $admin->notifications()->first();

    $this->actingAs($admin)
        ->postJson(route('notify.read', $notification->id))
        ->assertOk()
        ->assertJson(['success' => true]);

    $this->assertNotNull($notification->fresh()->read_at);
}

public function test_admin_can_mark_all_notifications_read(): void
{
    Permission::findOrCreate('orders-show', 'web');
    $admin = User::factory()->create(['status' => true]);
    $admin->givePermissionTo('orders-show');
    $order = Order::query()->first() ?? $this->makeOrder();

    $admin->notify(new NewOrderNotification($order));
    $admin->notify(new NewOrderNotification($order));

    $this->actingAs($admin)
        ->postJson(route('notify.readAll'))
        ->assertOk();

    $this->assertSame(0, $admin->unreadNotifications()->count());
}
```

Add a private `makeOrder()` helper in the test class matching required Order columns.

- [ ] **Step 2: Run — expect fail (routes/methods missing)**

```bash
php artisan test --filter=NewOrderNotificationTest
```

- [ ] **Step 3: Implement controller**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        abort(404);
    }

    public function markAsRead(string $id)
    {
        abort_unless(auth()->user()->can('orders-show'), 403);

        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        abort_unless(auth()->user()->can('orders-show'), 403);

        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
```

- [ ] **Step 4: Add routes**

Replace the notifications group in `routes/web.php` with:

```php
Route::controller(NotificationController::class)
    ->prefix('notifications')
    ->name('notify.')
    ->middleware('can:orders-show')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('{id}/read', 'markAsRead')->name('read');
        Route::post('read-all', 'markAllAsRead')->name('readAll');
    });
```

Important: declare `read-all` **before** `{id}/read` is fine as written above since paths differ; keep `read-all` as a static path (already is).

- [ ] **Step 5: Run tests — expect pass**

```bash
php artisan test --filter=NewOrderNotificationTest
```

- [ ] **Step 6: Commit** (only if user requested)

```bash
git add app/Http/Controllers/NotificationController.php routes/web.php tests/Feature/NewOrderNotificationTest.php
git commit -m "$(cat <<'EOF'
Add admin notification mark-as-read endpoints.

EOF
)"
```

---

### Task 5: Admin UI — bell, CSS, audio, Echo JS

**Files:**
- Create: `public/assets/css/admin-notifications.css`
- Create: `public/js/admin-notifications.js`
- Create: `public/sounds/.gitkeep` and `public/sounds/notification.mp3` (or `.wav` if mp3 unavailable — update `src` accordingly)
- Modify: `resources/views/components/header.blade.php`
- Modify: `resources/views/components/admin-master.blade.php`

**Interfaces:**
- Consumes: routes `notify.read`, `notify.readAll`; env `PUSHER_APP_KEY`, `PUSHER_APP_CLUSTER`; `auth()->id()`, CSRF meta
- Produces: live UI updates via Echo `.notification()`

- [ ] **Step 1: Add notification sound asset**

Create `public/sounds/`. Generate a short valid audio file. Preferred: small WAV written by PHP so the repo has a real playable file:

```bash
php -r "$rate=16000;$f=440;$dur=0.25;$n=(int)($rate*$dur);$data='';for($i=0;$i<$n;$i++){$data.=pack('v',(int)(30000*sin(2*M_PI*$f*$i/$rate)));} $hdr=pack('V','1179011410'); /* prefer writing a known-good tiny wav */"
```

Simpler approach — write this PHP one-off once and save as `public/sounds/notification.wav`, then point the Blade `<audio>` at it (browsers accept wav). Or download a short royalty-free mp3 into `public/sounds/notification.mp3`. **Do not leave a broken empty file.**

- [ ] **Step 2: Create CSS** `public/assets/css/admin-notifications.css`

```css
.admin-notif-dropdown {
  width: 320px;
  max-height: 420px;
  padding: 0;
  overflow: hidden;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
}

.admin-notif-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--vz-border-color, #e9ebec);
}

.admin-notif-list {
  max-height: 320px;
  overflow-y: auto;
}

.admin-notif-item {
  display: block;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
  text-decoration: none;
  color: inherit;
  cursor: pointer;
}

.admin-notif-item.unread {
  background: rgba(64, 81, 137, 0.06);
}

.admin-notif-item:hover {
  background: rgba(0, 0, 0, 0.03);
}

.admin-notif-empty {
  padding: 1.25rem 1rem;
  text-align: center;
  color: #878a99;
  font-size: 0.875rem;
}

.admin-notif-badge {
  position: absolute;
  top: 8px;
  right: 6px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  border-radius: 50rem;
  background: #f06548;
  color: #fff;
  font-size: 10px;
  line-height: 16px;
  text-align: center;
}

.admin-notif-badge.is-hidden {
  display: none !important;
}

.admin-notif-toast-wrap {
  position: fixed;
  top: 1.25rem;
  right: 1.25rem;
  z-index: 1090;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  pointer-events: none;
}

.admin-notif-toast {
  min-width: 260px;
  max-width: 360px;
  padding: 0.85rem 1rem;
  border-radius: 0.4rem;
  background: #212529;
  color: #fff;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2);
  animation: adminNotifSlideIn 0.25s ease-out;
  pointer-events: auto;
}

@keyframes adminNotifSlideIn {
  from { transform: translateX(120%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}
```

- [ ] **Step 3: Insert bell into `header.blade.php`**

Insert **before** the fullscreen button block (`ms-1 header-item` with `bx-fullscreen`), wrapped in `@can('orders-show')`. Server-render latest 10 + unread count:

```blade
@can('orders-show')
@php
    $adminNotifications = auth()->user()->notifications()->latest()->take(10)->get();
    $adminUnreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<div class="dropdown topbar-head-dropdown ms-1 header-item" id="adminNotificationDropdown">
    <button type="button"
        class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
        id="page-header-notifications-dropdown"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="bx bx-bell fs-22"></i>
        <span class="admin-notif-badge {{ $adminUnreadCount > 0 ? '' : 'is-hidden' }}"
              id="adminNotifBadge"
              data-count="{{ $adminUnreadCount }}">{{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}</span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 admin-notif-dropdown"
         aria-labelledby="page-header-notifications-dropdown">
        <div class="admin-notif-header">
            <span class="fw-semibold">Notifications</span>
            <button type="button" class="btn btn-sm btn-link p-0" id="adminNotifMarkAll">Mark all as read</button>
        </div>
        <div class="admin-notif-list" id="adminNotifList">
            @forelse ($adminNotifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="admin-notif-item {{ $notification->read_at ? '' : 'unread' }}"
                     data-id="{{ $notification->id }}"
                     role="button"
                     tabindex="0">
                    <div class="fw-medium">Order #{{ $data['order_id'] ?? '—' }}</div>
                    <div class="text-muted fs-12">{{ $data['message'] ?? '' }}</div>
                    <div class="text-muted fs-11">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="admin-notif-empty" id="adminNotifEmpty">No notifications yet</div>
            @endforelse
        </div>
    </div>
</div>
@endcan
```

- [ ] **Step 4: Wire assets in `admin-master.blade.php`**

In `<head>` next to order-alert CSS:

```blade
@auth
    @can('orders-show')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-order-alert.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-notifications.css') }}">
    <meta name="admin-user-id" content="{{ auth()->id() }}">
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <meta name="notify-read-all-url" content="{{ route('notify.readAll') }}">
    <meta name="notify-read-url-template" content="{{ url('/notifications/__ID__/read') }}">
    @endcan
@endauth
```

Before closing `</body>`, inside the existing `@can('orders-show')` scripts block (keep order-alert), add:

```blade
<audio id="adminNotifSound" preload="auto" src="{{ asset('sounds/notification.mp3') }}"></audio>
<div class="admin-notif-toast-wrap" id="adminNotifToastWrap" aria-live="polite"></div>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="{{ asset('js/admin-notifications.js') }}" defer></script>
```

Adjust audio `src` extension if the asset is `.wav`.

- [ ] **Step 5: Create `public/js/admin-notifications.js`**

Full file:

```javascript
(function () {
    'use strict';

    const userIdMeta = document.querySelector('meta[name="admin-user-id"]');
    const keyMeta = document.querySelector('meta[name="pusher-key"]');
    const clusterMeta = document.querySelector('meta[name="pusher-cluster"]');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    if (!userIdMeta || !keyMeta || !keyMeta.content || !csrfMeta) {
        return;
    }

    const userId = userIdMeta.content;
    const readAllUrl = document.querySelector('meta[name="notify-read-all-url"]')?.content;
    const readUrlTemplate = document.querySelector('meta[name="notify-read-url-template"]')?.content;

    const listEl = document.getElementById('adminNotifList');
    const badgeEl = document.getElementById('adminNotifBadge');
    const markAllBtn = document.getElementById('adminNotifMarkAll');
    const toastWrap = document.getElementById('adminNotifToastWrap');
    const audioEl = document.getElementById('adminNotifSound');

    let soundEnabled = false;

    document.addEventListener('click', function () {
        soundEnabled = true;
    }, { once: true });

    function playSound() {
        if (!soundEnabled || !audioEl) {
            return;
        }
        try {
            audioEl.currentTime = 0;
            const p = audioEl.play();
            if (p && typeof p.catch === 'function') {
                p.catch(function () {});
            }
        } catch (e) {}
    }

    function setBadge(count) {
        if (!badgeEl) {
            return;
        }
        const n = Math.max(0, count);
        badgeEl.dataset.count = String(n);
        badgeEl.textContent = n > 99 ? '99+' : String(n);
        badgeEl.classList.toggle('is-hidden', n === 0);
    }

    function getBadgeCount() {
        return parseInt(badgeEl?.dataset.count || '0', 10) || 0;
    }

    function showToast(message) {
        if (!toastWrap) {
            return;
        }
        const el = document.createElement('div');
        el.className = 'admin-notif-toast';
        el.textContent = message;
        toastWrap.appendChild(el);
        setTimeout(function () {
            el.remove();
        }, 4500);
    }

    function removeEmptyState() {
        const empty = document.getElementById('adminNotifEmpty');
        if (empty) {
            empty.remove();
        }
    }

    function trimListToTen() {
        if (!listEl) {
            return;
        }
        const items = listEl.querySelectorAll('.admin-notif-item');
        for (let i = 10; i < items.length; i++) {
            items[i].remove();
        }
    }

    function prependNotification(payload) {
        if (!listEl) {
            return;
        }
        removeEmptyState();

        const item = document.createElement('div');
        item.className = 'admin-notif-item unread';
        item.dataset.id = payload.id || '';
        item.setAttribute('role', 'button');
        item.tabIndex = 0;

        const orderId = payload.order_id ?? '—';
        const message = payload.message || 'নতুন অর্ডার এসেছে';
        const when = payload.created_at ? timeAgo(payload.created_at) : 'just now';

        item.innerHTML =
            '<div class="fw-medium">Order #' + escapeHtml(String(orderId)) + '</div>' +
            '<div class="text-muted fs-12">' + escapeHtml(message) + '</div>' +
            '<div class="text-muted fs-11">' + escapeHtml(when) + '</div>';

        item.addEventListener('click', function () {
            markOneRead(item);
        });

        listEl.insertBefore(item, listEl.firstChild);
        trimListToTen();
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function timeAgo(iso) {
        const then = new Date(iso).getTime();
        if (Number.isNaN(then)) {
            return 'just now';
        }
        const sec = Math.max(0, Math.floor((Date.now() - then) / 1000));
        if (sec < 60) return 'just now';
        if (sec < 3600) return Math.floor(sec / 60) + ' minutes ago';
        if (sec < 86400) return Math.floor(sec / 3600) + ' hours ago';
        return Math.floor(sec / 86400) + ' days ago';
    }

    function postJson(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfMeta.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).then(function (r) {
            if (!r.ok) {
                throw new Error('Request failed');
            }
            return r.json();
        });
    }

    function markOneRead(item) {
        const id = item.dataset.id;
        if (!id || !readUrlTemplate || item.classList.contains('is-reading')) {
            return;
        }
        if (!item.classList.contains('unread')) {
            return;
        }
        item.classList.add('is-reading');
        const url = readUrlTemplate.replace('__ID__', encodeURIComponent(id));
        postJson(url)
            .then(function () {
                item.classList.remove('unread', 'is-reading');
                setBadge(getBadgeCount() - 1);
            })
            .catch(function () {
                item.classList.remove('is-reading');
            });
    }

    if (listEl) {
        listEl.querySelectorAll('.admin-notif-item').forEach(function (item) {
            item.addEventListener('click', function () {
                markOneRead(item);
            });
        });
    }

    if (markAllBtn && readAllUrl) {
        markAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            postJson(readAllUrl)
                .then(function () {
                    listEl?.querySelectorAll('.admin-notif-item.unread').forEach(function (el) {
                        el.classList.remove('unread');
                    });
                    setBadge(0);
                })
                .catch(function () {});
        });
    }

    // Prefer .notification() over raw .listen(): Laravel emits BroadcastNotificationCreated
    // with the notification payload and type; .notification() keeps DB + broadcast in sync
    // without inventing a custom event class name mismatch.
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: keyMeta.content,
        cluster: clusterMeta?.content || 'mt1',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfMeta.content,
            },
        },
    });

    window.Echo.private('App.Models.User.' + userId)
        .notification(function (notification) {
            if (notification.type && notification.type !== 'new-order-notification') {
                return;
            }
            prependNotification({
                id: notification.id,
                order_id: notification.order_id,
                message: notification.message,
                created_at: notification.created_at,
            });
            setBadge(getBadgeCount() + 1);
            showToast(notification.message || 'নতুন অর্ডার এসেছে');
            playSound();
        });
})();
```

Note on `notification.id`: Laravel’s broadcast notification payload may include the database notification UUID as `id` when using the database channel together with broadcast. If `id` is missing on the wire, mark-as-read for that live row can no-op until refresh — acceptable; prefer verifying in manual test. If missing, after receive you can omit click-to-read until reload, or fetch latest unread via a small optional endpoint (YAGNI — skip unless needed).

- [ ] **Step 6: Visual/load smoke**

Run admin dashboard logged in as `orders-show` user; confirm bell renders, badge hidden when 0, no JS console errors if Pusher key empty (Echo may warn — keys must be set for live test).

- [ ] **Step 7: Commit** (only if user requested)

```bash
git add public/assets/css/admin-notifications.css public/js/admin-notifications.js public/sounds resources/views/components/header.blade.php resources/views/components/admin-master.blade.php
git commit -m "$(cat <<'EOF'
Add admin notification bell, Echo client, and toast UI.

EOF
)"
```

---

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
4. Click a notification → unread style clears, badge decrements (network `POST .../read` 200).
5. Mark all as read → badge 0.
6. Complete SSLCommerz sandbox payment → exactly one notification after first paid transition.
7. Confirm old polling order alert still works independently.

- [ ] **Step 5: Document production queue reminder** in PR/notes (no new markdown file unless asked):

- Production: `QUEUE_CONNECTION=database` or `redis` + `php artisan queue:work`
- Pusher free plan: ~200k messages/day, ~100 concurrent connections

---

## Spec coverage self-check

| Spec requirement | Task |
|------------------|------|
| Pusher + Echo CDN + `.env` keys | 1, 5 |
| `QUEUE_CONNECTION=sync` local / worker note | 1, 6 |
| `web`+`auth` broadcasting auth | 1 |
| Channel `App.Models.User.{id}` + `orders-show` | 1, 2 |
| `NewOrderNotification` DB+broadcast, Bangla message, `broadcastType` | 2 |
| `Notification::send` to all permission holders | 2 |
| COD + paid triggers, no double IPN | 3 |
| Mark read / read-all endpoints | 4 |
| Bell, badge, latest 10, toast, sound unlock | 5 |
| Keep polling alert | 5 (no edits to alert JS) |
| Free plan comment | 1 |
| Manual E2E | 6 |

## Placeholder / consistency scan

- No TBD left; Order factory gap handled with explicit `makeOrder()` instruction.
- Helper name consistent: `NotifyAdminsOfNewOrder::send`.
- Route names: `notify.read`, `notify.readAll`.
- Event type string: `new-order-notification` everywhere.
- Clarified: use `ShouldQueue` + `broadcast` channel; do not also implement `ShouldBroadcast` on the Notification class (avoids double-broadcast confusion vs original wording).
