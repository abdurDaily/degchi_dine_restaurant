<style>
    .permissions-page .permission-accordion-card {
        border: 1px solid rgba(17, 107, 131, 0.12);
        box-shadow: 0 8px 24px rgba(15, 40, 50, 0.04);
        overflow: hidden;
    }
    .permissions-page .permission-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        cursor: pointer;
        background: #f7fbfc;
        user-select: none;
    }
    .permissions-page .permission-accordion-header[aria-expanded="true"] {
        background: rgba(17, 107, 131, 0.08);
    }
    .permissions-page .permission-accordion-header[aria-expanded="true"] .permission-accordion-chevron {
        transform: rotate(180deg);
    }
    .permissions-page .permission-accordion-chevron {
        transition: transform 0.2s ease;
        color: #116b83;
    }
    .permissions-page .permission-section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(17, 107, 131, 0.1);
        color: #116b83;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .permissions-page .permission-group-card {
        border: 1px solid rgba(17, 107, 131, 0.1);
        box-shadow: none;
    }
    .permissions-page .permission-table th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .permissions-page .permission-table td {
        font-size: 0.9rem;
    }
    .permissions-page .sticky-update-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 5;
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }
</style>
