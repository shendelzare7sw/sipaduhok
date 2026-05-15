<style>
    .catatan-page {
        width: 100%;
        max-width: none;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .catatan-toolbar,
    .catatan-panel,
    .catatan-note-card,
    .catatan-form-card,
    .catatan-detail-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .catatan-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        margin-bottom: 18px;
    }

    .catatan-toolbar-title {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .catatan-toolbar-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: linear-gradient(135deg, #4361ee 0%, #06b6d4 100%);
        flex: 0 0 auto;
    }

    .catatan-toolbar h5,
    .catatan-panel h5,
    .catatan-form-card h5,
    .catatan-detail-card h5 {
        margin: 0;
        color: #1e293b;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.3;
    }

    .catatan-toolbar p {
        margin: 3px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.4;
    }

    .catatan-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .catatan-stat {
        position: relative;
        overflow: hidden;
        min-height: 112px;
        padding: 18px;
        border-radius: 12px;
        color: #ffffff;
        box-shadow: 0 12px 26px rgba(67, 97, 238, 0.15);
    }

    .catatan-stat.primary { background: linear-gradient(135deg, #4361ee 0%, #3b82f6 100%); }
    .catatan-stat.success { background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); }
    .catatan-stat.warning { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
    .catatan-stat.purple { background: linear-gradient(135deg, #8b5cf6 0%, #4361ee 100%); }

    .catatan-stat span {
        display: block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        opacity: .9;
    }

    .catatan-stat strong {
        display: block;
        margin-top: 8px;
        font-size: 30px;
        font-weight: 900;
        line-height: 1;
    }

    .catatan-stat small {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        opacity: .86;
    }

    .catatan-stat i {
        position: absolute;
        right: 16px;
        bottom: 12px;
        font-size: 46px;
        opacity: .18;
    }

    .catatan-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 14px;
        border-radius: 10px;
        border: 1px solid transparent;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        white-space: nowrap;
        cursor: pointer;
    }

    .catatan-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .catatan-btn.primary {
        background: #4361ee;
        color: #ffffff;
        box-shadow: 0 10px 18px rgba(67, 97, 238, .22);
    }

    .catatan-btn.primary:hover { color: #ffffff; background: #3651d4; }

    .catatan-btn.secondary {
        background: #f8fafc;
        color: #334155;
        border-color: #dbe4f0;
    }

    .catatan-btn.secondary:hover { color: #1e293b; background: #eef2f7; }

    .catatan-btn.danger {
        background: #fff7f7;
        color: #ef4444;
        border-color: #fecaca;
    }

    .catatan-btn.danger:hover { color: #dc2626; background: #fee2e2; }

    .catatan-panel {
        overflow: hidden;
    }

    .catatan-panel-header,
    .catatan-form-header,
    .catatan-detail-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .catatan-panel-body,
    .catatan-form-body,
    .catatan-detail-body {
        padding: 20px;
    }

    .catatan-list {
        display: grid;
        gap: 14px;
    }

    .catatan-note-card {
        position: relative;
        padding: 18px;
        border-left: 5px solid #4361ee;
    }

    .catatan-note-card.priority-biasa { border-left-color: #4361ee; }
    .catatan-note-card.priority-penting { border-left-color: #f59e0b; }
    .catatan-note-card.priority-mendesak { border-left-color: #ef4444; }

    .catatan-note-top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .catatan-note-title {
        min-width: 0;
    }

    .catatan-note-title h6 {
        margin: 0;
        color: #1e293b;
        font-size: 17px;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .catatan-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 12px;
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.35;
    }

    .catatan-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 100%;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        line-height: 1.2;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }

    .catatan-badge.biasa,
    .catatan-badge.semua {
        color: #3651d4;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
    }

    .catatan-badge.penting {
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fde68a;
    }

    .catatan-badge.mendesak {
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .catatan-badge.role {
        color: #047857;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }

    .catatan-badge.individu {
        color: #7c3aed;
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
    }

    .catatan-excerpt,
    .catatan-message-box {
        color: #334155;
        font-size: 14px;
        line-height: 1.65;
        overflow-wrap: anywhere;
    }

    .catatan-note-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
    }

    .catatan-read-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 7px 11px;
        border: 1px solid #d1fae5;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.25;
    }

    .catatan-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .catatan-empty {
        padding: 48px 20px;
        text-align: center;
        color: #64748b;
    }

    .catatan-empty i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin-bottom: 16px;
        border-radius: 18px;
        color: #4361ee;
        background: #eef2ff;
        font-size: 26px;
    }

    .catatan-empty h5 {
        margin-bottom: 8px;
        color: #1e293b;
        font-weight: 900;
    }

    .catatan-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 16px;
        padding: 13px 15px;
        border-radius: 12px;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        background: #eff6ff;
        font-size: 13px;
        line-height: 1.45;
    }

    .catatan-alert.success {
        border-color: #bbf7d0;
        color: #047857;
        background: #ecfdf5;
    }

    .catatan-form-card,
    .catatan-detail-card {
        overflow: hidden;
    }

    .catatan-form-grid,
    .catatan-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .catatan-field {
        margin-bottom: 18px;
    }

    .catatan-field label,
    .catatan-detail-label {
        display: block;
        margin-bottom: 7px;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .catatan-required {
        color: #ef4444;
    }

    .catatan-input,
    .catatan-select,
    .catatan-textarea {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #dbe4f0;
        border-radius: 10px;
        color: #1e293b;
        background: #ffffff;
        font-size: 14px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .catatan-input:focus,
    .catatan-select:focus,
    .catatan-textarea:focus {
        outline: none;
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, .12);
    }

    .catatan-textarea {
        min-height: 160px;
        resize: vertical;
        line-height: 1.6;
    }

    .catatan-help {
        margin-top: 7px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
    }

    .catatan-invalid {
        display: block;
        margin-top: 7px;
        color: #ef4444;
        font-size: 12px;
        font-weight: 700;
    }

    .catatan-recipient-grid,
    .catatan-priority-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .catatan-option {
        position: relative;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        min-height: 82px;
        padding: 13px;
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
    }

    .catatan-option:hover {
        border-color: #b7c4f8;
        background: #ffffff;
    }

    .catatan-option input {
        margin-top: 3px;
        accent-color: #4361ee;
        flex: 0 0 auto;
    }

    .catatan-option strong {
        display: block;
        color: #1e293b;
        font-size: 13px;
        line-height: 1.35;
    }

    .catatan-option span {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .catatan-option:has(input:checked) {
        border-color: #4361ee;
        background: #eef2ff;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, .08);
    }

    .catatan-conditional {
        margin-top: 14px;
        padding: 14px;
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .catatan-hidden {
        display: none !important;
    }

    .catatan-filter-panel {
        overflow: hidden;
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .catatan-filter-row {
        display: grid;
        grid-template-columns: minmax(140px, 180px) minmax(140px, 180px) minmax(200px, 1fr);
        gap: 10px;
        padding: 13px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .catatan-filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 13px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .catatan-count-chip {
        margin-left: auto;
        padding: 6px 10px;
        border-radius: 999px;
        color: #3651d4;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .catatan-user-list {
        max-height: 300px;
        overflow: auto;
        padding: 8px;
    }

    .catatan-user-item {
        display: grid;
        grid-template-columns: 18px minmax(0, 1fr);
        gap: 10px;
        align-items: flex-start;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
    }

    .catatan-user-item:hover {
        background: #f8fafc;
    }

    .catatan-user-item input {
        margin-top: 3px;
        accent-color: #4361ee;
    }

    .catatan-user-name {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        color: #1e293b;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .catatan-user-meta {
        margin-top: 3px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .catatan-mini-badge {
        display: inline-flex;
        padding: 3px 7px;
        border-radius: 999px;
        color: #3651d4;
        background: #eef2ff;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .catatan-form-actions {
        display: flex;
        gap: 10px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .catatan-detail-title {
        color: #1e293b;
        font-size: 24px;
        font-weight: 900;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .catatan-detail-value {
        color: #1e293b;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .catatan-message-box {
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        white-space: pre-wrap;
    }

    .catatan-reader-list {
        display: grid;
        gap: 10px;
        max-height: 420px;
        overflow: auto;
    }

    .catatan-reader {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .catatan-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: linear-gradient(135deg, #4361ee 0%, #8b5cf6 100%);
        font-weight: 900;
        flex: 0 0 auto;
    }

    .catatan-reader-name {
        color: #1e293b;
        font-size: 14px;
        font-weight: 900;
        overflow-wrap: anywhere;
    }

    .catatan-reader-time {
        margin-top: 2px;
        color: #64748b;
        font-size: 12px;
    }

    .catatan-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 22px 0 12px;
        color: #1e293b;
        font-size: 14px;
        font-weight: 900;
    }

    @media (max-width: 991.98px) {
        .catatan-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .catatan-form-grid,
        .catatan-detail-grid,
        .catatan-recipient-grid,
        .catatan-priority-grid {
            grid-template-columns: 1fr;
        }

        .catatan-filter-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .catatan-page {
            padding: 0 .75rem;
        }

        .catatan-toolbar,
        .catatan-panel-header,
        .catatan-form-header,
        .catatan-detail-header,
        .catatan-note-top,
        .catatan-note-footer,
        .catatan-form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .catatan-toolbar-title {
            align-items: flex-start;
        }

        .catatan-stats {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .catatan-stat {
            min-height: 92px;
            padding: 15px;
        }

        .catatan-stat strong {
            font-size: 24px;
        }

        .catatan-panel-body,
        .catatan-form-body,
        .catatan-detail-body {
            padding: 14px;
        }

        .catatan-note-card {
            padding: 14px;
        }

        .catatan-actions,
        .catatan-actions .catatan-btn,
        .catatan-toolbar .catatan-btn,
        .catatan-form-actions .catatan-btn {
            width: 100%;
        }

        .catatan-actions {
            justify-content: stretch;
        }

        .catatan-filter-actions {
            flex-wrap: wrap;
        }

        .catatan-count-chip {
            width: 100%;
            margin-left: 0;
            text-align: center;
        }

        .catatan-reader {
            grid-template-columns: 38px minmax(0, 1fr);
        }

        .catatan-reader .catatan-badge {
            grid-column: 1 / -1;
            justify-content: center;
        }
    }
</style>
