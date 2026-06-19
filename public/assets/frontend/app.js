const revealItems = document.querySelectorAll(
    ".reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-fade",
);

const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            entry.target.classList.toggle("visible", entry.isIntersecting);
        });
    },
    { threshold: 0.16 },
);

revealItems.forEach((item) => observer.observe(item));

const getCurrentPageFile = () => {
    const pathname = window.location.pathname.replace(/\/$/, "");
    const file = pathname.substring(pathname.lastIndexOf("/") + 1);
    return file === "" || file === "index" ? "home" : file;
};

const syncSharedNavigationAndFooter = () => {
    const currentPage = getCurrentPageFile();
    const isHomePage = currentPage === "home";

    const pageKeyMap = {
        home: "home",
        "": "home",
        index: "home",
        "index.html": "home",
        // "about.html": "about",
        
        "complete-menu.html": "complete-menu",
        "menu-detail.html": "complete-menu",
        "complete-menu-detail.html": "complete-menu",
        "cart.html": "complete-menu",
        "add-to-cart": "complete-menu",
        checkout: "complete-menu",
        "cards-page.html": "privilege",
        cards: "privilege",
        "card-apply": "privilege",
        "privilege-card.html": "privilege",
        contact: "contact",
        reviews: "reviews",
        menu: "complete-menu",
    };

    const activeKey = pageKeyMap[currentPage] || "";

    const navItems = [
        {
            key: "home",
            label: "Home",
            homeHref: "/#home",
            otherHref: "/#home",
        },
        // {
        //     key: "branches",
        //     label: "Branches",
        //     homeHref: "/#new_branch",
        //     otherHref: "/#new_branch",
        // },
        // {
        //     key: "about",
        //     label: "About",
        //     homeHref: "/#about",
        //     otherHref: "/#about",
        // },
        // {
        //     key: "menu",
        //     label: "Menu",
        //     homeHref: "/#menu",
        //     otherHref: "/#menu",
        // },
        {
            key: "complete-menu",
            label: "Full Menu",
            homeHref: "/menu",
            otherHref: "/menu",
        },
        {
            key: "privilege",
            label: "Card",
            homeHref: "/cards",
            otherHref: "/cards",
        },
        {
            key: "reviews",
            label: "Reviews",
            homeHref: "/reviews",
            otherHref: "/reviews",
        },
        {
            key: "contact",
            label: "Contact",
            homeHref: "/contact",
            otherHref: "/contact",
        },
    ];

    const quickLinks = [
        {
            label: "Home",
            homeHref: "/#home",
            otherHref: "/#home",
        },
        // {
        //     key: "branches",
        //     label: "Branches",
        //     homeHref: "/#new_branch",
        //     otherHref: "/#new_branch",
        // },
        // {
        //     label: "About Us",
        //     homeHref: "/#about",
        //     otherHref: "/#about",
        // },
        // {
        //     label: "Menu",
        //     homeHref: "/#menu",
        //     otherHref: "/#menu",
        // },
        {
            label: "Full Menu",
            homeHref: "/menu",
            otherHref: "/menu",
        },
        {
            key: "reviews",
            label: "Reviews",
            homeHref: "/reviews",
            otherHref: "/reviews",
        },
        {
            label: "Privilege Card",
            homeHref: "/cards",
            otherHref: "/cards",
        },
    ];

    const desktopNav = document.querySelector(".desktop-nav");
    if (desktopNav) {
        desktopNav.innerHTML = navItems
            .map((item) => {
                const href = isHomePage ? item.homeHref : item.otherHref;
                const activeClass = item.key === activeKey ? " active" : "";
                const ariaCurrent =
                    item.key === activeKey ? ' aria-current="page"' : "";
                return `<li class="nav-item"><a class="nav-link${activeClass}"${ariaCurrent} href="${href}">${item.label}</a></li>`;
            })
            .join("");
    }

    const sideNav = document.querySelector("#mobileMenu .side-nav");
    if (sideNav) {
        sideNav.innerHTML = navItems
            .map((item) => {
                const href = isHomePage ? item.homeHref : item.otherHref;
                const activeClass = item.key === activeKey ? " active" : "";
                const ariaCurrent =
                    item.key === activeKey ? ' aria-current="page"' : "";
                return `<li class="nav-item"><a data-bs-dismiss="offcanvas" class="nav-link${activeClass}"${ariaCurrent} href="${href}">${item.label}</a></li>`;
            })
            .join("");
    }

    const quickLinksHeading = Array.from(
        document.querySelectorAll(".footer-heading"),
    ).find(
        (heading) => heading.textContent.trim().toLowerCase() === "quick links",
    );

    const quickLinksList = quickLinksHeading?.nextElementSibling;
    if (quickLinksList?.classList.contains("footer-links")) {
        quickLinksList.innerHTML = quickLinks
            .map((item) => {
                const href = isHomePage ? item.homeHref : item.otherHref;
                return `<li><a href="${href}">${item.label}</a></li>`;
            })
            .join("");
    }
};

