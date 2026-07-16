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

Note: `BroadcastServiceProvider` already `require`s `channels.php`. Loading via both `channels:` and the provider is Laravelâ€™s common pattern; if double-registration of channel callbacks becomes an issue, keep the providerâ€™s `Broadcast::routes` + `require` and still pass `channels:` for framework discovery â€” or load channels only once. Prefer: provider handles `Broadcast::routes` + `require channels.php`; `channels:` in `withRouting` is still recommended by Laravel 11 docs. If channels register twice in testing, remove the `require` from the provider and rely on `channels:` only, keeping `Broadcast::routes` in the provider.

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


