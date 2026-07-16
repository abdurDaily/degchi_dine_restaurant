(function () {
    'use strict';

    const userIdMeta = document.querySelector('meta[name="admin-user-id"]');
    const keyMeta = document.querySelector('meta[name="pusher-key"]');
    const clusterMeta = document.querySelector('meta[name="pusher-cluster"]');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    const listEl = document.getElementById('adminNotifList');
    const badgeEl = document.getElementById('adminNotifBadge');
    const markAllBtn = document.getElementById('adminNotifMarkAll');
    const toastWrap = document.getElementById('adminNotifToastWrap');
    const audioEl = document.getElementById('adminNotifSound');

    if (!listEl && !badgeEl && !markAllBtn) {
        return;
    }

    const readAllUrl = document.querySelector('meta[name="notify-read-all-url"]')?.content;
    const readUrlTemplate = document.querySelector('meta[name="notify-read-url-template"]')?.content;

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
            const promise = audioEl.play();
            if (promise && typeof promise.catch === 'function') {
                promise.catch(function () {});
            }
        } catch (error) {}
    }

    function setBadge(count) {
        if (!badgeEl) {
            return;
        }

        const normalizedCount = Math.max(0, count);
        badgeEl.dataset.count = String(normalizedCount);
        badgeEl.textContent = normalizedCount > 99 ? '99+' : String(normalizedCount);
        badgeEl.classList.toggle('is-hidden', normalizedCount === 0);
    }

    function getBadgeCount() {
        return parseInt(badgeEl?.dataset.count || '0', 10) || 0;
    }

    function showToast(message) {
        if (!toastWrap) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = 'admin-notif-toast';
        toast.textContent = message;
        toastWrap.appendChild(toast);
        setTimeout(function () {
            toast.remove();
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
        for (let index = 10; index < items.length; index++) {
            items[index].remove();
        }
    }

    function escapeHtml(value) {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function timeAgo(iso) {
        const timestamp = new Date(iso).getTime();
        if (Number.isNaN(timestamp)) {
            return 'just now';
        }

        const seconds = Math.max(0, Math.floor((Date.now() - timestamp) / 1000));
        if (seconds < 60) return 'just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';

        return Math.floor(seconds / 86400) + ' days ago';
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

    function postJson(url) {
        if (!csrfMeta) {
            return Promise.reject(new Error('Missing CSRF token'));
        }

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfMeta.content,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Request failed');
            }

            return response.json();
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
        markAllBtn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            postJson(readAllUrl)
                .then(function () {
                    listEl?.querySelectorAll('.admin-notif-item.unread').forEach(function (item) {
                        item.classList.remove('unread');
                    });
                    setBadge(0);
                })
                .catch(function () {});
        });
    }

    const userId = userIdMeta?.content;
    const pusherKey = keyMeta?.content;
    if (!userId || !pusherKey || !csrfMeta) {
        return;
    }

    const EchoConstructor = typeof window.Echo === 'function'
        ? window.Echo
        : window.Echo?.default;

    if (!window.Pusher || typeof EchoConstructor !== 'function') {
        return;
    }

    window.Echo = new EchoConstructor({
        broadcaster: 'pusher',
        key: pusherKey,
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
            const type = notification.type || '';
            if (type !== 'new-order-notification' && !type.includes('NewOrderNotification')) {
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