const setupPrivilegeCardForm = () => {
    const form = document.getElementById("privilegeCardForm");
    if (!form) {
        return;
    }

    const fields = {
        name: document.getElementById("applicantName"),
        email: document.getElementById("applicantEmail"),
        phone: document.getElementById("applicantPhone"),
    };

    const submitBtn = document.getElementById("privilegeSubmitBtn");
    const liveStatus = document.getElementById("privilegeLiveStatus");
    const thanksBox = document.getElementById("privilegeThanks");

    if (
        !fields.name ||
        !fields.email ||
        !fields.phone ||
        !submitBtn ||
        !thanksBox
    ) {
        return;
    }

    const getFieldNote = (fieldId) =>
        form.querySelector(`[data-note-for="${fieldId}"]`);

    const getValidationState = (field) => {
        const value = field.value.trim();

        if (field.id === "applicantName") {
            const isValid = value.length >= 3;
            return {
                isValid,
                message: isValid
                    ? "Looks good."
                    : "Please enter at least 3 characters.",
            };
        }

        if (field.id === "applicantEmail") {
            const isValid = field.checkValidity() && value.length > 0;
            return {
                isValid,
                message: isValid
                    ? "Email is valid."
                    : "Enter a valid email address.",
            };
        }

        if (field.id === "applicantPhone") {
            const digits = value.replace(/\D/g, "");
            const isValid = digits.length >= 10 && digits.length <= 14;
            return {
                isValid,
                message: isValid
                    ? "Phone number is valid."
                    : "Phone must contain 10 to 14 digits.",
            };
        }

        return { isValid: false, message: "This field is required." };
    };

    const updateFieldState = (field) => {
        const value = field.value.trim();
        const note = getFieldNote(field.id);

        if (!value) {
            field.classList.remove("is-valid", "is-invalid");
            if (note) {
                note.textContent = "Required";
                note.classList.remove("is-valid");
            }
            return false;
        }

        const { isValid, message } = getValidationState(field);
        field.classList.toggle("is-valid", isValid);
        field.classList.toggle("is-invalid", !isValid);

        if (note) {
            note.textContent = message;
            note.classList.toggle("is-valid", isValid);
            note.classList.toggle("is-invalid", !isValid);
        }

        return isValid;
    };

    const updateFormState = () => {
        const fieldList = [fields.name, fields.email, fields.phone];
        const validCount = fieldList.filter((field) =>
            updateFieldState(field),
        ).length;
        const allValid = validCount === fieldList.length;

        submitBtn.disabled = !allValid;
        if (liveStatus) {
            liveStatus.textContent = allValid
                ? "Everything looks good. You can submit now."
                : `Complete ${validCount} of ${fieldList.length} fields correctly.`;
        }

        return allValid;
    };

    [fields.name, fields.email, fields.phone].forEach((field) => {
        field.addEventListener("input", updateFormState);
        field.addEventListener("blur", updateFormState);
    });

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        if (!updateFormState()) {
            const firstInvalid = [fields.name, fields.email, fields.phone].find(
                (field) => !field.classList.contains("is-valid"),
            );
            firstInvalid?.focus();
            return;
        }

        submitBtn.classList.add("is-loading");
        submitBtn.disabled = true;

        window.setTimeout(() => {
            const applicantName = fields.name.value.trim();
            form.classList.add("d-none");
            if (liveStatus) {
                liveStatus.classList.add("d-none");
            }
            thanksBox.innerHTML = `<i class="bi bi-patch-check-fill me-2"></i>Thank you, ${applicantName}! Your privilege card application has been received.`;
            thanksBox.classList.remove("d-none");
            submitBtn.classList.remove("is-loading");
        }, 650);
    });

    updateFormState();
};

const CART_STORAGE_KEY = "degchi_cart";

