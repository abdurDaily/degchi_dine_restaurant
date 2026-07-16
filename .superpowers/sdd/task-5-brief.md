### Task 5: Admin UI â€” bell, CSS, audio, Echo JS

**Files:**
- Create: `public/assets/css/admin-notifications.css`
- Create: `public/js/admin-notifications.js`
- Create: `public/sounds/.gitkeep` and `public/sounds/notification.mp3` (or `.wav` if mp3 unavailable â€” update `src` accordingly)
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

Simpler approach â€” write this PHP one-off once and save as `public/sounds/notification.wav`, then point the Blade `<audio>` at it (browsers accept wav). Or download a short royalty-free mp3 into `public/sounds/notification.mp3`. **Do not leave a broken empty file.**

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
                    <div class="fw-medium">Order #{{ $data['order_id'] ?? 'â€”' }}</div>
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

        const orderId = payload.order_id ?? 'â€”';
        const message = payload.message || 'à¦¨à¦¤à§à¦¨ à¦…à¦°à§à¦¡à¦¾à¦° à¦à¦¸à§‡à¦›à§‡';
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
            showToast(notification.message || 'à¦¨à¦¤à§à¦¨ à¦…à¦°à§à¦¡à¦¾à¦° à¦à¦¸à§‡à¦›à§‡');
            playSound();
        });
})();
```

Note on `notification.id`: Laravelâ€™s broadcast notification payload may include the database notification UUID as `id` when using the database channel together with broadcast. If `id` is missing on the wire, mark-as-read for that live row can no-op until refresh â€” acceptable; prefer verifying in manual test. If missing, after receive you can omit click-to-read until reload, or fetch latest unread via a small optional endpoint (YAGNI â€” skip unless needed).

- [ ] **Step 6: Visual/load smoke**

Run admin dashboard logged in as `orders-show` user; confirm bell renders, badge hidden when 0, no JS console errors if Pusher key empty (Echo may warn â€” keys must be set for live test).

- [ ] **Step 7: Commit** (only if user requested)

```bash
git add public/assets/css/admin-notifications.css public/js/admin-notifications.js public/sounds resources/views/components/header.blade.php resources/views/components/admin-master.blade.php
git commit -m "$(cat <<'EOF'
Add admin notification bell, Echo client, and toast UI.

EOF
)"
```

---


