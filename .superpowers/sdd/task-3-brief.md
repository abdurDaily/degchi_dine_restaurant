### Task 3: Trigger on COD and paid SSLCommerz

**Files:**
- Modify: `app/Http/Controllers/Frontend/HomeController.php` (COD block ~537â€“545)
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