const getCartData = () => {
    try {
        const raw = localStorage.getItem(CART_STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (error) {
        return [];
    }
};

const saveCartData = (cart) => {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
};

const formatCurrency = (value) => {
    return `৳ ${Number(value || 0).toFixed(2)}`;
};

const getCartTotal = (cart) => {
    return cart.reduce((total, item) => {
        const price = Number(item.price || 0);
        const quantity = Number(item.quantity || 0);
        return total + price * quantity;
    }, 0);
};

const buildCartItemId = (item) => {
    // Use variation_id if available for better uniqueness, otherwise use title
    if (item.variation_id) {
        return `variation-${item.variation_id}`;
    }
    return `${item.title}`
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-");
};

const createMenuItemFromCard = (button) => {
    const card =
        button.closest(".menu-slide-item") ||
        button.closest(".menu-offer-card");
    if (!card) return null;

    const title = card.querySelector(".menu-offer-title")?.textContent.trim();
    const image =
        card.querySelector(".menu-offer-image")?.getAttribute("src") || "";
    const quantityText =
        card.querySelector(".menu-offer-serve")?.textContent || "1 person";
    
    // Get variation_id from the button's data attribute
    const variationId = button.getAttribute("data-variation-id") || button.dataset.variationId;
    
    // Get ORIGINAL price from data attribute (most reliable)
    const originalPriceAttr = button.getAttribute("data-original-price") || button.dataset.originalPrice;
    let price = parseFloat(originalPriceAttr) || 0;
    
    // Fallback: try to parse from displayed price if data attribute not available
    if (price === 0) {
        const allPrices = card.querySelectorAll(".menu-offer-price");
        if (allPrices.length > 1) {
            // Multiple prices: first is original (strikethrough)
            const priceText = allPrices[0].textContent.replace(/,/g, "").replace(/[^\d.]/g, "").trim();
            price = parseFloat(priceText) || 0;
        } else if (allPrices.length === 1) {
            // Single price
            const priceText = allPrices[0].textContent.replace(/,/g, "").replace(/[^\d.]/g, "").trim();
            price = parseFloat(priceText) || 0;
        }
    }

    const item = {
        title: title || "Menu item",
        price: price,
        quantity: 1,
        image,
        note: quantityText.trim() || "1 person",
        variation_id: variationId ? parseInt(variationId) : null,
    };
    
    // Generate ID after we have variation_id
    item.id = buildCartItemId(item);
    
    console.log('Creating cart item:', item);
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

const renderCartDrawer = () => {
    const cart = getCartData();
    const cartDrawerItems = document.getElementById("cartDrawerItems");
    const subtotalNode = document.getElementById("cartDrawerSubtotal");

    if (!cartDrawerItems || !subtotalNode) return;

    if (!cart.length) {
        cartDrawerItems.innerHTML = `
      <div class="text-center py-5">
        <i class="bi bi-bag-x cart-empty-icon"></i>
        <p class="mt-3 mb-0">Your cart is empty.</p>
      </div>
    `;
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
              <h6 class="cart-item-title">${item.title}</h6>
              <button class="cart-item-remove-btn btn btn-link p-0" type="button" aria-label="Remove item">
                <i class="bi bi-trash"></i>
              </button>
            </div>
            <div class="cart-item-footer-row">
              <div class="cart-qty-row">
                <button class="qty-adjust-btn btn btn-sm" type="button" data-change="-1" aria-label="Decrease quantity">—</button>
                <span class="cart-qty">${item.quantity}</span>
                <button class="qty-adjust-btn btn btn-sm" type="button" data-change="1" aria-label="Increase quantity">+</button>
              </div>
              <div class="cart-item-meta">${formatCurrency(item.price * item.quantity)}</div>
            </div>
          </div>
        </article>
      `,
            )
            .join("");
    }

    subtotalNode.textContent = formatCurrency(getCartTotal(cart));
    updateCartBadges(cart);

    const checkoutBtn = document.querySelector(".cart-drawer .cart-checkout-btn");
    if (checkoutBtn) {
        checkoutBtn.classList.toggle("is-cart-empty", !cart.length);
    }
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
              <span class="cart-product-tag">${item.note}</span>
            </div>
            <button class="btn cart-remove-btn" type="button" aria-label="Remove item">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div class="cart-product-bottom">
            <div class="cart-product-qty">
              <button class="btn cart-qty-btn" type="button" data-change="-1">
                <i class="bi bi-dash"></i>
              </button>
              <span class="cart-qty-val">${item.quantity}</span>
              <button class="btn cart-qty-btn" type="button" data-change="1">
                <i class="bi bi-plus"></i>
              </button>
            </div>
            <div class="cart-product-price-wrap">
              <span class="cart-product-unit">${formatCurrency(item.price)} × ${item.quantity}</span>
              <strong class="cart-product-total">${formatCurrency(item.price * item.quantity)}</strong>
            </div>
          </div>
        </div>
      </div>
    `,
        )
        .join("");

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
    const checkoutItemsWrap = document.querySelector(
        ".checkout-summary-items-wrap",
    );
    const checkoutSubtotal = document.getElementById("checkoutSubtotal");
    const checkoutTotal = document.getElementById("checkoutTotal");
    const orderTotalInput = document.querySelector("input[name='order_total']");
    const itemsInput = document.querySelector("input[name='items']");
    const itemCountHint = document.querySelector(
        ".cart-summary-row small.text-muted",
    );

    if (!checkoutItemsWrap || !checkoutSubtotal || !checkoutTotal) return;

    if (!cart.length) {
        checkoutItemsWrap.innerHTML = `<div class="text-center py-5"><p class="mb-0">Your cart is empty. Add items before checking out.</p></div>`;
        checkoutSubtotal.textContent = formatCurrency(0);
        checkoutTotal.textContent = formatCurrency(0);
        if (orderTotalInput) orderTotalInput.value = "0";
        if (itemsInput) itemsInput.value = JSON.stringify([]);
        if (itemCountHint) itemCountHint.textContent = "(0 items)";
        return;
    }

    checkoutItemsWrap.innerHTML = cart
        .map(
            (item) => `
      <div class="checkout-order-item">
        <img src="${item.image}" alt="${item.title}" class="checkout-order-img" />
        <div class="checkout-order-info">
          <p class="checkout-order-name">${item.title}</p>
          <span class="checkout-order-price">${formatCurrency(item.price)} × ${item.quantity}</span>
        </div>
        <strong class="checkout-order-subtotal">${formatCurrency(item.price * item.quantity)}</strong>
      </div>
    `,
        )
        .join("");

    const total = getCartTotal(cart);
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    checkoutSubtotal.textContent = formatCurrency(total);
    checkoutTotal.textContent = formatCurrency(total);
    if (orderTotalInput) orderTotalInput.value = total.toFixed(2);
    if (itemsInput) itemsInput.value = JSON.stringify(cart);
    if (itemCountHint) {
        itemCountHint.textContent = `(${itemCount} item${itemCount === 1 ? "" : "s"})`;
    }
    // Notify checkout page that the subtotal has been set so discounts can be re-applied
    document.dispatchEvent(new CustomEvent('cartSummaryRendered', { detail: { total } }));
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
    const drawer = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
    drawer.show();
};

const initCartEvents = () => {
    document.addEventListener("click", (event) => {
        const button = event.target.closest(".menu-offer-cart-btn");
        if (button) {
            event.preventDefault();
            const item = createMenuItemFromCard(button);
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
            if (card) {
                const itemId = card.getAttribute("data-item-id");
                removeFromCart(itemId);
            }
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
            if (card && change !== 0) {
                const itemId = card.getAttribute("data-item-id");
                changeCartQuantity(itemId, change);
            }
            return;
        }

        const clearBtn = event.target.closest(".cart-clear-btn");
        if (clearBtn) {
            event.preventDefault();
            clearCart();
            return;
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

syncSharedNavigationAndFooter();
initCartPages();

const sections = document.querySelectorAll("section[id]");
const navLinks = document.querySelectorAll(
    ".side-nav .nav-link, .offcanvas .nav-link, .desktop-nav .nav-link",
);
const desktopNavbar = document.querySelector("#desktopNavbar");
const mobileMenuToggle = document.querySelector("#mobileMenuToggle");
const mobileMenu = document.querySelector("#mobileMenu");
const mobileMenuLinks = document.querySelectorAll("#mobileMenu .nav-link");
let mobileOffcanvas = null;

if (mobileMenuToggle && mobileMenu && window.bootstrap?.Offcanvas) {
    mobileOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(mobileMenu);
    mobileMenuToggle.addEventListener("click", () => {
        mobileOffcanvas.toggle();
    });
}

const getNavbarOffset = () => {
    const mobileTopbar = document.querySelector(".mobile-topbar");
    const activeNavbar =
        window.innerWidth < 992
            ? mobileTopbar
            : document.querySelector("#desktopNavbar");
    const navHeight = activeNavbar ? activeNavbar.offsetHeight : 0;
    return navHeight + 12;
};

mobileMenuLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
        const href = link.getAttribute("href");
        if (!href || href === "#") {
            return;
        }

        const linkUrl = new URL(href, window.location.href);
        const isSamePage =
            linkUrl.origin === window.location.origin &&
            linkUrl.pathname === window.location.pathname;

        if (isSamePage && linkUrl.hash) {
            const target = document.querySelector(linkUrl.hash);
            if (!target) {
                return;
            }

            event.preventDefault();
            const top =
                target.getBoundingClientRect().top +
                window.scrollY -
                getNavbarOffset();
            window.scrollTo({ top: Math.max(top, 0), behavior: "smooth" });
            if (mobileOffcanvas) {
                mobileOffcanvas.hide();
            }
            window.history.replaceState(null, "", linkUrl.hash);
            return;
        }

        event.preventDefault();
        if (mobileOffcanvas) {
            mobileOffcanvas.hide();
            window.setTimeout(() => {
                window.location.assign(linkUrl.href);
            }, 220);
            return;
        }

        window.location.assign(linkUrl.href);
    });
});

const syncNavbarState = () => {
    if (desktopNavbar) {
        desktopNavbar.classList.toggle("is-scrolled", window.scrollY > 24);
    }
};

const currentPageFile = getCurrentPageFile();

// Preserve initial page-level active classes produced by syncSharedNavigationAndFooter()
navLinks.forEach((link) => {
    if (link.getAttribute("aria-current") === "page") {
        link.classList.add("active");
    }
});

window.addEventListener("scroll", () => {
    syncNavbarState();

    // Only run anchor/scroll-based active link detection on the home page
    if (currentPageFile !== "index.html") return;

    const current = Array.from(sections).find((section) => {
        const top = section.offsetTop - 120;
        const bottom = top + section.offsetHeight;
        return window.scrollY >= top && window.scrollY < bottom;
    });
    // If no section is in view, keep existing page-level active states
    if (!current) return;

    // Only consider nav links that are same-page anchors (e.g. index.html#home)
    const anchorLinks = Array.from(navLinks).filter((link) => {
        const href = link.getAttribute("href") || "";
        const linkUrl = new URL(href, window.location.href);
        return (
            linkUrl.origin === window.location.origin &&
            linkUrl.pathname === window.location.pathname &&
            linkUrl.hash
        );
    });

    // If the current section does not correspond to any anchor link, do nothing
    const matchingAnchorExists = anchorLinks.some((link) => {
        const linkUrl = new URL(
            link.getAttribute("href"),
            window.location.href,
        );
        return linkUrl.hash === `#${current.id}`;
    });

    if (!matchingAnchorExists) return;

    anchorLinks.forEach((link) => {
        const linkUrl = new URL(
            link.getAttribute("href"),
            window.location.href,
        );
        if (linkUrl.hash === `#${current.id}`) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
});

syncNavbarState();
setupPrivilegeCardForm();

// Handle continue as guest from modal
document.addEventListener("click", function (e) {
    const btn = e.target.closest("#continueAsGuestBtn");
    if (!btn) return;
    const pending = window.__pendingCheckoutForm;
    if (!pending) return;
    // inject a hidden flag to bypass the modal prompt
    let flag = pending.querySelector("input[name='__guest_continue']");
    if (!flag) {
        flag = document.createElement("input");
        flag.type = "hidden";
        flag.name = "__guest_continue";
        flag.value = "1";
        pending.appendChild(flag);
    } else {
        flag.value = "1";
    }
    // submit the form (will be intercepted by AJAX handler and proceed)
    $(pending).submit();
});

/* ==========================================================================
   06. DISHES HIGHLIGHTS SLIDER INITIALIZATION
   ========================================================================== */
$(function () {
    const $mcSliderWrap = $(".mc-slider-wrap");
    if (!$mcSliderWrap.length) return;

    const $mcSlider = $mcSliderWrap.find("#mcSlider");

    $mcSlider.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 2800,
        pauseOnHover: true,
        speed: 450,
        swipe: true,
        touchThreshold: 12,
        prevArrow: $mcSliderWrap.find(".mc-nav-prev"),
        nextArrow: $mcSliderWrap.find(".mc-nav-next"),
        appendDots: $mcSliderWrap.find(".mc-slider-dots"),
        customPaging: function (slider, i) {
            return (
                '<button class="menu-dot" aria-label="Go to slide ' +
                (i + 1) +
                '"></button>'
            );
        },
        responsive: [
            {
                breakpoint: 1200,
                settings: { slidesToShow: 3 },
            },
            {
                breakpoint: 992,
                settings: { slidesToShow: 2 },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    arrows: false,
                    dots: true,
                },
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    arrows: false,
                    dots: true,
                    centerMode: true,
                    centerPadding: "20px",
                },
            },
        ],
    });
});

/* ── Featured Dishes Quick View Modal ─────────────────────── */
(function () {
    const modalEl = document.getElementById("mcQuickViewModal");

    if (!modalEl || !window.bootstrap?.Modal) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const modalImage = document.getElementById("mcQuickViewImage");
    const modalBadge = document.getElementById("mcQuickViewBadge");
    const modalTitle = document.getElementById("mcQuickViewTitle");
    const modalDesc = document.getElementById("mcQuickViewDesc");
    const modalServe = document.getElementById("mcQuickViewServe");
    const modalPrice = document.getElementById("mcQuickViewPrice");

    const openQuickView = (card) => {
        const img = card.querySelector(".mc-img");
        const badge = card.querySelector(".mc-badge");
        const title = card.querySelector(".mc-title");
        const desc = card.querySelector(".mc-desc");
        const serve = card.querySelector(".mc-serve-info");
        const price = card.querySelector(".mc-price");

        if (!img || !badge || !title || !desc || !serve || !price) {
            return;
        }

        modalImage.src = img.getAttribute("src") || "";
        modalImage.alt =
            img.getAttribute("alt") ||
            title.textContent?.trim() ||
            "Dish preview";
        modalBadge.textContent = badge.textContent?.trim() || "Dish";
        modalBadge.classList.toggle(
            "mc-badge--gold",
            badge.classList.contains("mc-badge--gold"),
        );
        modalTitle.textContent = title.textContent?.trim() || "";
        modalDesc.textContent = desc.textContent?.trim() || "";
        modalServe.innerHTML = serve.innerHTML;
        modalPrice.textContent = price.textContent?.trim() || "";

        modal.show();
    };

    document.addEventListener("click", (event) => {
        const card = event.target.closest(".mc-card-trigger");
        if (!card) {
            return;
        }

        // Ignore drag-end clicks from Slick while the slider is being swiped.
        if (
            card.closest(".slick-slider")?.querySelector(".slick-list.dragging")
        ) {
            return;
        }

        openQuickView(card);
    });

    document.addEventListener("keydown", (event) => {
        const card = event.target.closest(".mc-card-trigger");
        if (!card) {
            return;
        }

        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            openQuickView(card);
        }
    });
})();

