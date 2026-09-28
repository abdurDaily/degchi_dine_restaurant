/* ==========================================================================
   CART.JS — Cart system extracted from app.js
   Handles: localStorage cart, cart drawer, cart page, checkout summary
   ========================================================================== */

const CART_STORAGE_KEY = "degchi_cart";

const getMemberState = () => {
    return (
        window.DEGCHI_MEMBER || {
            loggedIn: false,
            canUseFirstOrder: false,
            loginUrl: "/member/login",
            registerUrl: "/card-apply",
        }
    );
};

const offerRequiresMemberLogin = (isFirstOrder) => {
    return !!isFirstOrder;
};

const showOfferMemberLoginModal = () => {
    const modalEl = document.getElementById("offerMemberLoginModal");
    if (modalEl && window.bootstrap?.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        return;
    }
    const member = getMemberState();
    window.location.href = member.loginUrl || "/member/login";
};

const getCartData = () => {
    try {
        const raw = localStorage.getItem(CART_STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch {
        return [];
    }
};

const saveCartData = (cart) => {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
};

const formatAmount = (value, decimals = 2) => {
    const amount = Number(value || 0);
    const [whole, fraction] = Math.abs(amount).toFixed(decimals).split(".");
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return `${amount < 0 ? "-" : ""}${grouped}${fraction ? "." + fraction : ""}`;
};

const formatCurrency = (value, decimals = 2) =>
    `\u09F3 ${formatAmount(value, decimals)}`;

const roundMoney = (value) => Math.round((Number(value) || 0) * 100) / 100;

const getItemUnitPrice = (item) => Number(item.price || 0);
const getItemOriginalPrice = (item) =>
    Number(item.original_price ?? item.price ?? 0);

const getCartTotal = (cart) =>
    cart.reduce(
        (t, item) => t + getItemUnitPrice(item) * Number(item.quantity || 0),
        0,
    );

const getCartOriginalTotal = (cart) =>
    cart.reduce(
        (t, item) =>
            t + getItemOriginalPrice(item) * Number(item.quantity || 0),
        0,
    );

const resolveOfferMinTotal = (item) => {
    const map = window.DEGCHI_OFFER_MIN_TOTALS || {};
    const offerId = item?.offer_id;

    // The server snapshot is authoritative whenever this offer appears in it, so
    // an admin edit (minimum raised, lowered or removed) always wins over a
    // value that is still cached on the cart line.
    if (
        offerId != null &&
        Object.prototype.hasOwnProperty.call(map, String(offerId))
    ) {
        const fromMap = parseFloat(map[String(offerId)]);
        return Number.isFinite(fromMap) && fromMap > 0 ? fromMap : 0;
    }

    const stored = parseFloat(item?.offer_min_total);
    return Number.isFinite(stored) && stored > 0 ? stored : 0;
};

/**
 * `min_total` is a whole-CART threshold measured on the undiscounted subtotal.
 * Returns the minimum when the offer is locked because the order is still
 * short of it, otherwise 0 (= unlocked / no minimum configured).
 */
const offerLockedByMinimum = (item, originalSubtotal) => {
    const min = resolveOfferMinTotal(item);
    if (!(min > 0)) return 0;
    if (originalSubtotal + 0.005 >= min) return 0;
    return min;
};

const discountedUnitFromItem = (item) => {
    const original = getItemOriginalPrice(item);
    const offerPrice = parseFloat(item.offer_price);
    if (Number.isFinite(offerPrice) && offerPrice > 0)
        return roundMoney(offerPrice);
    const percent = parseFloat(item.offer_percent) || 0;
    if (percent <= 0) return original;
    return roundMoney(original * (1 - percent / 100));
};

const canApplyItemOffer = (item, originalSubtotal, member) => {
    const percent = parseFloat(item.offer_percent) || 0;
    if (percent <= 0) return false;
    if (
        offerRequiresMemberLogin(item.is_first_order) &&
        !(member?.loggedIn && member.canUseFirstOrder !== false)
    ) {
        return false;
    }
    // The offer's min_total is a cart-level gate, never an item-level one.
    if (offerLockedByMinimum(item, originalSubtotal) > 0) return false;
    return true;
};

const applyCartOffers = (cart) => {
    const originalSubtotal = getCartOriginalTotal(cart);
    const member = getMemberState();
    return cart.map((item) => {
        const original = getItemOriginalPrice(item);
        const apply = canApplyItemOffer(item, originalSubtotal, member);
        return {
            ...item,
            original_price: original,
            offer_min_total:
                resolveOfferMinTotal(item) || item.offer_min_total || null,
            price: apply ? discountedUnitFromItem(item) : original,
            offer_applied: apply,
        };
    });
};

const persistCart = (cart) => {
    const priced = applyCartOffers(cart);
    saveCartData(priced);
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
    return priced;
};

/**
 * Shown instead of the discounted price while the order is still short of the
 * offer's min_total: the item keeps its regular price and the badge explains
 * how much more is needed to unlock the discount.
 */
const renderCartMinOrderBadge = (item, originalSubtotal) => {
    const min = offerLockedByMinimum(item, originalSubtotal);
    const percent = parseFloat(item.offer_percent) || 0;
    if (!(min > 0) || percent <= 0) return "";

    const missing = roundMoney(min - originalSubtotal);
    const title = `${percent}% off unlocks at a ${formatCurrency(min)} order subtotal — add ${formatCurrency(missing)} more`;

    return `<span class="cart-min-order-badge" title="${title}"><i class="bi bi-cart-check" aria-hidden="true"></i> Min ৳${formatAmount(min, 0)} order</span>`;
};

const renderCartOfferSuffix = (item) => {
    if (item.offer_applied && item.offer_percent)
        return ` · ${item.offer_percent}% OFF`;
    return "";
};

const buildCartItemId = (item) => {
    if (item.variation_id) return `variation-${item.variation_id}`;
    return `${item.title}`
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-");
};

const createMenuItemFromCard = (card) => {
    const menuCard = card?.closest?.(".pcard, .menu-offer-card") || card;
    if (
        !menuCard?.classList?.contains("pcard") &&
        !menuCard?.classList?.contains("menu-offer-card")
    )
        return null;

    const cartBtn = menuCard.querySelector(
        ".pcard-cart-btn, .menu-offer-cart-btn",
    );
    if (!cartBtn) return null;

    const title = menuCard
        .querySelector(".pcard-title, .menu-offer-title")
        ?.textContent.trim();
    const image =
        menuCard
            .querySelector(".pcard-img, .menu-offer-image")
            ?.getAttribute("src") || "";
    const quantityText =
        menuCard.querySelector(".pcard-serve, .menu-offer-serve")
            ?.textContent || "1 person";

    const variationId =
        cartBtn.getAttribute("data-variation-id") ||
        cartBtn.dataset.variationId;

    let originalPrice =
        parseFloat(
            cartBtn.getAttribute("data-original-price") ||
                cartBtn.dataset.originalPrice,
        ) || 0;

    if (originalPrice === 0) {
        const allPrices = menuCard.querySelectorAll(
            ".pcard-price, .menu-offer-price",
        );
        if (allPrices.length > 1) {
            const oldPrice = menuCard.querySelector(
                ".pcard-price-old, .menu-offer-price-old",
            );
            const priceText = (oldPrice || allPrices[0]).textContent
                .replace(/,/g, "")
                .replace(/[^\d.]/g, "")
                .trim();
            originalPrice = parseFloat(priceText) || 0;
        } else if (allPrices.length === 1) {
            const priceText = allPrices[0].textContent
                .replace(/,/g, "")
                .replace(/[^\d.]/g, "")
                .trim();
            originalPrice = parseFloat(priceText) || 0;
        }
    }

    const offerPercent =
        parseFloat(
            cartBtn.getAttribute("data-offer-percent") ||
                cartBtn.dataset.offerPercent ||
                "0",
        ) || 0;
    const offerId =
        cartBtn.getAttribute("data-offer-id") ||
        cartBtn.dataset.offerId ||
        null;
    const isFirstOrder =
        (cartBtn.getAttribute("data-is-first-order") ||
            cartBtn.dataset.isFirstOrder ||
            "0") === "1";
    const applicableTo = (
        cartBtn.getAttribute("data-applicable-to") ||
        cartBtn.dataset.applicableTo ||
        "all"
    ).toLowerCase();
    const offerMinTotal =
        parseFloat(
            cartBtn.getAttribute("data-offer-min-total") ||
                cartBtn.dataset.offerMinTotal ||
                "0",
        ) || 0;

    const member = getMemberState();
    if (
        offerRequiresMemberLogin(isFirstOrder, applicableTo) &&
        !member.loggedIn
    ) {
        showOfferMemberLoginModal();
        return null;
    }

    let offerPriceAttr =
        parseFloat(
            cartBtn.getAttribute("data-offer-price") ||
                cartBtn.dataset.offerPrice ||
                "0",
        ) || 0;
    if (offerPercent > 0 && !(offerPriceAttr > 0)) {
        offerPriceAttr = roundMoney(originalPrice * (1 - offerPercent / 100));
    }

    const item = {
        title: title || "Menu item",
        price: originalPrice,
        original_price: originalPrice,
        quantity: 1,
        image,
        note: quantityText.trim() || "1 person",
        variation_id: variationId ? parseInt(variationId, 10) : null,
        offer_id: offerId ? parseInt(offerId, 10) : null,
        offer_percent: offerPercent > 0 ? offerPercent : 0,
        offer_price: offerPriceAttr > 0 ? offerPriceAttr : null,
        offer_min_total: offerMinTotal > 0 ? offerMinTotal : null,
        offer_applied: false,
        is_first_order: isFirstOrder,
        applicable_to: applicableTo,
    };
    item.id = buildCartItemId(item);
    return item;
};

const updateCartBadges = (cart) => {
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    document
        .querySelectorAll(".desktop-order-qty, .mobile-order-qty")
        .forEach((node) => {
            node.textContent = totalCount;
            node.setAttribute("aria-label", `${totalCount} items`);
        });
};

const renderCartItemPriceLabel = (item) => {
    const unit = getItemUnitPrice(item);
    const original = getItemOriginalPrice(item);
    if (item.offer_applied && original > unit) {
        return `<span class="text-decoration-line-through text-white me-1">${formatCurrency(original)}</span>${formatCurrency(unit)}`;
    }
    return formatCurrency(unit);
};

const renderCartDrawer = () => {
    const cart = getCartData();
    const cartDrawerItems = document.getElementById("cartDrawerItems");
    const subtotalNode = document.getElementById("cartDrawerSubtotal");
    const cartDrawerCount = document.getElementById("cartDrawerCount");

    if (!cartDrawerItems || !subtotalNode) return;

    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartDrawerCount) {
        cartDrawerCount.textContent =
            itemCount === 0
                ? "No items yet"
                : `${itemCount} item${itemCount === 1 ? "" : "s"}`;
    }

    const cartSubtotal = getCartOriginalTotal(cart);

    if (!cart.length) {
        cartDrawerItems.innerHTML = `
      <div class="cart-drawer-empty">
        <div class="cart-drawer-empty-icon" aria-hidden="true"><i class="bi bi-bag"></i></div>
        <p class="cart-drawer-empty-title">Your cart is empty</p>
        <p class="cart-drawer-empty-text">Add dishes from the menu to get started.</p>
      </div>`;
    } else {
        cartDrawerItems.innerHTML = cart
            .map(
                (item) => `
        <article class="cart-item" data-item-id="${item.id}">
          <div class="cart-item-image-wrap">
            <img src="${item.image}" alt="${item.title}" class="cart-item-image" />
          </div>
          <div class="cart-item-body">
            <div class="cart-item-header-row">
              <div class="cart-item-info">
                <h6 class="cart-item-title">${item.title}</h6>
                <span class="cart-item-unit">${renderCartItemPriceLabel(item)} each${renderCartOfferSuffix(item)}</span>
                ${renderCartMinOrderBadge(item, cartSubtotal)}
              </div>
              <button class="cart-item-remove-btn" type="button" aria-label="Remove ${item.title}">
                <i class="bi bi-trash3"></i>
              </button>
            </div>
            <div class="cart-item-footer-row">
              <div class="cart-qty-row">
                <button class="qty-adjust-btn" type="button" data-change="-1" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
                <span class="cart-qty">${item.quantity}</span>
                <button class="qty-adjust-btn" type="button" data-change="1" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
              </div>
              <div class="cart-item-meta">${formatCurrency(getItemUnitPrice(item) * item.quantity)}</div>
            </div>
          </div>
        </article>`,
            )
            .join("");
    }

    subtotalNode.textContent = formatCurrency(getCartTotal(cart));
    updateCartBadges(cart);

    const checkoutBtn = document.querySelector(
        ".cart-drawer .cart-checkout-btn",
    );
    if (checkoutBtn)
        checkoutBtn.classList.toggle("is-cart-empty", !cart.length);
};

const renderCartPage = () => {
    const cart = getCartData();
    const cartPageItems = document.getElementById("cartPageItems");
    const cartPageSubtotal = document.getElementById("cartPageSubtotal");
    const cartPageTotal = document.getElementById("cartPageTotal");
    const cartCountBadge = document.querySelector(".cart-count-badge");
    const cartPageEmpty = document.getElementById("cartPageEmpty");

    if (!cartPageItems || !cartPageSubtotal || !cartPageTotal) return;

    if (!cart.length) {
        if (cartPageEmpty) cartPageEmpty.style.display = "block";
        cartPageItems.innerHTML = "";
        cartPageSubtotal.textContent = formatCurrency(0);
        cartPageTotal.textContent = formatCurrency(0);
        const emptyHeading = document.getElementById("cartPageHeading");
        if (emptyHeading) emptyHeading.textContent = "0 items in your cart";
        const emptyCount = document.getElementById("cartPageItemCount");
        if (emptyCount) emptyCount.textContent = "(0 items)";
        renderOfferUnlockNotice(
            document.getElementById("cartPageOfferNotice"),
            cart,
        );
        if (cartCountBadge) cartCountBadge.textContent = "0 Items";
        return;
    }

    if (cartPageEmpty) cartPageEmpty.style.display = "none";
    const cartSubtotal = getCartOriginalTotal(cart);
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);

    cartPageItems.innerHTML = cart
        .map(
            (item) => `
      <div class="cart-product-card" data-item-id="${item.id}">
        <div class="cart-product-img-wrap">
          <img src="${item.image}" alt="${item.title}" class="cart-product-img" />
        </div>
        <div class="cart-product-body">
          <div class="cart-product-top">
            <div>
              <h6 class="cart-product-name">${item.title}</h6>
              <span class="cart-product-tag">${item.note}${renderCartOfferSuffix(item)}</span>
              ${renderCartMinOrderBadge(item, cartSubtotal)}
            </div>
            <button class="btn cart-remove-btn" type="button" aria-label="Remove item"><i class="bi bi-x-lg"></i></button>
          </div>
          <div class="cart-product-bottom">
            <div class="cart-product-qty">
              <button class="btn cart-qty-btn" type="button" data-change="-1"><i class="bi bi-dash"></i></button>
              <span class="cart-qty-val">${item.quantity}</span>
              <button class="btn cart-qty-btn" type="button" data-change="1"><i class="bi bi-plus"></i></button>
            </div>
            <div class="cart-product-price-wrap">
              <span class="cart-product-unit">${renderCartItemPriceLabel(item)} × ${item.quantity}</span>
              <strong class="cart-product-total">${formatCurrency(getItemUnitPrice(item) * item.quantity)}</strong>
            </div>
          </div>
        </div>
      </div>`,
        )
        .join("");

    cartPageSubtotal.textContent = formatCurrency(getCartTotal(cart));
    cartPageTotal.textContent = formatCurrency(getCartTotal(cart));

    const cartSectionLabel = document.getElementById("cartPageHeading");
    if (cartSectionLabel) {
        cartSectionLabel.textContent = `${itemCount} item${itemCount === 1 ? "" : "s"} in your cart`;
    } else {
        const legacyLabel = document.querySelector(".cart-section-label");
        if (legacyLabel)
            legacyLabel.innerHTML = `<i class="bi bi-list-check me-2"></i>${itemCount} item${itemCount === 1 ? "" : "s"} in your cart`;
    }
    const cartPageItemCount = document.getElementById("cartPageItemCount");
    if (cartPageItemCount)
        cartPageItemCount.textContent = `(${itemCount} item${itemCount === 1 ? "" : "s"})`;
    if (cartCountBadge) cartCountBadge.textContent = `${itemCount} Items`;

    renderOfferUnlockNotice(
        document.getElementById("cartPageOfferNotice"),
        cart,
    );
};

