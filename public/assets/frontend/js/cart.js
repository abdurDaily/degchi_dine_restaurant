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

const formatCurrency = (value) => {
    return `\u09F3 ${Number(value || 0).toFixed(2)}`;
};

const getItemUnitPrice = (item) => Number(item.price || 0);
const getItemOriginalPrice = (item) => Number(item.original_price ?? item.price ?? 0);

const getCartTotal = (cart) =>
    cart.reduce((t, item) => t + getItemUnitPrice(item) * Number(item.quantity || 0), 0);

const getCartOriginalTotal = (cart) =>
    cart.reduce((t, item) => t + getItemOriginalPrice(item) * Number(item.quantity || 0), 0);

const buildCartItemId = (item) => {
    if (item.variation_id) return `variation-${item.variation_id}`;
    return `${item.title}`.trim().toLowerCase().replace(/[^a-z0-9]+/g, "-");
};

const createMenuItemFromCard = (card) => {
    const menuCard = card?.closest?.(".pcard, .menu-offer-card") || card;
    if (!menuCard?.classList?.contains("pcard") && !menuCard?.classList?.contains("menu-offer-card")) return null;

    const cartBtn = menuCard.querySelector(".pcard-cart-btn, .menu-offer-cart-btn");
    if (!cartBtn) return null;

    const title = menuCard.querySelector(".pcard-title, .menu-offer-title")?.textContent.trim();
    const image = menuCard.querySelector(".pcard-img, .menu-offer-image")?.getAttribute("src") || "";
    const quantityText = menuCard.querySelector(".pcard-serve, .menu-offer-serve")?.textContent || "1 person";

    const variationId = cartBtn.getAttribute("data-variation-id") || cartBtn.dataset.variationId;

    let originalPrice = parseFloat(
        cartBtn.getAttribute("data-original-price") || cartBtn.dataset.originalPrice
    ) || 0;

    if (originalPrice === 0) {
        const allPrices = menuCard.querySelectorAll(".pcard-price, .menu-offer-price");
        if (allPrices.length > 1) {
            const oldPrice = menuCard.querySelector(".pcard-price-old, .menu-offer-price-old");
            const priceText = (oldPrice || allPrices[0]).textContent.replace(/,/g, "").replace(/[^\d.]/g, "").trim();
            originalPrice = parseFloat(priceText) || 0;
        } else if (allPrices.length === 1) {
            const priceText = allPrices[0].textContent.replace(/,/g, "").replace(/[^\d.]/g, "").trim();
            originalPrice = parseFloat(priceText) || 0;
        }
    }

    const offerPercent = parseFloat(cartBtn.getAttribute("data-offer-percent") || cartBtn.dataset.offerPercent || "0") || 0;
    const offerId = cartBtn.getAttribute("data-offer-id") || cartBtn.dataset.offerId || null;
    const isFirstOrder = (cartBtn.getAttribute("data-is-first-order") || cartBtn.dataset.isFirstOrder || "0") === "1";
    const applicableTo = (cartBtn.getAttribute("data-applicable-to") || cartBtn.dataset.applicableTo || "all").toLowerCase();

    const member = getMemberState();
    if (offerRequiresMemberLogin(isFirstOrder, applicableTo) && !member.loggedIn) {
        showOfferMemberLoginModal();
        return null;
    }

    let offerPriceAttr = parseFloat(cartBtn.getAttribute("data-offer-price") || cartBtn.dataset.offerPrice || "0") || 0;
    let price = originalPrice;
    let offerApplied = false;
    let appliedOfferPercent = 0;

    const canApplyOffer = offerPercent > 0 && (!offerRequiresMemberLogin(isFirstOrder) || (member.loggedIn && (!isFirstOrder || member.canUseFirstOrder !== false)));

    if (canApplyOffer) {
        price = offerPriceAttr > 0 ? offerPriceAttr : Math.round(originalPrice * (1 - offerPercent / 100) * 100) / 100;
        offerApplied = true;
        appliedOfferPercent = offerPercent;
    }

    const item = {
        title: title || "Menu item",
        price,
        original_price: originalPrice,
        quantity: 1,
        image,
        note: quantityText.trim() || "1 person",
        variation_id: variationId ? parseInt(variationId, 10) : null,
        offer_id: offerId ? parseInt(offerId, 10) : null,
        offer_percent: appliedOfferPercent,
        offer_applied: offerApplied,
    };
    item.id = buildCartItemId(item);
    return item;
};