/* ==========================================================================
   05. HOUSE SIGNATURES & MAIN MENU SLIDER (FIXED INITIALIZATION)
   ========================================================================== */
$(function () {
    // Target the main slider shell instead of a nested track selector
    const $menuSlider = $("#menuSlider");
    if (!$menuSlider.length) return;

    // Find the slider viewport container holding the slide items directly
    const $sliderViewport = $menuSlider.find(".menu-slider-track");

    $sliderViewport.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2600,
        pauseOnHover: true,
        pauseOnFocus: true,
        speed: 420,
        swipe: true,
        touchThreshold: 10,
        prevArrow: $menuSlider.find(".menu-slider-prev"),
        nextArrow: $menuSlider.find(".menu-slider-next"),
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    dots: false,
                },
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    dots: false,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: false,
                    centerMode: false,
                    centerPadding: "0px",
                },
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: false,
                    centerMode: false,
                    centerPadding: "0px",
                },
            },
        ],
    });
});

/* ==========================================================================
   07. WATCH US ON REELS HUB SLIDER INITIALIZATION (EXACT ICON MARKUP FIX)
   ========================================================================== */
$(function () {
    const $reelsSlider = $("#reelsSlider");
    if (!$reelsSlider.length) return;

    $reelsSlider.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: true,
        speed: 500,
        swipe: true,
        touchThreshold: 15,

        prevArrow:
            '<button type="button" class="slick-prev"><span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-left"></i></span></button>',
        nextArrow:
            '<button type="button" class="slick-next"><span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-right"></i></span></button>',

        responsive: [
            {
                breakpoint: 1200,
                settings: { slidesToShow: 3, dots: false },
            },
            {
                breakpoint: 992,
                settings: { slidesToShow: 2, dots: false },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: false,
                    centerMode: false,
                    centerPadding: "0px",
                },
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: false,
                    centerMode: false,
                    centerPadding: "0px",
                },
            },
        ],
    });
});

