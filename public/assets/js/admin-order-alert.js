(function () {
    'use strict';

    const meta = document.querySelector('meta[name="orders-latest-url"]');
    if (!meta || !meta.content) {
        return;
    }

    const STORAGE_ENABLED = 'dd_order_sound_enabled';
    const STORAGE_VOLUME = 'dd_order_sound_volume';
    const PING_KEY = 'dd_order_alert_ping';
    const POLL_MS = 3000;

    let lastKnownId = 0;
    let audioCtx = null;
    let panelOpen = false;

    function getEnabled() {
        const v = localStorage.getItem(STORAGE_ENABLED);
        return v === null ? true : v === '1';
    }

    function setEnabled(on) {
        localStorage.setItem(STORAGE_ENABLED, on ? '1' : '0');
        syncUi();
    }

    function getVolume() {
        const v = parseFloat(localStorage.getItem(STORAGE_VOLUME));
        if (Number.isNaN(v)) {
            return 1;
        }
        return Math.min(1, Math.max(0.2, v));
    }

    function setVolume(val) {
        const v = Math.min(1, Math.max(0.2, parseFloat(val) || 1));
        localStorage.setItem(STORAGE_VOLUME, String(v));
        syncUi();
        return v;
    }

    function ensureAudioContext() {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) {
            return null;
        }
        if (!audioCtx) {
            audioCtx = new Ctx();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function playNewOrderAlert(force) {
        if (!force && !getEnabled()) {
            return;
        }

        const ctx = ensureAudioContext();
        if (!ctx) {
            return;
        }

        const volume = getVolume();
        const now = ctx.currentTime;

        const master = ctx.createGain();
        master.gain.setValueAtTime(volume, now);
        master.connect(ctx.destination);

        const tones = [
            { freq: 880, start: 0, dur: 0.28, peak: 0.55 },
            { freq: 1174, start: 0.22, dur: 0.32, peak: 0.6 },
            { freq: 988, start: 0.48, dur: 0.38, peak: 0.65 },
        ];

        tones.forEach(function (t) {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'square';
            osc.frequency.setValueAtTime(t.freq, now + t.start);
            gain.gain.setValueAtTime(0.0001, now + t.start);
            gain.gain.exponentialRampToValueAtTime(t.peak * volume, now + t.start + 0.03);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + t.start + t.dur);
            osc.connect(gain);
            gain.connect(master);
            osc.start(now + t.start);
            osc.stop(now + t.start + t.dur + 0.05);
        });

        if (!force && document.hidden) {
            setTimeout(function () {
                playNewOrderAlert(false);
            }, 700);
        }
    }

    function showToast(diff) {
        const el = document.getElementById('ddOrderAlertToast');
        const countEl = document.getElementById('ddOrderAlertCount');
        if (!el) {
            return;
        }
        if (countEl) {
            countEl.textContent =
                diff + ' new order' + (diff > 1 ? 's' : '') + ' just arrived.';
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            bootstrap.Toast.getOrCreateInstance(el, {
                autohide: true,
                delay: 7000,
            }).show();
        }
    }

    function fireNewOrderAlert(diff, newId) {
        if (newId <= lastKnownId) {
            return;
        }
        lastKnownId = newId;

        playNewOrderAlert(false);
        showToast(diff);

        window.dispatchEvent(
            new CustomEvent('dd:new-order', {
                detail: { diff: diff, latestId: newId },
            })
        );

        if (typeof toastr !== 'undefined') {
            toastr.success(
                diff + ' new order' + (diff > 1 ? 's' : '') + ' received!',
                'New Order',
                { timeOut: 5000 }
            );
        }
    }

    function broadcastToOtherTabs(diff, newId) {
        try {
            localStorage.setItem(
                PING_KEY,
                JSON.stringify({ id: newId, diff: diff, t: Date.now() })
            );
        } catch (e) {
            /* ignore */
        }
    }

    function pollLatest() {
        fetch(meta.content, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (res) {
                const newId = res.latest_id || 0;
                if (lastKnownId > 0 && newId > lastKnownId) {
                    const diff = newId - lastKnownId;
                    fireNewOrderAlert(diff, newId);
                    broadcastToOtherTabs(diff, newId);
                } else {
                    lastKnownId = Math.max(lastKnownId, newId);
                }
            })
            .catch(function () {
                /* silent */
            });
    }

    function syncUi() {
        const enabled = getEnabled();
        const vol = getVolume();
        const gearBtn = document.getElementById('ddOrderAlertGearBtn');
        const gearIcon = document.getElementById('ddOrderAlertGearIcon');
        const toggleBtn = document.getElementById('ddOrderAlertToggleBtn');
        const volumeInput = document.getElementById('ddOrderAlertVolume');
        const volumeVal = document.getElementById('ddOrderAlertVolumeVal');

        if (volumeInput) {
            volumeInput.value = String(Math.round(vol * 100));
        }
        if (volumeVal) {
            volumeVal.textContent = Math.round(vol * 100) + '%';
        }
        if (gearBtn) {
            gearBtn.classList.toggle('is-muted', !enabled);
            gearBtn.title = enabled
                ? 'Order alert sound on — click to adjust'
                : 'Order alert sound off — click to adjust';
        }
        if (gearIcon) {
            gearIcon.className = enabled ? 'ri-volume-up-line' : 'ri-volume-mute-line';
        }
        if (toggleBtn) {
            toggleBtn.textContent = enabled ? 'Mute alerts' : 'Unmute alerts';
            toggleBtn.classList.toggle('btn-danger', !enabled);
            toggleBtn.classList.toggle('btn-success', enabled);
        }
    }

    function bindUi() {
        const gearBtn = document.getElementById('ddOrderAlertGearBtn');
        const panel = document.getElementById('ddOrderAlertPanel');
        const toggleBtn = document.getElementById('ddOrderAlertToggleBtn');
        const testBtn = document.getElementById('ddOrderAlertTestBtn');
        const volumeInput = document.getElementById('ddOrderAlertVolume');

        if (gearBtn && panel) {
            gearBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                panelOpen = !panelOpen;
                panel.classList.toggle('is-open', panelOpen);
            });
        }

        document.addEventListener('click', function (e) {
            if (!panelOpen || !panel) {
                return;
            }
            if (panel.contains(e.target) || (gearBtn && gearBtn.contains(e.target))) {
                return;
            }
            panelOpen = false;
            panel.classList.remove('is-open');
        });

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                setEnabled(!getEnabled());
            });
        }

        if (testBtn) {
            testBtn.addEventListener('click', function () {
                ensureAudioContext();
                playNewOrderAlert(true);
            });
        }

        if (volumeInput) {
            volumeInput.addEventListener('input', function () {
                setVolume(parseInt(volumeInput.value, 10) / 100);
            });
        }

        ['click', 'keydown', 'touchstart'].forEach(function (evt) {
            document.addEventListener(
                evt,
                function () {
                    ensureAudioContext();
                },
                { once: true, passive: true }
            );
        });

        syncUi();
    }

    function init() {
        bindUi();

        fetch(meta.content, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (res) {
                lastKnownId = res.latest_id || 0;
            })
            .finally(function () {
                setInterval(pollLatest, POLL_MS);
            });

        window.addEventListener('storage', function (e) {
            if (e.key !== PING_KEY || !e.newValue) {
                return;
            }
            try {
                const data = JSON.parse(e.newValue);
                if (data.id > lastKnownId) {
                    fireNewOrderAlert(data.diff || 1, data.id);
                }
            } catch (err) {
                /* ignore */
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
