@extends('frontend.layout')

@section('meta_title', 'Member Dashboard')
@section('meta_robots', 'noindex, nofollow')

@push('front_css')
<style>
.member-dashboard .md-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-top: 24px;
    max-width: 100%;
}
.member-dashboard .md-hero-actions form {
    display: contents;
}
.member-dashboard .md-hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.25s ease;
    white-space: nowrap;
    max-width: 100%;
}
.member-dashboard .md-hero-btn-outline {
    background: transparent;
    border: 1px solid rgba(255, 107, 53, 0.45);
    color: var(--brand-secondary);
}
.member-dashboard .md-hero-btn-outline:hover {
    background: rgba(255, 107, 53, 0.12);
    color: var(--brand-secondary);
}
.member-dashboard .md-hero-btn-solid,
.member-dashboard .md-hero-btn.is-active {
    background: var(--brand-secondary);
    border: 1px solid var(--brand-secondary);
    color: #fff;
}
.member-dashboard .md-hero-btn-solid:hover,
.member-dashboard .md-hero-btn.is-active:hover {
    background: var(--brand-secondary-dark);
    color: #fff;
}
.member-dashboard .md-main-box {
    margin-top: -100px;
    position: relative;
    z-index: 2;
    overflow-x: hidden;
}
.member-dashboard .md-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
.member-dashboard .md-stat-card {
    background: linear-gradient(145deg, #fff 0%, #faf8f4 100%);
    border: 1px solid var(--dd-border);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    min-width: 0;
}
.member-dashboard .md-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255, 107, 53, 0.1);
    color: var(--brand-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin-bottom: 10px;
}
.member-dashboard .md-stat-value {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--dd-text-main);
    line-height: 1.2;
    word-break: break-word;
}
.member-dashboard .md-stat-label {
    font-size: 0.78rem;
    color: var(--dd-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 4px;
}
.member-dashboard .md-sidebar {
    background: linear-gradient(160deg, #1f1412 0%, #2d1f1a 100%);
    border-radius: 20px;
    padding: 28px;
    color: #fff;
    height: 100%;
    min-width: 0;
}
.member-dashboard .md-card-visual {
    background: linear-gradient(135deg, rgba(255,107,53,0.18) 0%, rgba(255,255,255,0.06) 100%);
    border: 1px solid rgba(255, 107, 53, 0.35);
    border-radius: 16px;
    padding: 22px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.member-dashboard .md-card-visual::after {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(255,107,53,0.2) 0%, transparent 70%);
    border-radius: 50%;
}
.member-dashboard .md-card-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: rgba(255,255,255,0.55);
    margin-bottom: 8px;
}
.member-dashboard .md-card-number {
    font-family: 'Courier New', monospace;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--brand-secondary);
    letter-spacing: 0.06em;
    word-break: break-all;
}
.member-dashboard .md-copy-btn {
    margin-top: 12px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    font-size: 0.78rem;
    padding: 6px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
    max-width: 100%;
}
.member-dashboard .md-copy-btn:hover { background: rgba(255,255,255,0.14); }
.member-dashboard .md-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.member-dashboard .md-info-list li {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    font-size: 0.9rem;
}
.member-dashboard .md-info-list li:last-child { border-bottom: none; }
.member-dashboard .md-info-list span:first-child {
    color: rgba(255,255,255,0.55);
    font-size: 0.82rem;
    flex-shrink: 0;
}
.member-dashboard .md-info-list strong,
.member-dashboard .md-info-list .md-badge {
    text-align: right;
    word-break: break-word;
    max-width: 60%;
}
.member-dashboard .md-login-tip {
    margin-top: 16px;
    padding: 16px;
    border-radius: 12px;
    background: rgba(255, 107, 53, 0.1);
    border: 1px dashed rgba(255, 107, 53, 0.35);
}
.member-dashboard .md-login-tip h6 {
    color: var(--brand-secondary);
    font-size: 0.82rem;
    font-weight: 700;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.member-dashboard .md-login-tip p {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.75);
    margin: 0;
    line-height: 1.55;
    word-break: break-word;
}
.member-dashboard .md-orders-panel {
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--dd-border);
    padding: 28px;
    box-shadow: 0 20px 50px rgba(31, 20, 18, 0.06);
    min-width: 0;
}
.member-dashboard .md-panel-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--dd-border);
}
.member-dashboard .md-panel-header h3 {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--dd-text-main);
    margin: 0;
    word-break: break-word;
}
.member-dashboard .md-order-success {
    background: linear-gradient(135deg, rgba(40,167,69,0.08), rgba(40,167,69,0.02));
    border: 1px solid rgba(40,167,69,0.25);
    border-radius: 16px;
    padding: 20px 22px;
    margin-bottom: 22px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.member-dashboard .md-order-success-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #28a745;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.member-dashboard .md-order-card {
    border: 1px solid var(--dd-border);
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 12px;
    transition: box-shadow 0.2s, border-color 0.2s;
    background: #fff;
    min-width: 0;
}
.member-dashboard .md-order-card.is-highlight {
    border-color: #28a745;
    background: linear-gradient(135deg, rgba(40,167,69,0.06), #fff);
    box-shadow: 0 8px 24px rgba(40,167,69,0.12);
}
.member-dashboard .md-order-card:hover {
    box-shadow: 0 8px 20px rgba(31,20,18,0.06);
}
.member-dashboard .md-order-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 10px;
}
.member-dashboard .md-order-id {
    font-weight: 700;
    font-size: 1rem;
    color: var(--dd-text-main);
}
.member-dashboard .md-order-date {
    font-size: 0.82rem;
    color: var(--dd-text-muted);
}
.member-dashboard .md-order-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}
.member-dashboard .md-order-amount {
    font-size: 1.1rem;
    font-weight: 700;
    color: #28a745;
    flex-shrink: 0;
}
.member-dashboard .md-order-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.member-dashboard .md-order-actions .btn {
    border-radius: 50px;
    font-size: 0.78rem;
}
.member-dashboard .md-manage-panel {
    display: none;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed var(--dd-border);
}
.member-dashboard .md-manage-panel.is-open {
    display: block;
}
.member-dashboard .md-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(31,20,18,0.06);
}
.member-dashboard .md-item-row:last-child {
    border-bottom: none;
}
.member-dashboard .md-item-info {
    flex: 1;
    min-width: 0;
}
.member-dashboard .md-item-title {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--dd-text-main);
}
.member-dashboard .md-item-meta {
    font-size: 0.78rem;
    color: var(--dd-text-muted);
}
.member-dashboard .md-item-price {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--dd-text-main);
    white-space: nowrap;
}
.member-dashboard .md-qty-stepper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f7f4ef;
    border-radius: 50px;
    padding: 4px 6px;
}
.member-dashboard .md-qty-stepper button {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 50%;
    background: #fff;
    color: var(--dd-text-main);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    cursor: pointer;
}
.member-dashboard .md-qty-stepper button:hover {
    background: var(--brand-secondary);
    color: #fff;
}
.member-dashboard .md-qty-stepper button.md-qty-remove {
    color: #dc3545;
}
.member-dashboard .md-qty-stepper button.md-qty-remove:hover {
    background: #dc3545;
    color: #fff;
}
.member-dashboard .md-qty-value {
    min-width: 22px;
    text-align: center;
    font-weight: 700;
    font-size: 0.9rem;
}
.member-dashboard .md-manage-summary {
    margin-top: 12px;
    background: #faf8f4;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 0.85rem;
}
.member-dashboard .md-manage-summary-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 6px;
}
.member-dashboard .md-manage-summary-row:last-child {
    margin-bottom: 0;
    font-weight: 700;
    font-size: 0.95rem;
    color: #28a745;
    padding-top: 6px;
    border-top: 1px solid var(--dd-border);
}
.member-dashboard .md-order-card.is-busy {
    opacity: 0.65;
    pointer-events: none;
}
.member-dashboard .md-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.member-dashboard .md-badge-pending { background: #fff3cd; color: #856404; }
.member-dashboard .md-badge-confirmed { background: #cce5ff; color: #004085; }
.member-dashboard .md-badge-completed { background: #d4edda; color: #155724; }
.member-dashboard .md-badge-canceled { background: #f8d7da; color: #721c24; }
.member-dashboard .md-badge-golden { background: rgba(255,107,53,0.2); color: var(--brand-secondary); }
.member-dashboard .md-badge-member { background: rgba(13,202,240,0.15); color: #0aa2c0; }
.member-dashboard .md-badge-approved { background: #d4edda; color: #155724; }
.member-dashboard .md-badge-rejected { background: #f8d7da; color: #721c24; }
.member-dashboard .md-badge-pending-student { background: #fff3cd; color: #856404; }
.member-dashboard .md-empty-state {
    text-align: center;
    padding: 48px 24px;
}
.member-dashboard .md-empty-state iconify-icon {
    font-size: 3.5rem;
    color: var(--dd-border);
    margin-bottom: 16px;
}
.member-dashboard .md-empty-state h5 {
    color: var(--dd-text-main);
    font-size: 14px;
    margin-bottom: 8px;
}
.member-dashboard .md-empty-state p {
    color: var(--dd-text-muted);
    font-size: 14px;
    margin-bottom: 20px;
}
.member-dashboard .md-empty-state .dd-submit-btn {
    background: var(--brand-secondary);
    border: none;
}
.member-dashboard .md-empty-state .dd-submit-btn::before {
    background: var(--brand-secondary-dark);
}
.member-dashboard .md-empty-state .dd-submit-btn:hover {
    background: var(--brand-secondary-dark);
}
.member-dashboard .md-profile-panel {
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--dd-border);
    padding: 28px;
    box-shadow: 0 20px 50px rgba(31, 20, 18, 0.06);
    margin-bottom: 28px;
    min-width: 0;
}
.member-dashboard .md-profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.member-dashboard .md-profile-grid .md-field-full {
    grid-column: 1 / -1;
}
.member-dashboard .md-field {
    min-width: 0;
}
.member-dashboard .md-field label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--dd-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.member-dashboard .md-field input,
.member-dashboard .md-field textarea {
    width: 100%;
    max-width: 100%;
    border: 1px solid var(--dd-border);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 0.92rem;
    color: var(--dd-text-main);
    background: #fff;
    box-sizing: border-box;
}
.member-dashboard .md-field input[type="file"] {
    padding: 8px;
    font-size: 0.82rem;
}
.member-dashboard .md-field input:disabled,
.member-dashboard .md-field input[readonly] {
    background: #f7f5f2;
    color: var(--dd-text-muted);
    cursor: not-allowed;
}
.member-dashboard .md-field small {
    display: block;
    margin-top: 6px;
    font-size: 0.75rem;
    color: var(--dd-text-muted);
}
.member-dashboard .md-profile-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 18px;
}
.member-dashboard .md-save-btn,
.member-dashboard .md-cancel-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 50px;
    padding: 10px 20px;
    font-size: 0.88rem;
    max-width: 100%;
}
.member-dashboard .md-save-btn {
    background: var(--brand-secondary);
    color: #fff;
    border: none;
    font-weight: 700;
}
.member-dashboard .md-save-btn:hover {
    background: var(--brand-secondary-dark);
}
.member-dashboard .md-cancel-btn {
    background: transparent;
    color: var(--dd-text-main);
    border: 1px solid var(--dd-border);
    font-weight: 600;
    cursor: pointer;
}
.member-dashboard .md-cancel-btn:hover {
    background: #f7f5f2;
}
.member-dashboard .md-avatar-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
}
.member-dashboard .md-avatar {
    width: 72px;
    height: 72px;
    min-width: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--dd-border);
    background: #f7f5f2;
    flex-shrink: 0;
}
.member-dashboard .md-edit-panel {
    max-width: 820px;
    margin: 0 auto 28px;
}

/* Tablet */
@media (max-width: 991px) {
    .member-dashboard .md-stats-row { grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .member-dashboard .md-main-box { margin-top: -60px; }
    .member-dashboard .md-profile-grid { grid-template-columns: 1fr; }
    .member-dashboard .md-stat-card { padding: 14px 10px; }
    .member-dashboard .md-stat-value { font-size: 1.1rem; }
    .member-dashboard .md-stat-label { font-size: 0.68rem; letter-spacing: 0.03em; }
}

/* Mobile */
@media (max-width: 575.98px) {
    .member-dashboard .md-main-box {
        margin-top: -40px;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    .member-dashboard .md-hero-actions {
        gap: 8px;
        margin-top: 18px;
        padding: 0 4px;
    }
    .member-dashboard .md-hero-btn {
        flex: 1 1 calc(50% - 8px);
        min-width: 0;
        padding: 9px 10px;
        font-size: 0.75rem;
        gap: 5px;
        white-space: normal;
        text-align: center;
        line-height: 1.25;
    }
    .member-dashboard .md-hero-btn iconify-icon {
        font-size: 1rem;
        flex-shrink: 0;
    }
    .member-dashboard .md-stats-row {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 18px;
    }
    .member-dashboard .md-stat-card { padding: 16px; }
    .member-dashboard .md-stat-value { font-size: 1.25rem; }
    .member-dashboard .md-stat-label { font-size: 0.74rem; }
    .member-dashboard .md-sidebar,
    .member-dashboard .md-orders-panel,
    .member-dashboard .md-profile-panel {
        padding: 18px 14px;
        border-radius: 16px;
    }
    .member-dashboard .md-card-visual { padding: 16px; margin-bottom: 16px; }
    .member-dashboard .md-card-number { font-size: 0.92rem; letter-spacing: 0.03em; }
    .member-dashboard .md-info-list li {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        padding: 10px 0;
        font-size: 0.85rem;
    }
    .member-dashboard .md-info-list strong,
    .member-dashboard .md-info-list .md-badge {
        text-align: left;
        max-width: 100%;
    }
    .member-dashboard .md-panel-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 16px;
    }
    .member-dashboard .md-panel-header h3 { font-size: 1.15rem; }
    .member-dashboard .md-order-success {
        flex-direction: column;
        padding: 14px;
        gap: 10px;
    }
    .member-dashboard .md-order-success-icon {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
    }
    .member-dashboard .md-order-card { padding: 14px; }
    .member-dashboard .md-order-top {
        flex-direction: column;
        gap: 6px;
    }
    .member-dashboard .md-order-amount { font-size: 1rem; }
    .member-dashboard .md-avatar-row {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
    }
    .member-dashboard .md-avatar {
        width: 64px;
        height: 64px;
        min-width: 64px;
    }
    .member-dashboard .md-avatar-row .md-field {
        width: 100%;
    }
    .member-dashboard .md-profile-actions {
        flex-direction: column-reverse;
        align-items: stretch;
    }
    .member-dashboard .md-save-btn,
    .member-dashboard .md-cancel-btn {
        width: 100%;
        padding: 12px 16px;
    }
    .member-dashboard .md-field input,
    .member-dashboard .md-field textarea {
        font-size: 16px; /* prevents iOS zoom */
        padding: 10px 12px;
    }
    .member-dashboard .md-empty-state { padding: 32px 12px; }
    .member-dashboard .md-empty-state iconify-icon { font-size: 2.6rem; }
    .member-dashboard .md-login-tip { padding: 12px; }
}

/* Very small phones (320px) */
@media (max-width: 374.98px) {
    .member-dashboard .md-main-box {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .member-dashboard .md-hero-btn {
        flex: 1 1 100%;
        padding: 10px 12px;
        font-size: 0.78rem;
    }
    .member-dashboard .md-sidebar,
    .member-dashboard .md-orders-panel,
    .member-dashboard .md-profile-panel {
        padding: 14px 12px;
        border-radius: 14px;
    }
    .member-dashboard .md-card-number { font-size: 0.84rem; }
    .member-dashboard .md-copy-btn {
        width: 100%;
        padding: 8px 10px;
    }
    .member-dashboard .md-badge {
        padding: 3px 8px;
        font-size: 0.66rem;
    }
    .member-dashboard .md-panel-header h3 { font-size: 1.05rem; }
}
</style>
@endpush

@section('frontend_content')
<section class="dd-apply-wrapper member-dashboard">

    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <span class="dd-apply-badge">Member Portal</span>
            <h1 class="dd-apply-headline">Welcome back, {{ explode(' ', $member->name)[0] }}</h1>
            <p class="dd-apply-subhead">Track your orders, view membership perks, and manage your Degchi Dine rewards.</p>

            <div class="md-hero-actions">
                <a href="{{ route('frontend.order.track') }}" class="md-hero-btn md-hero-btn-outline">
                    <iconify-icon icon="solar:delivery-linear"></iconify-icon>
                    Track Order
                </a>
                <a href="{{ route('frontend.completeMenu') }}" class="md-hero-btn md-hero-btn-outline" data-tab="order">
                    <iconify-icon icon="solar:chef-hat-linear"></iconify-icon>
                    Order Food
                </a>
                <a href="{{ route('frontend.contact') }}" class="md-hero-btn md-hero-btn-outline">
                    <iconify-icon icon="solar:phone-calling-linear"></iconify-icon>
                    Contact Us
                </a>
                <button type="button" class="md-hero-btn md-hero-btn-outline border-0" id="mdEditProfileBtn" data-tab="profile">
                    <iconify-icon icon="solar:user-edit-linear"></iconify-icon>
                    Edit Profile
                </button>
                <form method="POST" action="{{ route('frontend.member.logout') }}">
                    @csrf
                    <button type="submit" class="md-hero-btn md-hero-btn-outline border-0">
                        <iconify-icon icon="solar:logout-2-linear"></iconify-icon>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container px-4 px-lg-5 md-main-box">
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 14px;">{{ session('success') }}</div>
        @endif

        {{-- Edit Profile (hidden by default) --}}
        <div id="mdEditView" class="{{ $errors->any() ? '' : 'd-none' }}">
            <div class="md-profile-panel md-edit-panel">
                <div class="md-panel-header">
                    <h3><iconify-icon icon="solar:user-edit-linear" class="me-2" style="color: var(--brand-secondary);"></iconify-icon>Edit Profile</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 mb-3" style="border-radius: 12px;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('frontend.member.profile.update') }}" enctype="multipart/form-data" id="mdProfileForm">
                    @csrf
                    @method('PUT')

                    <div class="md-avatar-row">
                        @if ($member->profile_image_path)
                            <img src="{{ asset('storage/' . $member->profile_image_path) }}" alt="{{ $member->name }}" class="md-avatar">
                        @else
                            <div class="md-avatar d-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:user-linear" style="font-size: 1.8rem; color: var(--dd-text-muted);"></iconify-icon>
                            </div>
                        @endif
                        <div class="md-field flex-grow-1 mb-0">
                            <label for="profile_image">Profile Photo (optional)</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/webp,image/png,image/jpeg">
                        </div>
                    </div>

                    <div class="md-profile-grid">
                        <div class="md-field md-field-full">
                            <label for="unique_card_number">Membership Card Number</label>
                            <input type="text" id="unique_card_number" value="{{ $member->unique_card_number }}" readonly>
                            <small>Card number cannot be changed.</small>
                        </div>
                        <div class="md-field">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" required>
                        </div>
                        <div class="md-field">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $member->phone) }}" required>
                        </div>
                        <div class="md-field">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" required>
                        </div>
                        <div class="md-field">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob', optional($member->dob)->format('Y-m-d')) }}">
                        </div>
                        <div class="md-field">
                            <label for="marriage_date">Marriage Date</label>
                            <input type="date" id="marriage_date" name="marriage_date" value="{{ old('marriage_date', optional($member->marriage_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="md-field md-field-full">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3">{{ old('address', $member->address) }}</textarea>
                        </div>
                    </div>

                    <div class="md-profile-actions">
                        <button type="button" class="md-cancel-btn" id="mdCancelEditBtn">
                            <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                            Cancel
                        </button>
                        <button type="submit" class="md-save-btn">
                            <iconify-icon icon="solar:diskette-linear"></iconify-icon>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Default dashboard --}}
        <div id="mdDefaultView" class="{{ $errors->any() ? 'd-none' : '' }}">
        <div class="row g-4">
            {{-- Sidebar: membership card --}}
            <div class="col-lg-4">
                <div class="md-sidebar">
                    <div class="md-card-visual">
                        <div class="md-card-label">Your Membership Card</div>
                        <div class="md-card-number" id="memberCardNumberDisplay">{{ $member->unique_card_number }}</div>
                        <button type="button" class="md-copy-btn" id="copyCardBtn">
                            <iconify-icon icon="solar:copy-linear" class="me-1"></iconify-icon> Copy Card Number
                        </button>
                    </div>

                    <ul class="md-info-list">
                        <li>
                            <span>Member Name</span>
                            <strong>{{ $member->name }}</strong>
                        </li>
                        <li>
                            <span>Phone</span>
                            <strong>{{ $member->phone }}</strong>
                        </li>
                        <li>
                            <span>Card Type</span>
                            @if ($member->type === 'golden')
                                <span class="md-badge md-badge-golden">Golden</span>
                            @else
                                <span class="md-badge md-badge-member">Membership</span>
                            @endif
                        </li>
                        @if ($member->is_student)
                        <li>
                            <span>Student Status</span>
                            @if ($member->approval_status === 'approved')
                                <span class="md-badge md-badge-approved">Approved</span>
                            @elseif ($member->approval_status === 'rejected')
                                <span class="md-badge md-badge-rejected">Rejected</span>
                            @else
                                <span class="md-badge md-badge-pending-student">Pending</span>
                            @endif
                        </li>
                        @endif
                        <li>
                            <span>Valid Until</span>
                            <strong>{{ $member->expires_at?->format('d M Y') ?? 'N/A' }}</strong>
                        </li>
                    </ul>

                    <div class="md-login-tip">
                        <h6><iconify-icon icon="solar:key-linear" class="me-1"></iconify-icon> Sign in again later</h6>
                        <p>
                            Visit <strong>/member/login</strong> and use your <strong>phone number</strong> or <strong>card number</strong> with the <strong>password</strong> you created when you applied.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main: stats + orders --}}
            <div class="col-lg-8">
                <div class="md-stats-row">
                    <div class="md-stat-card">
                        <div class="md-stat-icon"><iconify-icon icon="solar:bag-check-linear"></iconify-icon></div>
                        <div class="md-stat-value">{{ $orders->total()}}</div>
                        <div class="md-stat-label">Total Orders</div>
                    </div>
                    <div class="md-stat-card">
                        <div class="md-stat-icon"><iconify-icon icon="solar:wallet-money-linear"></iconify-icon></div>
                        <div class="md-stat-value">৳{{ number_format($member->total_purchase, 0) }}</div>
                        <div class="md-stat-label">Total Spent</div>
                    </div>
                    <div class="md-stat-card">
                        <div class="md-stat-icon"><iconify-icon icon="solar:star-linear"></iconify-icon></div>
                        <div class="md-stat-value">
                            @if ($member->type === 'golden')
                                10%
                            @elseif (!$member->first_order_discount_used)
                                {{ $member->is_student ? '35%' : '30%' }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="md-stat-label">
                            @if ($member->type === 'golden')
                                Golden Discount
                            @elseif (!$member->first_order_discount_used)
                                First Order Off
                            @else
                                Next Reward
                            @endif
                        </div>
                    </div>
                </div>

                <div class="md-orders-panel">
                    <div class="md-panel-header">
                        <h3><iconify-icon icon="solar:clipboard-list-linear" class="me-2" style="color: var(--brand-secondary);"></iconify-icon>My Orders</h3>
                        <a href="{{ route('frontend.completeMenu') }}" class="btn btn-sm btn-dark">+ New Order</a>
                    </div>

                    @if ($highlightOrder)
                        <div class="md-order-success">
                            <div class="md-order-success-icon">✓</div>
                            <div>
                                <strong style="font-size: 1.05rem;">Order #{{ $highlightOrder->id }} placed successfully!</strong>
                                <p class="mb-0 mt-1 text-muted" style="font-size: 0.9rem;">
                                    Total ৳{{ number_format($highlightOrder->final_amount, 2) }} ·
                                    {{ strtoupper($highlightOrder->payment_method ?? 'N/A') }} ·
                                    {{ ucfirst($highlightOrder->status) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @forelse ($orders as $order)
                        @php
                            $statusClass = match($order->status) {
                                'completed' => 'md-badge-completed',
                                'confirmed' => 'md-badge-confirmed',
                                'canceled' => 'md-badge-canceled',
                                default => 'md-badge-pending',
                            };
                            $isPending = $order->status === 'pending';
                            $orderItems = $order->normalizedItems();
                            $offerDiscountTotal = round(collect($orderItems)->sum(fn ($i) => (float) ($i['offer_discount'] ?? 0)), 2);
                            $displaySubtotal = round(max(0, (float) $order->total_amount - $offerDiscountTotal), 2);
                        @endphp
                        <div class="md-order-card {{ $highlightOrder && $highlightOrder->id === $order->id ? 'is-highlight' : '' }}"
                             data-order-id="{{ $order->id }}"
                             data-order-status="{{ $order->status }}"
                             @if($isPending)
                             data-cancel-url="{{ route('frontend.member.orders.cancel', $order) }}"
                             data-qty-url="{{ route('frontend.member.orders.items.quantity', $order) }}"
                             @endif>
                            <div class="md-order-top">
                                <div>
                                    <div class="md-order-id">Order #{{ $order->id }}</div>
                                    <div class="md-order-date">{{ $order->created_at->format('d M Y · h:i A') }}</div>
                                </div>
                                <div class="md-order-amount js-order-amount">৳{{ number_format($order->final_amount, 2) }}</div>
                            </div>
                            <div class="md-order-meta">
                                <span class="md-badge {{ $statusClass }} js-order-status-badge">{{ ucfirst($order->status) }}</span>
                                <span class="text-muted" style="font-size: 0.82rem;">
                                    <iconify-icon icon="solar:card-linear" class="me-1"></iconify-icon>
                                    {{ strtoupper($order->payment_method ?? 'N/A') }}
                                </span>
                                <span class="text-success js-order-saved" style="font-size: 0.82rem; {{ (float) $order->discount_amount > 0 ? '' : 'display:none;' }}">
                                    Saved ৳<span class="js-order-saved-val">{{ number_format($order->discount_amount, 2) }}</span>
                                </span>
                            </div>
                            @if ($order->status === 'canceled' && !empty($order->status_remarks))
                                <p class="text-danger mb-2 js-order-remarks" style="font-size: 0.78rem; line-height: 1.45;">
                                    <strong>Remarks:</strong> {{ $order->status_remarks }}
                                </p>
                            @else
                                <p class="text-danger mb-2 js-order-remarks" style="font-size: 0.78rem; line-height: 1.45; display:none;"></p>
                            @endif

                            <div class="md-order-actions">
                                <a href="{{ route('frontend.order.confirmation', $order) }}" class="btn btn-sm btn-outline-dark">
                                    View Order Details
                                </a>
                                @if ($isPending)
                                    <button type="button" class="btn btn-sm btn-dark js-manage-order-btn">
                                        Manage Items
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-cancel-order-btn">
                                        Cancel Order
                                    </button>
                                @endif
                            </div>

                            @if ($isPending)
                                <div class="md-manage-panel js-manage-panel">
                                    <div class="js-manage-items">
                                        @foreach ($orderItems as $index => $item)
                                            @php
                                                $title = $item['title'] ?? $item['name'] ?? 'Item';
                                                $price = (float) ($item['original_price'] ?? $item['price'] ?? 0);
                                                $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
                                                $offerDiscount = (float) ($item['offer_discount'] ?? 0);
                                                $offerPercent = (int) ($item['offer_percent'] ?? 0);
                                                $discountedUnit = $offerPercent > 0 && $qty > 0
                                                    ? max(0, $price - ($offerDiscount / $qty))
                                                    : $price;
                                                $lineSubtotal = max(0, ($price * $qty) - $offerDiscount);
                                            @endphp
                                            <div class="md-item-row" data-item-index="{{ $index }}">
                                                <div class="md-item-info">
                                                    <div class="md-item-title">{{ $title }}</div>
                                                    <div class="md-item-meta">
                                                        @if($offerPercent > 0)
                                                            <span class="text-decoration-line-through text-muted">৳{{ number_format($price, 2) }}</span>
                                                            <span class="text-danger fw-semibold">৳{{ number_format($discountedUnit, 2) }}</span>
                                                            <span class="badge bg-danger-subtle text-danger">{{ $offerPercent }}% OFF</span>
                                                        @else
                                                            ৳{{ number_format($price, 2) }}
                                                        @endif
                                                        @if(!empty($item['note']))
                                                            · {{ $item['note'] }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="md-qty-stepper">
                                                    <button type="button" class="js-qty-minus" aria-label="Decrease">−</button>
                                                    <span class="md-qty-value">{{ $qty }}</span>
                                                    <button type="button" class="js-qty-plus" aria-label="Increase">+</button>
                                                    <button type="button" class="md-qty-remove js-qty-remove" aria-label="Remove" title="Remove item">×</button>
                                                </div>
                                                <div class="md-item-price js-line-subtotal">৳{{ number_format($lineSubtotal, 2) }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="md-manage-summary js-manage-summary">
                                        <div class="md-manage-summary-row">
                                            <span>Subtotal</span>
                                            <span class="js-sum-subtotal">৳{{ number_format($displaySubtotal, 2) }}</span>
                                        </div>
                                        <div class="md-manage-summary-row js-sum-discount-row" style="{{ (float) $order->discount_amount - $offerDiscountTotal > 0.004 || $offerDiscountTotal > 0.004 ? '' : 'display:none;' }}">
                                            <span>Discounts</span>
                                            <span class="text-success js-sum-discount">- ৳{{ number_format((float) $order->discount_amount, 2) }}</span>
                                        </div>
                                        <div class="md-manage-summary-row">
                                            <span>Delivery</span>
                                            <span class="js-sum-delivery">৳{{ number_format((float) ($order->delivery_charge ?? 0), 2) }}</span>
                                        </div>
                                        <div class="md-manage-summary-row">
                                            <span>Total</span>
                                            <span class="js-sum-total">৳{{ number_format($order->final_amount, 2) }}</span>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">
                                        You can change quantity or remove items while this order is still pending. Delivery charge stays fixed.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="md-empty-state">
                            <iconify-icon icon="solar:bag-cross-linear"></iconify-icon>
                            <h5>No orders yet</h5>
                            <p>Your order history will appear here once you place your first order.</p>
                            <a href="{{ route('frontend.completeMenu') }}" class="dd-submit-btn" style="max-width: 220px; margin: 0 auto; text-decoration: none;">
                                <span>Browse Menu</span>
                                <iconify-icon icon="solar:arrow-right-linear" class="dd-btn-icon"></iconify-icon>
                            </a>
                        </div>
                    @endforelse

                    @if ($orders->hasPages())
                        <div class="mt-3">{{ $orders->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </div>
</section>
@endsection

@push('front_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var copyBtn = document.getElementById('copyCardBtn');
    var cardDisplay = document.getElementById('memberCardNumberDisplay');
    var defaultView = document.getElementById('mdDefaultView');
    var editView = document.getElementById('mdEditView');
    var editBtn = document.getElementById('mdEditProfileBtn');
    var cancelBtn = document.getElementById('mdCancelEditBtn');
    var profileForm = document.getElementById('mdProfileForm');
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    var heroBtns = document.querySelectorAll('.md-hero-actions .md-hero-btn[data-tab]');

    function setActiveTab(tab) {
        heroBtns.forEach(function(btn) {
            if (btn.getAttribute('data-tab') === tab) {
                btn.classList.add('is-active');
            } else {
                btn.classList.remove('is-active');
            }
        });
    }

    function showDefaultDashboard() {
        if (defaultView) defaultView.classList.remove('d-none');
        if (editView) editView.classList.add('d-none');
        if (profileForm) profileForm.reset();
        setActiveTab('order');
    }

    function showEditProfile() {
        if (defaultView) defaultView.classList.add('d-none');
        if (editView) editView.classList.remove('d-none');
        if (editView) editView.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setActiveTab('profile');
    }

    if (editBtn) editBtn.addEventListener('click', showEditProfile);
    if (cancelBtn) cancelBtn.addEventListener('click', showDefaultDashboard);

    setActiveTab('order');

    if (copyBtn && cardDisplay) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(cardDisplay.textContent.trim()).then(function () {
                copyBtn.innerHTML = '<iconify-icon icon="solar:check-circle-linear" class="me-1"></iconify-icon> Copied!';
                setTimeout(function () {
                    copyBtn.innerHTML = '<iconify-icon icon="solar:copy-linear" class="me-1"></iconify-icon> Copy Card Number';
                }, 2000);
            });
        });
    }

    function money(value) {
        return '৳' + (Number(value) || 0).toFixed(2);
    }

    function notify(type, message) {
        if (!message) return;
        if (typeof toastr !== 'undefined') {
            toastr[type === 'error' ? 'error' : 'success'](message);
        } else if (type === 'error') {
            alert(message);
        } else {
            alert(message);
        }
    }

    function escapeHtml(text) {
        var d = document.createElement('div');
        d.textContent = text == null ? '' : String(text);
        return d.innerHTML;
    }

    function setCardBusy(card, busy) {
        card.classList.toggle('is-busy', !!busy);
    }

    function markCanceled(card, order, message) {
        card.dataset.orderStatus = 'canceled';
        var badge = card.querySelector('.js-order-status-badge');
        if (badge) {
            badge.className = 'md-badge md-badge-canceled js-order-status-badge';
            badge.textContent = 'Canceled';
        }
        var amount = card.querySelector('.js-order-amount');
        if (amount && order) amount.textContent = money(order.final_amount);

        var remarks = card.querySelector('.js-order-remarks');
        if (remarks && order && order.status_remarks) {
            remarks.style.display = '';
            remarks.innerHTML = '<strong>Remarks:</strong> ' + escapeHtml(order.status_remarks);
        }

        card.querySelectorAll('.js-manage-order-btn, .js-cancel-order-btn').forEach(function (btn) {
            btn.remove();
        });
        var panel = card.querySelector('.js-manage-panel');
        if (panel) panel.remove();
        notify('success', message || 'Order canceled.');
    }

    function renderManagePanel(card, order) {
        var itemsWrap = card.querySelector('.js-manage-items');
        var amountEl = card.querySelector('.js-order-amount');
        var savedWrap = card.querySelector('.js-order-saved');
        var savedVal = card.querySelector('.js-order-saved-val');

        if (amountEl) amountEl.textContent = money(order.final_amount);
        if (savedWrap && savedVal) {
            if ((order.discount_amount || 0) > 0.004) {
                savedWrap.style.display = '';
                savedVal.textContent = Number(order.discount_amount).toFixed(2);
            } else {
                savedWrap.style.display = 'none';
            }
        }

        if (itemsWrap) {
            itemsWrap.innerHTML = (order.items || []).map(function (item) {
                var priceHtml = item.offer_percent > 0
                    ? '<span class="text-decoration-line-through text-muted">' + money(item.price) + '</span> ' +
                      '<span class="text-danger fw-semibold">' + money(item.discounted_unit) + '</span> ' +
                      '<span class="badge bg-danger-subtle text-danger">' + item.offer_percent + '% OFF</span>'
                    : money(item.price);
                var noteHtml = item.note ? ' · ' + escapeHtml(item.note) : '';

                return '<div class="md-item-row" data-item-index="' + item.index + '">' +
                    '<div class="md-item-info"><div class="md-item-title">' + escapeHtml(item.title) + '</div>' +
                    '<div class="md-item-meta">' + priceHtml + noteHtml + '</div></div>' +
                    '<div class="md-qty-stepper">' +
                    '<button type="button" class="js-qty-minus" aria-label="Decrease">−</button>' +
                    '<span class="md-qty-value">' + item.quantity + '</span>' +
                    '<button type="button" class="js-qty-plus" aria-label="Increase">+</button>' +
                    '<button type="button" class="md-qty-remove js-qty-remove" aria-label="Remove" title="Remove item">×</button>' +
                    '</div><div class="md-item-price js-line-subtotal">' + money(item.line_subtotal) + '</div></div>';
            }).join('');
        }

        var subtotal = card.querySelector('.js-sum-subtotal');
        var discount = card.querySelector('.js-sum-discount');
        var discountRow = card.querySelector('.js-sum-discount-row');
        var delivery = card.querySelector('.js-sum-delivery');
        var total = card.querySelector('.js-sum-total');
        if (subtotal) subtotal.textContent = money(order.display_subtotal);
        if (discount) discount.textContent = '- ' + money(order.discount_amount);
        if (discountRow) discountRow.style.display = (order.discount_amount || 0) > 0.004 ? '' : 'none';
        if (delivery) delivery.textContent = money(order.delivery_charge);
        if (total) total.textContent = money(order.final_amount);
    }

    function postQuantity(card, index, quantity) {
        var url = card.dataset.qtyUrl;
        if (!url) return;
        setCardBusy(card, true);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ index: index, quantity: quantity })
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, data: data };
            });
        })
        .then(function (result) {
            if (!result.ok || !result.data.success) {
                notify('error', (result.data && result.data.message) || 'Unable to update item.');
                setCardBusy(card, false);
                return;
            }
            if (result.data.canceled) {
                markCanceled(card, result.data.order, result.data.message);
                return;
            }
            renderManagePanel(card, result.data.order);
            notify('success', result.data.message);
            setCardBusy(card, false);
        })
        .catch(function () {
            notify('error', 'Unable to update item. Please try again.');
            setCardBusy(card, false);
        });
    }

    document.addEventListener('click', function (e) {
        var manageBtn = e.target.closest('.js-manage-order-btn');
        if (manageBtn) {
            var card = manageBtn.closest('.md-order-card');
            var panel = card && card.querySelector('.js-manage-panel');
            if (panel) {
                panel.classList.toggle('is-open');
                manageBtn.textContent = panel.classList.contains('is-open') ? 'Hide Items' : 'Manage Items';
            }
            return;
        }

        var cancelBtnEl = e.target.closest('.js-cancel-order-btn');
        if (cancelBtnEl) {
            var cancelCard = cancelBtnEl.closest('.md-order-card');
            var cancelUrl = cancelCard && cancelCard.dataset.cancelUrl;
            if (!cancelUrl) return;
            if (!confirm('Cancel this pending order? This cannot be undone.')) return;

            setCardBusy(cancelCard, true);
            fetch(cancelUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    notify('error', (result.data && result.data.message) || 'Unable to cancel order.');
                    setCardBusy(cancelCard, false);
                    return;
                }
                markCanceled(cancelCard, result.data.order, result.data.message);
            })
            .catch(function () {
                notify('error', 'Unable to cancel order. Please try again.');
                setCardBusy(cancelCard, false);
            });
            return;
        }

        var plus = e.target.closest('.js-qty-plus');
        var minus = e.target.closest('.js-qty-minus');
        var remove = e.target.closest('.js-qty-remove');
        if (!plus && !minus && !remove) return;

        var row = e.target.closest('.md-item-row');
        var qtyCard = e.target.closest('.md-order-card');
        if (!row || !qtyCard) return;

        var index = parseInt(row.dataset.itemIndex, 10);
        var currentQty = parseInt((row.querySelector('.md-qty-value') || {}).textContent || '1', 10) || 1;

        if (remove) {
            if (!confirm('Remove this item from the order?')) return;
            postQuantity(qtyCard, index, 0);
            return;
        }

        var nextQty = plus ? currentQty + 1 : currentQty - 1;
        if (nextQty <= 0) {
            if (!confirm('Quantity would drop to 0 — remove this item?')) return;
            nextQty = 0;
        }
        postQuantity(qtyCard, index, nextQty);
    });
});
</script>
@endpush
