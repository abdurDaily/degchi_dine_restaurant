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

- [ ] **Step 2: Run test â€” expect fail**

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
            'message' => 'à¦¨à¦¤à§à¦¨ à¦…à¦°à§à¦¡à¦¾à¦° à¦à¦¸à§‡à¦›à§‡',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
```

Note: Laravel notification broadcasting uses `broadcastType()` (not a separate Event `broadcastAs`). Echo `.notification()` receives `notification.type === 'new-order-notification'`. Do **not** also implement `ShouldBroadcast` on the Notification class â€” the `broadcast` channel already emits `BroadcastNotificationCreated`; implementing both is redundant and can confuse the queue.

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

- [ ] **Step 6: Run test â€” expect pass**

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


