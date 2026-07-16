# Task 1 Report: Broadcasting Infrastructure

**Status:** DONE  
**Branch:** feature/admin-order-notifications  
**Date:** 2026-07-16

## Summary

Implemented Pusher broadcasting infrastructure for admin real-time order notifications. All files created/modified per task brief, with global constraint overrides applied (no double channel registration, no commit).

## Changes Made

### Step 1: Install Pusher PHP SDK

```bash
composer require pusher/pusher-php-server
```

- Added `pusher/pusher-php-server: ^7.2` (resolved to 7.2.8) to `composer.json` and `composer.lock`
- Composer completed without errors

### Step 2: Create `config/broadcasting.php`

- Created Laravel 11-style broadcasting config
- Default connection: `env('BROADCAST_CONNECTION', 'null')`
- Pusher connection includes free-plan limit comment: `200k messages/day, 100 concurrent connections`
- Includes `ably`, `log`, and `null` drivers per brief

### Step 3: Create `routes/channels.php`

- Channel name exactly: `App.Models.User.{id}`
- Authorization: `(int) $user->id === (int) $id && $user->can('orders-show')`

### Step 4: Create `app/Providers/BroadcastServiceProvider.php`

- Registers broadcast auth routes with `web` + `auth` middleware
- **Override applied:** No `require base_path('routes/channels.php')` — channels loaded via `bootstrap/app.php` only to avoid double registration

### Step 5: Register provider in `bootstrap/providers.php`

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
];
```

### Step 6: Register channels path in `bootstrap/app.php`

Added `channels: __DIR__ . '/../routes/channels.php'` to `withRouting()`. Existing admin route group (`then:`) preserved unchanged.

### Step 7: Update `.env.example`

- `BROADCAST_CONNECTION=pusher`
- `QUEUE_CONNECTION=sync` with local/dev and production worker comments
- Pusher credentials block: `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER=ap2`
- Other `.env.example` keys left intact

### Step 8: Verification

```bash
php artisan config:clear
# INFO  Configuration cache cleared successfully.

php artisan route:list --path=broadcasting
# GET|POST|HEAD  broadcasting/auth  ...  BroadcastController@authenticate
# Showing [1] routes
```

Config loads cleanly; `/broadcasting/auth` route registered under `web`+`auth` middleware via `BroadcastServiceProvider`.

### Step 9: Commit

**Skipped** per global constraint override (Do NOT commit).

## Self-Review

| Requirement | Status |
|---|---|
| Channel name `App.Models.User.{id}` | ✓ |
| Auth: same user id + `orders-show` permission | ✓ |
| `bootstrap/app.php` passes `channels:` path | ✓ |
| `BroadcastServiceProvider` only `Broadcast::routes` (no require) | ✓ |
| Pusher free-plan comment in config | ✓ |
| `QUEUE_CONNECTION=sync` + production comment in `.env.example` | ✓ |
| No commit | ✓ |
| `POST broadcasting/auth` route listed | ✓ (GET\|POST\|HEAD) |

## Files Touched

| File | Action |
|---|---|
| `composer.json` | Modified (pusher dependency) |
| `composer.lock` | Modified |
| `config/broadcasting.php` | Created |
| `routes/channels.php` | Created |
| `app/Providers/BroadcastServiceProvider.php` | Created |
| `bootstrap/providers.php` | Modified |
| `bootstrap/app.php` | Modified |
| `.env.example` | Modified |

## Concerns

None. Infrastructure is ready for Task 2 (notification classes and broadcasting events).

## Next Steps (out of scope for Task 1)

- Task 2+: `NewOrderNotification`, `NotifyAdminsOfNewOrder`, Echo/JS UI in admin layout
- Developer must copy Pusher credentials into local `.env` before live broadcast testing

---

## Task 1 Review Fix (Critical)

**Status:** FIXED  
**Date:** 2026-07-16

### Problem

`channels:` in `withRouting()` triggered `withBroadcasting($channels)` without middleware attributes → `Broadcast::routes(null)` → `web` only. `BroadcastServiceProvider` also called `Broadcast::routes(['middleware' => ['web', 'auth']])`, causing double registration; `auth` was not reliably applied.

### Changes

1. **`bootstrap/app.php`:** Removed `channels:` from `withRouting()`; added `->withBroadcasting(__DIR__ . '/../routes/channels.php', ['middleware' => ['web', 'auth']])` on the configure chain.
2. **`app/Providers/BroadcastServiceProvider.php`:** Removed `Broadcast::routes()` from `boot()` (empty boot; provider remains registered in `bootstrap/providers.php`).

### Verification

```bash
php artisan route:list --path=broadcasting -v
```

```
GET|POST|HEAD  broadcasting/auth  ...  BroadcastController@authenticate
               ⇂ web
               ⇂ auth
```

Middleware confirmed: `web`, `auth`. Single route registration via `withBroadcasting()`.
