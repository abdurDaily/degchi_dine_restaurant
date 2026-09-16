{{-- Floating Action Buttons --}}
<div class="floating-right" id="floatingActionGroup">
    <div class="fab-menu" id="fabMenu">
        @guest('member')
            @unless(request()->routeIs('frontend.order.track'))
            <button type="button" class="fab-item fab-track" data-bs-toggle="modal"
                data-bs-target="#trackOrderModal" aria-label="Track your order" title="Track Order">
                <span class="fab-item-icon"><iconify-icon icon="solar:delivery-linear" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Track Order</span>
            </button>
            @endunless
        @else
            @unless(request()->routeIs('frontend.order.track'))
            <a href="{{ route('frontend.order.track') }}" class="fab-item fab-track"
                aria-label="Track an order" title="Track Order">
                <span class="fab-item-icon"><iconify-icon icon="solar:delivery-linear" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Track Order</span>
            </a>
            @endunless
            <a href="{{ route('frontend.member.dashboard') }}" class="fab-item fab-dashboard"
                aria-label="My Dashboard" title="My Dashboard">
                <span class="fab-item-icon"><iconify-icon icon="solar:widget-5-bold" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Dashboard</span>
            </a>
        @endguest

        <a class="fab-item fab-messenger"
            href="https://m.me/YOUR_PAGE_USERNAME"
            target="_blank" rel="noopener noreferrer"
            aria-label="Chat with us on Messenger" title="Messenger">
            <span class="fab-item-icon"><iconify-icon icon="ri:messenger-fill" aria-hidden="true"></iconify-icon></span>
            <span class="fab-item-label">Messenger</span>
        </a>

        <a class="fab-item fab-whatsapp"
            href="https://wa.me/8801898795400?text=Hello%20Degchi%20Dine%20I%20have%20a%20question%20about%20ordering"
            target="_blank" rel="noopener noreferrer"
            aria-label="Chat with us on WhatsApp" title="WhatsApp">
            <span class="fab-item-icon"><i class="bi bi-whatsapp" aria-hidden="true"></i></span>
            <span class="fab-item-label">WhatsApp</span>
        </a>
    </div>

    <button type="button" id="fabMainToggle" class="fab-main" aria-label="Open contact options" aria-expanded="false">
        <iconify-icon icon="solar:chat-round-dots-bold" class="fab-icon-open" aria-hidden="true"></iconify-icon>
        <iconify-icon icon="solar:close-circle-bold" class="fab-icon-close" aria-hidden="true"></iconify-icon>
    </button>
</div>
