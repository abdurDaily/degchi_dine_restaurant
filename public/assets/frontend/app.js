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

navLinks.forEach((link) => {
    if (link.getAttribute("aria-current") === "page") {
        link.classList.add("active");
    }
});

let lastScrollTick = 0;
const SCROLL_THROTTLE_MS = 16;
window.addEventListener("scroll", () => {
    const now = performance.now();
    if (now - lastScrollTick < SCROLL_THROTTLE_MS) return;
    lastScrollTick = now;

    if (currentPageFile !== "index.html") {
        syncNavbarState();
        return;
    }

    // Batch DOM reads before writes to avoid forced reflow
    const current = Array.from(sections).find((section) => {
        const top = section.offsetTop - 120;
        const bottom = top + section.offsetHeight;
        return window.scrollY >= top && window.scrollY < bottom;
    });

    // Write after reads
    syncNavbarState();

    if (!current) return;

    const anchorLinks = Array.from(navLinks).filter((link) => {
        const href = link.getAttribute("href") || "";
        const linkUrl = new URL(href, window.location.href);
        return (
            linkUrl.origin === window.location.origin &&
            linkUrl.pathname === window.location.pathname &&
            linkUrl.hash
        );
    });

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

document.addEventListener("click", function (e) {
    const btn = e.target.closest("#continueAsGuestBtn");
    if (!btn) return;
    const pending = window.__pendingCheckoutForm;
    if (!pending) return;
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
    $(pending).submit();
});

/* ==========================================================================
   DISHES HIGHLIGHTS SLIDER INITIALIZATION
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
            { breakpoint: 1200, settings: { slidesToShow: 3 } },
            { breakpoint: 992, settings: { slidesToShow: 2 } },
            { breakpoint: 768, settings: { slidesToShow: 2, arrows: false, dots: true } },
            { breakpoint: 576, settings: { slidesToShow: 1, arrows: false, dots: true, centerMode: true, centerPadding: "20px" } },
        ],
    });
});

/* ── Featured Dishes Quick View Modal ─────────────────────── */
(function () {
    const modalEl = document.getElementById("mcQuickViewModal");
    if (!modalEl || !window.bootstrap?.Modal) return;

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
        if (!img || !badge || !title || !desc || !serve || !price) return;

        modalImage.src = img.getAttribute("src") || "";
        modalImage.alt = img.getAttribute("alt") || title.textContent?.trim() || "Dish preview";
        modalBadge.textContent = badge.textContent?.trim() || "Dish";
        modalBadge.classList.toggle("mc-badge--gold", badge.classList.contains("mc-badge--gold"));
        modalTitle.textContent = title.textContent?.trim() || "";
        modalDesc.textContent = desc.textContent?.trim() || "";
        modalServe.innerHTML = serve.innerHTML;
        modalPrice.textContent = price.textContent?.trim() || "";
        modal.show();
    };

    document.addEventListener("click", (event) => {
        const card = event.target.closest(".mc-card-trigger");
        if (!card) return;
        if (card.closest(".slick-slider")?.querySelector(".slick-list.dragging")) return;
        openQuickView(card);
    });

    document.addEventListener("keydown", (event) => {
        const card = event.target.closest(".mc-card-trigger");
        if (!card) return;
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            openQuickView(card);
        }
    });
})();

/* ==========================================================================
   HOUSE SIGNATURES & MAIN MENU SLIDER
   ========================================================================== */
$(function () {
    const $menuSlider = $("#menuSlider");
    if (!$menuSlider.length) return;

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
            { breakpoint: 1200, settings: { slidesToShow: 3, slidesToScroll: 1, dots: false } },
            { breakpoint: 992, settings: { slidesToShow: 2, slidesToScroll: 1, dots: false } },
            { breakpoint: 768, settings: { slidesToShow: 1, slidesToScroll: 1, arrows: true, dots: false, centerMode: false, centerPadding: "0px" } },
            { breakpoint: 576, settings: { slidesToShow: 1, slidesToScroll: 1, arrows: true, dots: false, centerMode: false, centerPadding: "0px" } },
        ],
    });

    $menuSlider.addClass("is-slick-ready");
});