/**
 * A single line under the cart totals: tells the customer how much more is
 * needed to switch the locked offer(s) on, so the "Min ৳X order" pills on the
 * items are not a dead end. Hidden when nothing is locked.
 */
const renderOfferUnlockNotice = (node, cart) => {
    if (!node) return;

    const subtotal = getCartOriginalTotal(cart);
    const locked = cart
        .map((item) => ({ item, min: offerLockedByMinimum(item, subtotal) }))
        .filter(
            ({ item, min }) =>
                min > 0 && (parseFloat(item.offer_percent) || 0) > 0,
        );

    if (!locked.length) {
        node.innerHTML = "";
        node.hidden = true;
        return;
    }

    // One notice for the whole cart: the smallest minimum is the first to unlock.
    const best = locked.reduce((a, b) => (b.min < a.min ? b : a));
    const missing = roundMoney(best.min - subtotal);
    const percent = parseFloat(best.item.offer_percent) || 0;

    node.innerHTML = `<i class="bi bi-info-circle-fill" aria-hidden="true"></i> আরও ${formatCurrency(missing)} মূল্যের খাবার অর্ডার করুন, তাহলে ${locked.length > 1 ? "অফারের খাবারে" : "অফারের খাবারে"} ${percent}% ছাড় পাবেন (ন্যূনতম অর্ডার ৳${formatAmount(best.min, 0)})।`;
    node.hidden = false;
};

