# Task 1 Review: Broadcasting Infrastructure (Re-review)

**Reviewer:** task-scoped gate  
**Base:** d85178f7a8e74eddb8f9f6b7369c4798f1aa88d9  
**Head:** WORKING_TREE (post-critical fix)

## Verdict

- **Spec compliance:** ✅ — All global constraints and brief requirements satisfied.
- **Quality:** Approved

## Findings

### Critical (resolved)
- **`auth` middleware on `/broadcasting/auth`.** Fix verified: `bootstrap/app.php` uses `->withBroadcasting(..., ['middleware' => ['web', 'auth']])`; duplicate `Broadcast::routes()` removed from provider. `php artisan route:list --path=broadcasting -v` shows single route with `web` + `auth`.

### Minor
- **`BroadcastServiceProvider` is an empty shell.** Acceptable — provider remains registered per brief; all broadcasting setup now lives in `withBroadcasting()`. Could be removed in a future cleanup without functional impact.

## Verified OK

- `pusher/pusher-php-server` ^7.2 in composer; `config/broadcasting.php` matches brief (env creds, free-plan comment).
- Channel `App.Models.User.{id}` + `orders-show` auth in `routes/channels.php`; permission exists in codebase.
- No double channel/route registration; admin `then:` group preserved.
- `.env.example`: `BROADCAST_CONNECTION=pusher`, `QUEUE_CONNECTION=sync` + comments, Pusher block; no commit.
- Smoke: `config:clear` OK; `broadcasting/auth` route exists with correct middleware.