/* ==========================================================================
   WATCH US ON REELS HUB SLIDER
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
        prevArrow: '<button type="button" class="slick-prev reels-slick-prev"><span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-left"></i></span></button>',
        nextArrow: '<button type="button" class="slick-next reels-slick-next"><span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-right"></i></span></button>',
        responsive: [
            { breakpoint: 1200, settings: { slidesToShow: 3, dots: false } },
            { breakpoint: 992, settings: { slidesToShow: 2, dots: false } },
            { breakpoint: 768, settings: { slidesToShow: 1, slidesToScroll: 1, arrows: false, dots: false, centerMode: false, centerPadding: "0px" } },
            { breakpoint: 576, settings: { slidesToShow: 1, slidesToScroll: 1, arrows: false, dots: false, centerMode: false, centerPadding: "0px" } },
        ],
    });
});

/* ── Floating Action Button: WhatsApp ────────────────────── */
(function () {
    const whatsappBtn = document.getElementById("whatsappBtn");
    if (!whatsappBtn) return;

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
const $reviewsSlider = $(".reviews-slider");
if ($reviewsSlider.length && typeof $reviewsSlider.slick === "function") {
    $reviewsSlider.slick({
        centerMode: true,
        centerPadding: "0px",
        slidesToShow: 3,
        infinite: true,
        speed: 900,
        cssEase: "cubic-bezier(0.23, 1, 0.32, 1)",
        autoplay: true,
        autoplaySpeed: 4000,
        dots: true,
        arrows: false,
        useTransform: true,
        responsive: [
            { breakpoint: 768, settings: { slidesToShow: 1, slidesToScroll: 1, centerMode: false, centerPadding: "0px", dots: true } },
            { breakpoint: 576, settings: { slidesToShow: 1, slidesToScroll: 1, centerMode: false, centerPadding: "0px", dots: true } },
        ],
    });
}

// menu card slider
$(document).ready(function () {
    const $mainCarousel = $(".js-main-carousel");
    if (!$mainCarousel.length || typeof $mainCarousel.slick !== "function") return;
    $mainCarousel.slick({
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

    let isMobile = window.innerWidth <= 991;

    const $modalCarousel = $(".js-modal-nav-carousel");
    if ($modalCarousel.length && typeof $modalCarousel.slick === "function") {
        $modalCarousel.slick({
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
                { breakpoint: 991, settings: { vertical: false, verticalSwiping: false, arrows: false, slidesToShow: 3, variableWidth: true } },
                { breakpoint: 480, settings: { vertical: false, verticalSwiping: false, arrows: false, slidesToShow: 2, variableWidth: true } },
            ],
        });
    }

    $(window).on("resize", function () {
        const checkMobile = window.innerWidth <= 991;
        if (checkMobile !== isMobile) {
            isMobile = checkMobile;
            if ($mainCarousel.length) $mainCarousel.slick("slickSetOption", "slidesToShow", isMobile ? 2 : 4, true);
            if ($modalCarousel.length) $modalCarousel.slick("slickSetOption", "slidesToShow", isMobile ? 1 : 4, true);
        }
    });

    $modalCarousel.on("afterChange", function (event, slick, currentSlide) {
        const activeImgSrc = $(slick.$slides[currentSlide]).attr("data-img");
        $("#modal-active-display-img").attr("src", activeImgSrc);
    });

    $(".menu-thumb-card").on("click", function () {
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

    $(".js-close-modal, .js-modal-overlay").on("click", function (e) {
        if (e.target === this || $(this).hasClass("js-close-modal") || $(this).parents(".js-close-modal").length) {
            $(".js-modal-overlay").removeClass("active");
            $mainCarousel.slick("slickPlay");
        }
    });
});

$(document).ready(function () {
    if (typeof $.fn.slick === "undefined") return;

    const $sliderFor = $(".slider-for");
    if ($sliderFor.length) {
        $sliderFor.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: true,
            cssEase: "cubic-bezier(0.25, 1, 0.5, 1)",
            speed: 800,
            asNavFor: ".slider-nav",
            prevArrow: $(".custom-prev"),
            nextArrow: $(".custom-next"),
        });
        $sliderFor.closest(".platter-card").addClass("is-slick-ready");
    }

    const $sliderNav = $(".slider-nav");
    if ($sliderNav.length) {
        $sliderNav.slick({
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
            speed: 800,
            responsive: [
                { breakpoint: 991, settings: { vertical: false, verticalSwiping: false, centerMode: true, centerPadding: "0px", slidesToShow: 3 } },
                { breakpoint: 576, settings: { vertical: false, verticalSwiping: false, centerMode: true, centerPadding: "24px", slidesToShow: 1 } },
            ],
        });
    }

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
        if (e.target === this || $(this).hasClass("menu-modal-close") || $(this).closest(".menu-modal-close").length) {
            $("#menuPopup").fadeOut(300);
        }
    });
});

/* ── Floating Action Button Group ─────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const group = document.getElementById('floatingActionGroup');
    const toggleBtn = document.getElementById('fabMainToggle');
    if (!group || !toggleBtn) return;

    function closeMenu() {
        group.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
        group.classList.add('is-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
    }

    toggleBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        group.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    document.addEventListener('click', function (e) {
        if (!group.contains(e.target)) closeMenu();
    });

    group.querySelectorAll('.fab-item').forEach(function (item) {
        item.addEventListener('click', function () { closeMenu(); });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
});

/* ── Slick cloned slides: prevent focus in aria-hidden containers ── */
document.addEventListener('DOMContentLoaded', function () {
    var focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    function inertClonedSlides() {
        document.querySelectorAll('.slick-cloned[aria-hidden="true"]').forEach(function (clone) {
            clone.querySelectorAll(focusableSelector).forEach(function (el) {
                el.setAttribute('tabindex', '-1');
            });
        });
    }
    inertClonedSlides();
    document.addEventListener('DOMNodeInserted', inertClonedSlides);
});