const renderCheckoutSummary = () => {
    const cart = getCartData();
    const checkoutItemsWrap = document.getElementById("orderSummaryList");
    const checkoutSubtotal = document.getElementById("checkoutSubtotal");
    const checkoutTotal = document.getElementById("checkoutTotal");
    const orderTotalInput = document.querySelector("input[name='order_total']");
    const itemsInput = document.querySelector("input[name='items']");
    const itemCountEl = document.getElementById("itemCount");
    const tpl = document.getElementById("checkoutItemTpl");

    if (!checkoutItemsWrap || !checkoutSubtotal || !checkoutTotal) return;

    const emptyState = checkoutItemsWrap.querySelector(
        ".checkout-summary-empty",
    );

    if (!cart.length) {
        checkoutItemsWrap
            .querySelectorAll(".checkout-order-item")
            .forEach((el) => el.remove());
        if (emptyState) emptyState.style.display = "";
        checkoutSubtotal.textContent = formatCurrency(0);
        checkoutTotal.textContent = formatCurrency(0);
        if (orderTotalInput) orderTotalInput.value = "0";
        if (itemsInput) itemsInput.value = JSON.stringify([]);
        if (itemCountEl) itemCountEl.textContent = "(0 items)";
        renderOfferUnlockNotice(
            document.getElementById("checkoutOfferNotice"),
            cart,
        );
        return;
    }

    if (emptyState) emptyState.style.display = "none";
    checkoutItemsWrap
        .querySelectorAll(".checkout-order-item")
        .forEach((el) => el.remove());

    // min_total is measured against the undiscounted cart subtotal, so it is
    // resolved once here and shared by every line in the summary.
    const originalTotal = getCartOriginalTotal(cart);

    cart.forEach((item) => {
        let row;
        if (tpl) {
            row = tpl.content.cloneNode(true);
        } else {
            row = document.createElement("div");
            row.innerHTML = `<div class="checkout-order-item"><div class="checkout-order-img-wrap"><img class="checkout-order-img" /></div><div class="checkout-order-body"><div class="checkout-order-top"><p class="checkout-order-name"></p><span class="checkout-order-tag text-white"></span></div><div class="checkout-order-flags"></div><div class="checkout-order-bottom"><span class="checkout-order-price"></span><strong class="checkout-order-subtotal"></strong></div></div></div>`;
            row = row.firstElementChild;
        }

        const root = row.querySelector ? row : row;
        const img = root.querySelector(".checkout-order-img");
        const name = root.querySelector(".checkout-order-name");
        const tag = root.querySelector(".checkout-order-tag");
        const price = root.querySelector(".checkout-order-price");
        const subtotal = root.querySelector(".checkout-order-subtotal");
        const flags = root.querySelector(".checkout-order-flags");
        const itemWrap = root.querySelector(".checkout-order-item") || root;

        if (itemWrap.dataset) itemWrap.dataset.itemId = item.id;
        if (img) {
            img.src = item.image;
            img.alt = item.title;
        }
        if (name) name.textContent = item.title;
        if (tag) {
            const parts = [item.note || ""];
            if (item.offer_applied && item.offer_percent)
                parts.push(`${item.offer_percent}% OFF`);
            tag.textContent = parts.filter(Boolean).join(" · ");
        }
        // Offer locked behind its min_total: badge only, price stays at list price.
        const minBadge = renderCartMinOrderBadge(item, originalTotal).replace(
            "cart-min-order-badge",
            "cart-min-order-badge checkout-min-order-badge",
        );
        if (flags) {
            flags.innerHTML = minBadge;
        } else if (minBadge) {
            const orderTop = root.querySelector(".checkout-order-top");
            if (orderTop) orderTop.insertAdjacentHTML("afterend", minBadge);
        }
        if (price)
            price.innerHTML =
                renderCartItemPriceLabel(item) + ` &times; ${item.quantity}`;
        if (subtotal)
            subtotal.textContent = formatCurrency(
                getItemUnitPrice(item) * item.quantity,
            );

        checkoutItemsWrap.appendChild(row);
    });

    const discountedTotal = getCartTotal(cart);
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    checkoutSubtotal.textContent = formatCurrency(discountedTotal);
    checkoutTotal.textContent = formatCurrency(discountedTotal);
    if (orderTotalInput) orderTotalInput.value = originalTotal.toFixed(2);
    if (itemsInput) itemsInput.value = JSON.stringify(cart);
    if (itemCountEl)
        itemCountEl.textContent = `(${itemCount} item${itemCount === 1 ? "" : "s"})`;
    renderOfferUnlockNotice(
        document.getElementById("checkoutOfferNotice"),
        cart,
    );
    document.dispatchEvent(
        new CustomEvent("cartSummaryRendered", {
            detail: { total: discountedTotal, originalTotal },
        }),
    );
};