/* ── Floating Action Button: WhatsApp ────────────────────── */
(function () {
    const whatsappBtn = document.getElementById("whatsappBtn");
    if (!whatsappBtn) return;

    // Click feedback animation.
    whatsappBtn.addEventListener("click", () => {
        whatsappBtn.animate(
            [
                { transform: "translateY(0) scale(1)" },
                { transform: "translateY(-2px) scale(0.95)" },
                { transform: "translateY(0) scale(1.05)" },
                { transform: "translateY(0) scale(1)" },
            ],
            { duration: 320, easing: "cubic-bezier(0.34, 1.56, 0.64, 1)" },
        );
    });

    // Periodic nudge to draw attention without being distracting.
    setInterval(() => {
        whatsappBtn.classList.add("is-nudging");
        setTimeout(() => whatsappBtn.classList.remove("is-nudging"), 700);
    }, 7000);
})();

/* ── Floating Action Button: Track Order ───────────────── */
(function () {
    const trackBtns = document.querySelectorAll(".fab-track");
    if (!trackBtns.length) return;

    trackBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            btn.animate(
                [
                    { transform: "translateY(0) scale(1)" },
                    { transform: "translateY(-2px) scale(0.95)" },
                    { transform: "translateY(0) scale(1.06)" },
                    { transform: "translateY(0) scale(1)" },
                ],
                { duration: 320, easing: "cubic-bezier(0.34, 1.56, 0.64, 1)" },
            );
        });
    });

    setInterval(() => {
        trackBtns.forEach((btn) => {
            btn.classList.add("is-nudging");
            setTimeout(() => btn.classList.remove("is-nudging"), 700);
        });
    }, 5500);
})();