const updateCartBadges = (cart) => {
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll(".desktop-order-qty, .mobile-order-qty").forEach((node) => {
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
        cartDrawerCount.textContent = itemCount === 0 ? "No items yet" : `${itemCount} item${itemCount === 1 ? "" : "s"}`;
    }

    if (!cart.length) {
        cartDrawerItems.innerHTML = `
      <div class="cart-drawer-empty">
        <div class="cart-drawer-empty-icon" aria-hidden="true"><i class="bi bi-bag"></i></div>
        <p class="cart-drawer-empty-title">Your cart is empty</p>
        <p class="cart-drawer-empty-text">Add dishes from the menu to get started.</p>
      </div>`;
    } else {
        cartDrawerItems.innerHTML = cart.map((item) => `
        <article class="cart-item" data-item-id="${item.id}">
          <div class="cart-item-image-wrap">
            <img src="${item.image}" alt="${item.title}" class="cart-item-image" />
          </div>
          <div class="cart-item-body">
            <div class="cart-item-header-row">
              <div class="cart-item-info">
                <h6 class="cart-item-title">${item.title}</h6>
                <span class="cart-item-unit">${renderCartItemPriceLabel(item)} each${item.offer_applied && item.offer_percent ? ` · ${item.offer_percent}% OFF` : ""}</span>
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
        </article>`).join("");
    }

    subtotalNode.textContent = formatCurrency(getCartTotal(cart));
    updateCartBadges(cart);

    const checkoutBtn = document.querySelector(".cart-drawer .cart-checkout-btn");
    if (checkoutBtn) checkoutBtn.classList.toggle("is-cart-empty", !cart.length);
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
        if (cartCountBadge) cartCountBadge.textContent = "0 Items";
        return;
    }

    if (cartPageEmpty) cartPageEmpty.style.display = "none";
    cartPageItems.innerHTML = cart.map((item) => `
      <div class="cart-product-card" data-item-id="${item.id}">
        <div class="cart-product-img-wrap">
          <img src="${item.image}" alt="${item.title}" class="cart-product-img" />
        </div>
        <div class="cart-product-body">
          <div class="cart-product-top">
            <div>
              <h6 class="cart-product-name">${item.title}</h6>
              <span class="cart-product-tag">${item.note}${item.offer_applied && item.offer_percent ? ` · ${item.offer_percent}% OFF` : ""}</span>
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
      </div>`).join("");

    cartPageSubtotal.textContent = formatCurrency(getCartTotal(cart));
    cartPageTotal.textContent = formatCurrency(getCartTotal(cart));

    const cartSectionLabel = document.querySelector(".cart-section-label");
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartSectionLabel) {
        cartSectionLabel.innerHTML = `<i class="bi bi-list-check me-2"></i>${itemCount} item${itemCount === 1 ? "" : "s"} in your cart`;
    }
    if (cartCountBadge) cartCountBadge.textContent = `${itemCount} Items`;
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

    const emptyState = checkoutItemsWrap.querySelector(".checkout-summary-empty");

    if (!cart.length) {
        checkoutItemsWrap.querySelectorAll(".checkout-order-item").forEach(el => el.remove());
        if (emptyState) emptyState.style.display = "";
        checkoutSubtotal.textContent = formatCurrency(0);
        checkoutTotal.textContent = formatCurrency(0);
        if (orderTotalInput) orderTotalInput.value = "0";
        if (itemsInput) itemsInput.value = JSON.stringify([]);
        if (itemCountEl) itemCountEl.textContent = "(0 items)";
        return;
    }

    if (emptyState) emptyState.style.display = "none";
    checkoutItemsWrap.querySelectorAll(".checkout-order-item").forEach(el => el.remove());

    cart.forEach((item) => {
        let row;
        if (tpl) {
            row = tpl.content.cloneNode(true);
        } else {
            row = document.createElement("div");
            row.innerHTML = `<div class="checkout-order-item"><div class="checkout-order-img-wrap"><img class="checkout-order-img" /></div><div class="checkout-order-body"><div class="checkout-order-top"><p class="checkout-order-name"></p><span class="checkout-order-tag text-white"></span></div><div class="checkout-order-bottom"><span class="checkout-order-price"></span><strong class="checkout-order-subtotal"></strong></div></div></div>`;
            row = row.firstElementChild;
        }

        const root = row.querySelector ? row : row;
        const img = root.querySelector(".checkout-order-img");
        const name = root.querySelector(".checkout-order-name");
        const tag = root.querySelector(".checkout-order-tag");
        const price = root.querySelector(".checkout-order-price");
        const subtotal = root.querySelector(".checkout-order-subtotal");
        const itemWrap = root.querySelector(".checkout-order-item") || root;

        if (itemWrap.dataset) itemWrap.dataset.itemId = item.id;
        if (img) { img.src = item.image; img.alt = item.title; }
        if (name) name.textContent = item.title;
        if (tag) {
            const parts = [item.note || ""];
            if (item.offer_applied && item.offer_percent) parts.push(`${item.offer_percent}% OFF`);
            tag.textContent = parts.filter(Boolean).join(" · ");
        }
        if (price) price.innerHTML = renderCartItemPriceLabel(item) + ` &times; ${item.quantity}`;
        if (subtotal) subtotal.textContent = formatCurrency(getItemUnitPrice(item) * item.quantity);

        checkoutItemsWrap.appendChild(row);
    });

    const discountedTotal = getCartTotal(cart);
    const originalTotal = getCartOriginalTotal(cart);
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    checkoutSubtotal.textContent = formatCurrency(discountedTotal);
    checkoutTotal.textContent = formatCurrency(discountedTotal);
    if (orderTotalInput) orderTotalInput.value = originalTotal.toFixed(2);
    if (itemsInput) itemsInput.value = JSON.stringify(cart);
    if (itemCountEl) itemCountEl.textContent = `(${itemCount} item${itemCount === 1 ? "" : "s"})`;
    document.dispatchEvent(new CustomEvent("cartSummaryRendered", { detail: { total: discountedTotal, originalTotal } }));
};