const addToCart = (item) => {
    const cart = getCartData();
    const existing = cart.find((entry) => entry.id === item.id);
    if (existing) {
        existing.quantity += 1;
        if (item.offer_min_total && !existing.offer_min_total)
            existing.offer_min_total = item.offer_min_total;
        if (item.offer_percent && !existing.offer_percent)
            existing.offer_percent = item.offer_percent;
        if (item.offer_id && !existing.offer_id)
            existing.offer_id = item.offer_id;
        if (item.offer_price && !existing.offer_price)
            existing.offer_price = item.offer_price;
        if (item.is_first_order != null && existing.is_first_order == null)
            existing.is_first_order = item.is_first_order;
    } else {
        cart.push(item);
    }
    persistCart(cart);
};

const removeFromCart = (itemId) => {
    persistCart(getCartData().filter((item) => item.id !== itemId));
};

const changeCartQuantity = (itemId, delta) => {
    const cart = getCartData().map((item) => {
        if (item.id !== itemId) return item;
        return { ...item, quantity: Math.max(1, item.quantity + delta) };
    });
    persistCart(cart.filter((item) => item.quantity > 0));
};

const clearCart = () => {
    persistCart([]);
};

const openCartDrawer = () => {
    const drawerEl = document.getElementById("cartDrawer");
    if (!drawerEl || !window.bootstrap?.Offcanvas) return;
    bootstrap.Offcanvas.getOrCreateInstance(drawerEl).show();
};