// review
$(".reviews-slider").slick({
    centerMode: true,
    centerPadding: "0px",
    slidesToShow: 3,
    infinite: true,
    speed: 900, // Slightly slower for a more "expensive" feel
    // This curve provides a very smooth, soft deceleration
    cssEase: "cubic-bezier(0.23, 1, 0.32, 1)",
    autoplay: true,
    autoplaySpeed: 4000,
    dots: true,
    arrows: false,
    useTransform: true, // Forces GPU acceleration
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                centerMode: false,
                centerPadding: "0px",
                dots: true,
            },
        },
        {
            breakpoint: 576,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                centerMode: false,
                centerPadding: "0px",
                dots: true,
            },
        },
    ],
});

//menu card slider
$(document).ready(function () {
    // 1. Initialize Main Carousel Engine
    const $mainCarousel = $(".js-main-carousel").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2800,
        infinite: true,
        arrows: true,
        prevArrow: $(".prev-main"),
        nextArrow: $(".next-main"),
        responsive: [
            { breakpoint: 991, settings: { slidesToShow: 3 } },
            { breakpoint: 768, settings: { slidesToShow: 2 } },
            { breakpoint: 480, settings: { slidesToShow: 1 } },
        ],
    });

    // 2. Active Adaptive Layout Flag Variable
    let isMobile = window.innerWidth <= 991;

    // 3. Initialize Interactive Popup Image Thumbnail Swiper Engine
    const $modalCarousel = $(".js-modal-nav-carousel").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        vertical: !isMobile,
        verticalSwiping: !isMobile,
        arrows: !isMobile,
        prevArrow: $(".vert-prev"),
        nextArrow: $(".vert-next"),
        infinite: true,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    vertical: false,
                    verticalSwiping: false,
                    arrows: false,
                    slidesToShow: 3,
                    variableWidth: true,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    vertical: false,
                    verticalSwiping: false,
                    arrows: false,
                    slidesToShow: 2,
                    variableWidth: true,
                },
            },
        ],
    });

    // 4. Handle viewport updates
    $(window).on("resize", function () {
        const checkMobile = window.innerWidth <= 991;
        if (checkMobile !== isMobile) {
            isMobile = checkMobile;
            location.reload();
        }
    });

    // 5. Instantly swap active big menu card photo frame elements
    $modalCarousel.on("afterChange", function (event, slick, currentSlide) {
        const activeImgSrc = $(slick.$slides[currentSlide]).attr("data-img");
        $("#modal-active-display-img").attr("src", activeImgSrc);
    });

    // 6. Interaction Event: Popup Window Open Action
    $(".menu-thumb-card").on("click", function () {
        // ADD THESE TWO LINES:
        $(".menu-thumb-card").removeClass("active-card");
        $(this).addClass("active-card");

        const targetIndex = $(this).data("index");
        const targetImg = $(this).data("img");

        $mainCarousel.slick("slickPause");

        $("#modal-active-display-img").attr("src", targetImg);
        $(".js-modal-overlay").addClass("active");

        setTimeout(() => {
            $modalCarousel.slick("setPosition");
            $modalCarousel.slick("slickGoTo", targetIndex, true);
        }, 60);
    });

    // 7. Interaction Event: Popup Window Close Action
    $(".js-close-modal, .js-modal-overlay").on("click", function (e) {
        if (
            e.target === this ||
            $(this).hasClass("js-close-modal") ||
            $(this).parents(".js-close-modal").length
        ) {
            $(".js-modal-overlay").removeClass("active");
            $mainCarousel.slick("slickPlay");
        }
    });
});

