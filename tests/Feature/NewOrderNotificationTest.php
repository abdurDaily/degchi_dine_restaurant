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

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'customer_phone' => '01700000000',
            'customer_address' => 'Test Address',
            'payment_method' => 'cod',
            'status' => 'pending',
            'total_amount' => 100.00,
            'final_amount' => 100.00,
        ]);

        Notification::fake();

        NotifyAdminsOfNewOrder::send($order);

        Notification::assertSentTo($admin, NewOrderNotification::class);
        Notification::assertNotSentTo($other, NewOrderNotification::class);
    }

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

    private function makeOrder(): Order
    {
        return Order::create([
            'customer_name' => 'Test Customer',
            'customer_phone' => '01700000000',
            'customer_address' => 'Test Address',
            'payment_method' => 'cod',
            'status' => 'pending',
            'total_amount' => 100.00,
            'final_amount' => 100.00,
        ]);
    }
}