const addToCart = (item) => {
    const cart = getCartData();
    const existing = cart.find((entry) => entry.id === item.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push(item);
    }
    saveCartData(cart);
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
};

const removeFromCart = (itemId) => {
    const cart = getCartData().filter((item) => item.id !== itemId);
    saveCartData(cart);
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
};

const changeCartQuantity = (itemId, delta) => {
    const cart = getCartData().map((item) => {
        if (item.id !== itemId) return item;
        return { ...item, quantity: Math.max(1, item.quantity + delta) };
    });
    saveCartData(cart.filter((item) => item.quantity > 0));
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
};

const clearCart = () => {
    saveCartData([]);
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
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

        const removeButton = event.target.closest(".cart-item-remove-btn, .cart-remove-btn");
        if (removeButton) {
            const card = removeButton.closest("[data-item-id]");
            if (card) removeFromCart(card.getAttribute("data-item-id"));
            return;
        }

        const qtyButton = event.target.closest(".qty-adjust-btn, .cart-qty-btn");
        if (qtyButton) {
            const change = Number(qtyButton.dataset.change || qtyButton.getAttribute("data-change") || 0);
            const card = qtyButton.closest("[data-item-id]");
            if (card && change !== 0) changeCartQuantity(card.getAttribute("data-item-id"), change);
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
    renderCartDrawer();
    renderCartPage();
    renderCheckoutSummary();
    initCartEvents();
};

initCartPages();
