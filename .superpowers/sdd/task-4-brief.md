### Task 4: Mark-as-read API

**Files:**
- Modify: `app/Http/Controllers/NotificationController.php`
- Modify: `routes/web.php` (notifications group ~103â€“106)
- Modify: `tests/Feature/NewOrderNotificationTest.php`

**Interfaces:**
- Consumes: `auth()->user()->notifications()`
- Produces:
  - `POST /notifications/{id}/read` â†’ `notify.read` â†’ JSON `{ success: true }`
  - `POST /notifications/read-all` â†’ `notify.readAll` â†’ JSON `{ success: true }`

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

- [ ] **Step 2: Run â€” expect fail (routes/methods missing)**

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

- [ ] **Step 5: Run tests â€” expect pass**

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


