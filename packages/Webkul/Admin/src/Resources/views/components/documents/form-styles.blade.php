<style>
    .document-form-toolbar {
        border: 1px solid #d8e0ea !important;
        border-radius: 18px !important;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
        padding: 16px 18px !important;
    }

    .document-form-panel {
        border: 1px solid #d8e0ea !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
        padding: 20px !important;
    }

    .document-form-panel .custom-input,
    .document-form-panel .custom-select {
        border-color: #cfd8e3;
        min-height: 42px;
    }

    .document-form-panel .secondary-button,
    .document-form-panel .primary-button {
        border-radius: 999px;
    }

    .document-form-subtitle {
        color: #6b7280;
        font-size: 13px;
    }

    .document-form-section {
        border-top: 1px solid #e5edf5;
        padding-top: 18px;
    }

    .document-form-section-title {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .document-form-section-note {
        color: #64748b;
        font-size: 12px;
    }

    .document-form-items {
        border-top: 1px solid #e5edf5;
        padding-top: 18px;
    }

    .document-form-summary-box {
        border: 1px solid #d8e0ea;
        border-radius: 16px;
        background: #f8fbff !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .7);
    }

    .document-form-mini-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    .document-form-mini-grid .span-2 { grid-column: span 2 / span 2; }
    .document-form-mini-grid .span-3 { grid-column: span 3 / span 3; }
    .document-form-mini-grid .span-4 { grid-column: span 4 / span 4; }
    .document-form-mini-grid .span-6 { grid-column: span 6 / span 6; }
    .document-form-mini-grid .span-12 { grid-column: span 12 / span 12; }

    @media (max-width: 768px) {
        .document-form-mini-grid {
            grid-template-columns: 1fr;
        }

        .document-form-mini-grid .span-2,
        .document-form-mini-grid .span-3,
        .document-form-mini-grid .span-4,
        .document-form-mini-grid .span-6,
        .document-form-mini-grid .span-12 {
            grid-column: auto;
        }
    }
</style>