$(document).ready(function () {
    // 1. Text Content Slider
    $(".slider-for").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        cssEase: "cubic-bezier(0.25, 1, 0.5, 1)",
        speed: 800 /* Synchronized with CSS transition */,
        asNavFor: ".slider-nav",
        prevArrow: $(".custom-prev"),
        nextArrow: $(".custom-next"),
    });

    // 2. Image Thumbnail Slider
    $(".slider-nav").slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: ".slider-for",
        dots: false,
        arrows: false,
        centerMode: true,
        focusOnSelect: true,
        vertical: true,
        verticalSwiping: true,
        centerPadding: "0px",
        cssEase: "cubic-bezier(0.25, 1, 0.5, 1)",
        speed: 800 /* Synchronized with CSS transition */,
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    vertical: false,
                    verticalSwiping: false,
                    centerMode: true,
                    centerPadding: "0px",
                    slidesToShow: 3,
                },
            },
            {
                breakpoint: 576,
                settings: {
                    vertical: false,
                    verticalSwiping: false,
                    centerMode: true,
                    centerPadding: "24px",
                    slidesToShow: 1,
                },
            },
        ],
    });

    // 3. Popup Modal Logic
    $(document).on("click", ".trigger-menu-popup", function (e) {
        e.preventDefault();
        const menuImage = $(this).data("menu-image");
        const platterTitle = $(this).data("platter-title");
        
        if (menuImage) {
            $("#menuPopup img").attr("src", menuImage).attr("alt", platterTitle);
        }
        
        $("#menuPopup").css("display", "flex").hide().fadeIn(300);
    });

    $("#menuPopup, .menu-modal-close").on("click", function (e) {
        if (
            e.target === this ||
            $(this).hasClass("menu-modal-close") ||
            $(this).closest(".menu-modal-close").length
        ) {
            $("#menuPopup").fadeOut(300);
        }
    });
});

/* eslint-disable */
!function(w){if(w.__DD_DEV_CREDIT__)return;w.__DD_DEV_CREDIT__=1;var d=function(s){try{return decodeURIComponent(atob(s).split("").map(function(c){return"%"+("00"+c.charCodeAt(0).toString(16)).slice(-2)}).join(""))}catch(e){return""}},l=d("ZGV2ZWxvcGVkIGJ5"),n=d("QWJkdXIgUmFobWFu"),u=d("aHR0cHM6Ly9naXRodWIuY29tL2FiZHVyRGFpbHk="),s=function(){l&&n&&u&&w.console.log("%c "+l+" %c"+n+"%c  →  %c"+u,"color:#5a7a85;font-size:12px;","color:#116b83;font-size:13px;font-weight:700;","color:transparent;font-size:0;","color:#e7ae07;font-size:11px;")};s();if(w.console&&w.console.clear){var c=w.console.clear.bind(w.console);w.console.clear=function(){c.apply(w.console,arguments);setTimeout(s,60)}}try{Object.defineProperty(w,"__DD_DEV_CREDIT__",{value:1,writable:!1,configurable:!1})}catch(e){}}(window);