const initCartEvents = () => {
    document.addEventListener("click", (event) => {
        const menuCard = event.target.closest(".pcard, .menu-offer-card");
        if (menuCard?.querySelector(".pcard-cart-btn, .menu-offer-cart-btn")) {
            event.preventDefault();
            const item = createMenuItemFromCard(menuCard);
            if (item) {
                addToCart(item);
                openCartDrawer();
            }
            return;
        }

        const removeButton = event.target.closest(
            ".cart-item-remove-btn, .cart-remove-btn",
        );
        if (removeButton) {
            const card = removeButton.closest("[data-item-id]");
            if (card) removeFromCart(card.getAttribute("data-item-id"));
            return;
        }

        const qtyButton = event.target.closest(
            ".qty-adjust-btn, .cart-qty-btn",
        );
        if (qtyButton) {
            const change = Number(
                qtyButton.dataset.change ||
                    qtyButton.getAttribute("data-change") ||
                    0,
            );
            const card = qtyButton.closest("[data-item-id]");
            if (card && change !== 0)
                changeCartQuantity(card.getAttribute("data-item-id"), change);
            return;
        }

        if (event.target.closest(".cart-clear-btn")) {
            event.preventDefault();
            clearCart();
        }
    });
};

const initCartPages = () => {
    if (new URLSearchParams(window.location.search).get("clear_cart") === "1") {
        localStorage.removeItem(CART_STORAGE_KEY);
    }
    persistCart(getCartData());
    initCartEvents();
};

initCartPages();
