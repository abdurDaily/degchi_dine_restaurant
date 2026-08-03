/**
 * Backend "Order Details" item editor — quantity stepper + "Add Extra Item"
 * picker. Included on both the standalone order page (show.blade.php) and
 * the orders list modal (index.blade.php); every handler is delegated on
 * `document` so it keeps working after the items table / summary card is
 * replaced with fresh server-rendered HTML, or after the whole details
 * partial is (re)loaded into the modal via AJAX.
 *
 * The server is always the source of truth for pricing/discount math — this
 * file never computes totals itself, it only swaps in the HTML the backend
 * returns after each change.
 */
(function ($) {
    'use strict';

    if (!$) {
        return;
    }

    // Guard against this file's handlers ever being bound twice (e.g. if the
    // script tag were ever accidentally included more than once on a page).
    if (window.__DEGCHI_ORDER_ITEMS_JS__) {
        return;
    }
    window.__DEGCHI_ORDER_ITEMS_JS__ = true;

    function notify(type, message) {
        if (!message) return;
        if (typeof toastr !== 'undefined') {
            toastr[type === 'error' ? 'error' : 'success'](message);
        } else if (type === 'error') {
            alert(message);
        }
    }

    function money(value) {
        return '৳' + (Number(value) || 0).toFixed(2);
    }

    function refreshOrdersTableIfPresent() {
        if ($.fn.dataTable && $('.yajra-datatable').length && $.fn.dataTable.isDataTable('.yajra-datatable')) {
            $('.yajra-datatable').DataTable().draw(false);
        }
        if (typeof window.refreshCounts === 'function') {
            window.refreshCounts();
        }
    }

    // Flash the summary card briefly so a total/price change is impossible to miss.
    function flashSummaryUpdated() {
        const el = $('#orderSummaryWrap');
        el.addClass('order-summary-flash');
        setTimeout(() => el.removeClass('order-summary-flash'), 900);
    }

    // Turn a failed AJAX response into a specific, human-readable message
    // instead of a generic "something went wrong" — makes real failures
    // (expired session, missing permission, validation) obvious & debuggable.
    function describeAjaxError(xhr, fallback) {
        if (xhr.status === 401) {
            return 'Your session has expired. Please refresh the page and log in again.';
        }
        if (xhr.status === 419) {
            return 'Your session has expired (page token invalid). Please refresh the page and try again.';
        }
        if (xhr.status === 403) {
            return (xhr.responseJSON && xhr.responseJSON.message) || 'You don\'t have permission to edit this order.';
        }
        if (xhr.status === 422) {
            const errors = xhr.responseJSON && xhr.responseJSON.errors;
            if (errors) {
                const firstError = Object.values(errors)[0];
                return Array.isArray(firstError) ? firstError[0] : fallback;
            }
            return (xhr.responseJSON && xhr.responseJSON.message) || fallback;
        }
        if (xhr.status === 0) {
            return 'Could not reach the server. Check your connection and try again.';
        }
        if (xhr.status >= 500) {
            return 'Server error while saving. Please try again — if it persists, check the Laravel log.';
        }
        return (xhr.responseJSON && xhr.responseJSON.message) || fallback;
    }

    /* ══════════════════════════════════════════════════════════════
       1. QUANTITY STEPPER  (+ / − / remove)
    ══════════════════════════════════════════════════════════════ */
    function setItemsBusy(wrap, busy) {
        wrap.css({ opacity: busy ? 0.55 : '', pointerEvents: busy ? 'none' : '' });
    }

    function submitQuantityChange(wrap, index, quantity) {
        const url = wrap.data('quantity-url');
        if (!url) {
            console.warn('[order-items] Missing data-quantity-url on #orderItemsTableWrap — cannot update quantity.');
            notify('error', 'Something is misconfigured on this page (missing update URL). Please reload.');
            return;
        }

        setItemsBusy(wrap, true);

        $.ajax({
            url: url,
            type: 'POST',
            data: { index: index, quantity: quantity },
            success: function (res) {
                // Swapping innerHTML does NOT reset the wrap element's own
                // inline style, so the busy state (opacity + pointer-events:
                // none) must always be cleared explicitly here — otherwise
                // the whole table silently stops responding to clicks after
                // the very first successful update.
                setItemsBusy(wrap, false);

                if (res.success) {
                    wrap.html(res.items_html);
                    $('#orderSummaryWrap').html(res.summary_html);
                    refreshOrdersTableIfPresent();
                    flashSummaryUpdated();
                    notify('success', quantity <= 0 ? 'Item removed from the order.' : 'Quantity updated.');
                } else {
                    notify('error', res.message || 'Unable to update this item.');
                }
            },
            error: function (xhr) {
                console.error('[order-items] quantity update failed', xhr.status, xhr.responseText);
                notify('error', describeAjaxError(xhr, 'Unable to update this item.'));
                setItemsBusy(wrap, false);
            },
        });
    }

    $(document).on('click', '.order-qty-plus, .order-qty-minus, .order-item-remove', function () {
        const btn = $(this);
        const stepper = btn.closest('.order-qty-stepper');
        const wrap = btn.closest('#orderItemsTableWrap');
        const index = stepper.data('item-index');
        const currentQty = parseInt(stepper.find('.order-qty-value').text(), 10) || 1;

        if (btn.hasClass('order-item-remove')) {
            if (!confirm('Remove this item from the order?')) return;
            submitQuantityChange(wrap, index, 0);
            return;
        }

        let nextQty = btn.hasClass('order-qty-plus') ? currentQty + 1 : currentQty - 1;

        if (nextQty <= 0) {
            if (!confirm('Quantity would drop to 0 — remove this item from the order?')) return;
            nextQty = 0;
        }

        submitQuantityChange(wrap, index, nextQty);
    });

    /* ══════════════════════════════════════════════════════════════
       2. "ADD EXTRA ITEM" MODAL
    ══════════════════════════════════════════════════════════════ */
    let menuPickerCache = [];

    // Bootstrap modals stack/backdrop poorly when nested inside another open
    // modal's DOM (this partial can be AJAX-loaded into the orders list
    // modal). Moving it to <body> the first time it's used sidesteps that.
    function ensureModalOnBody() {
        const modal = $('#addExtraItemModal');
        if (modal.length && !modal.parent().is('body')) {
            modal.appendTo('body');
        }
        return modal;
    }

    function renderMenuPicker(menus) {
        const list = $('#menuPickerList');

        if (!menus || !menus.length) {
            list.html('<div class="text-center py-5 text-muted">No menu items found.</div>');
            return;
        }

        let html = '';

        menus.forEach(function (menu) {
            html += '<div class="menu-picker-group">' +
                '<div class="menu-picker-group-title">' +
                    '<span class="text-muted">' + escapeHtml(menu.category) + ' &rsaquo;</span> ' + escapeHtml(menu.name) +
                '</div>' +
                '<div class="menu-picker-variations">';

            menu.variations.forEach(function (variation) {
                const hasOffer = variation.offer_percent > 0;
                const priceHtml = hasOffer
                    ? '<span class="text-decoration-line-through text-muted me-1">' + money(variation.price) + '</span>' +
                      '<span class="fw-bold text-danger">' + money(variation.discounted_price) + '</span>' +
                      '<span class="badge bg-danger-subtle text-danger ms-1">' + variation.offer_percent + '% OFF</span>'
                    : '<span class="fw-bold">' + money(variation.price) + '</span>';

                const thumb = variation.image_url
                    ? '<img src="' + variation.image_url + '" alt="' + escapeHtml(variation.name) + '">'
                    : '<i class="ri-restaurant-line"></i>';

                html += '<div class="menu-picker-item" data-variation-id="' + variation.variation_id + '">' +
                    '<div class="menu-picker-item-thumb">' + thumb + '</div>' +
                    '<div class="menu-picker-item-info">' +
                        '<div class="menu-picker-item-name">' + escapeHtml(variation.name) + '</div>' +
                        '<div class="menu-picker-item-price">' + priceHtml + '</div>' +
                    '</div>' +
                    '<div class="menu-picker-item-actions">' +
                        '<input type="number" class="form-control form-control-sm menu-picker-qty" value="1" min="1" max="50">' +
                        '<button type="button" class="btn btn-sm btn-add-to-order menu-picker-add-btn">' +
                            '<i class="ri-add-line me-1"></i><span class="add-label">Add</span>' +
                        '</button>' +
                    '</div>' +
                '</div>';
            });

            html += '</div></div>';
        });

        list.html(html);
    }

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : text).html();
    }

    function filterMenuPicker(term) {
        term = (term || '').trim().toLowerCase();

        if (!term) {
            renderMenuPicker(menuPickerCache);
            return;
        }

        const filtered = menuPickerCache
            .map(function (menu) {
                const nameMatches = menu.name.toLowerCase().indexOf(term) !== -1;
                const variations = nameMatches
                    ? menu.variations
                    : menu.variations.filter(function (v) {
                        return v.name.toLowerCase().indexOf(term) !== -1;
                    });

                return variations.length ? Object.assign({}, menu, { variations: variations }) : null;
            })
            .filter(function (menu) { return menu !== null; });

        renderMenuPicker(filtered);
    }

    $(document).on('click', '#addExtraItemBtn', function () {
        const btn = $(this);
        const pickerUrl = btn.data('menu-picker-url');

        if (!pickerUrl) {
            console.warn('[order-items] Missing data-menu-picker-url on #addExtraItemBtn.');
            notify('error', 'Something is misconfigured on this page (missing menu URL). Please reload.');
            return;
        }

        const modal = ensureModalOnBody();
        $('#menuPickerSearch').val('');
        $('#menuPickerList').html(
            '<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading menu…</div>'
        );
        modal.modal('show');

        $.ajax({
            url: pickerUrl,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    menuPickerCache = res.menus || [];
                    renderMenuPicker(menuPickerCache);
                } else {
                    $('#menuPickerList').html('<div class="alert alert-danger m-3">' + (res.message || 'Failed to load the menu.') + '</div>');
                }
            },
            error: function (xhr) {
                console.error('[order-items] menu picker load failed', xhr.status, xhr.responseText);
                $('#menuPickerList').html('<div class="alert alert-danger m-3">' + describeAjaxError(xhr, 'Failed to load the menu.') + '</div>');
            },
        });
    });

    let searchDebounceTimer = null;
    $(document).on('input', '#menuPickerSearch', function () {
        const term = $(this).val();
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => filterMenuPicker(term), 150);
    });

    $(document).on('click', '.menu-picker-add-btn', function () {
        const btn = $(this);
        const row = btn.closest('.menu-picker-item');
        const variationId = row.data('variation-id');
        const quantity = parseInt(row.find('.menu-picker-qty').val(), 10) || 1;
        const addUrl = $('#addExtraItemBtn').data('add-item-url');

        if (!addUrl) {
            console.warn('[order-items] Missing data-add-item-url on #addExtraItemBtn.');
            notify('error', 'Something is misconfigured on this page (missing add-item URL). Please reload.');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: addUrl,
            type: 'POST',
            data: { variation_id: variationId, quantity: quantity },
            success: function (res) {
                if (res.success) {
                    $('#orderItemsTableWrap').html(res.items_html);
                    $('#orderSummaryWrap').html(res.summary_html);
                    refreshOrdersTableIfPresent();
                    flashSummaryUpdated();
                    notify('success', 'Item added to the order.');

                    btn.removeClass('btn-add-to-order').addClass('btn-success')
                        .html('<i class="ri-check-line"></i><span class="add-label"> Added</span>');
                    setTimeout(function () {
                        btn.prop('disabled', false).removeClass('btn-success').addClass('btn-add-to-order')
                            .html('<i class="ri-add-line me-1"></i><span class="add-label">Add</span>');
                    }, 1200);
                } else {
                    notify('error', res.message || 'Unable to add this item.');
                    btn.prop('disabled', false).html('<i class="ri-add-line me-1"></i><span class="add-label">Add</span>');
                }
            },
            error: function (xhr) {
                console.error('[order-items] add item failed', xhr.status, xhr.responseText);
                notify('error', describeAjaxError(xhr, 'Unable to add this item.'));
                btn.prop('disabled', false).html('<i class="ri-add-line me-1"></i><span class="add-label">Add</span>');
            },
        });
    });

    console.log('[order-items] backend-order-items.js loaded and handlers bound.');
})(window.jQuery);
