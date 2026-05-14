<style>
.monitoring-page {
    --primary: #4361ee;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --purple: #8b5cf6;
    --ink: #1f2937;
    --muted: #64748b;
    --line: #e2e8f0;
    --soft: #f8fafc;
}

.monitoring-page .page-panel,
.monitoring-page .summary-card,
.monitoring-page .content-card,
.monitoring-page .info-panel {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
}

.monitoring-page .page-panel {
    align-items: center;
    display: flex;
    gap: 16px;
    justify-content: space-between;
    margin-bottom: 18px;
    padding: 20px;
}

.monitoring-page .panel-kicker {
    color: var(--primary);
    display: block;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .04em;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.monitoring-page .panel-title {
    color: var(--ink);
    font-weight: 800;
    margin-bottom: 4px;
}

.monitoring-page .panel-subtitle,
.monitoring-page .summary-card span,
.monitoring-page .meta-text {
    color: var(--muted);
}

.monitoring-page .scope-pill {
    align-items: center;
    background: rgba(67, 97, 238, .1);
    border-radius: 999px;
    color: var(--primary);
    display: inline-flex;
    font-size: 12px;
    font-weight: 800;
    gap: 8px;
    padding: 9px 12px;
    white-space: nowrap;
}

.monitoring-page .summary-card {
    height: 100%;
    padding: 16px;
}

.monitoring-page .summary-icon,
.monitoring-page .info-icon {
    align-items: center;
    border-radius: 10px;
    display: inline-flex;
    height: 36px;
    justify-content: center;
    width: 36px;
}

.monitoring-page .summary-icon {
    margin-bottom: 14px;
}

.monitoring-page .summary-icon.primary,
.monitoring-page .info-icon.primary { background: rgba(67, 97, 238, .12); color: var(--primary); }
.monitoring-page .summary-icon.success,
.monitoring-page .info-icon.success { background: rgba(16, 185, 129, .12); color: var(--success); }
.monitoring-page .summary-icon.warning,
.monitoring-page .info-icon.warning { background: rgba(245, 158, 11, .14); color: var(--warning); }
.monitoring-page .summary-icon.danger,
.monitoring-page .info-icon.danger { background: rgba(239, 68, 68, .12); color: var(--danger); }
.monitoring-page .summary-icon.info,
.monitoring-page .info-icon.info { background: rgba(6, 182, 212, .12); color: var(--info); }
.monitoring-page .summary-icon.purple,
.monitoring-page .info-icon.purple { background: rgba(139, 92, 246, .12); color: var(--purple); }

.monitoring-page .summary-card span {
    display: block;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .03em;
    text-transform: uppercase;
}

.monitoring-page .summary-card strong {
    color: var(--ink);
    display: block;
    font-size: 22px;
    line-height: 1.2;
    margin-top: 5px;
    word-break: break-word;
}

.monitoring-page .content-card {
    margin-bottom: 20px;
    overflow: hidden;
}

.monitoring-page .content-card-header {
    align-items: center;
    border-bottom: 1px solid var(--line);
    display: flex;
    gap: 14px;
    justify-content: space-between;
    padding: 18px 20px;
}

.monitoring-page .content-card-header h5 {
    color: var(--ink);
    font-weight: 800;
}

.monitoring-page .content-card-body {
    padding: 20px;
}

.monitoring-page .filter-toolbar {
    align-items: center;
    background: var(--soft);
    border: 1px solid #eef2f7;
    border-radius: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px;
}

.monitoring-page .filter-toolbar .form-control,
.monitoring-page .filter-toolbar .form-select {
    min-width: 180px;
}

.monitoring-page .table-clean {
    margin-bottom: 0;
}

.monitoring-page .table-clean thead th {
    background: var(--soft);
    border-bottom: 1px solid var(--line);
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .03em;
    padding: 14px 16px;
    text-transform: uppercase;
}

.monitoring-page .table-clean tbody td {
    border-bottom: 1px solid #eef2f7;
    color: #334155;
    padding: 15px 16px;
    vertical-align: middle;
}

.monitoring-page .entity-title {
    color: var(--ink);
    display: inline-block;
    font-weight: 800;
    max-width: 100%;
    overflow-wrap: anywhere;
    word-break: normal;
}

.monitoring-page .entity-subtitle {
    color: var(--muted);
    display: block;
    font-size: 12px;
    margin-top: 2px;
    max-width: 100%;
    overflow-wrap: anywhere;
}

.monitoring-page .soft-badge,
.monitoring-page .status-pill {
    align-items: center;
    border-radius: 999px;
    display: inline-flex;
    font-size: 12px;
    gap: 4px;
    font-weight: 800;
    line-height: 1.2;
    max-width: 100%;
    padding: 8px 10px;
    white-space: normal;
}

.monitoring-page .soft-badge.primary { background: rgba(67, 97, 238, .11); color: var(--primary); }
.monitoring-page .soft-badge.success { background: rgba(16, 185, 129, .12); color: var(--success); }
.monitoring-page .soft-badge.warning { background: rgba(245, 158, 11, .14); color: #b45309; }
.monitoring-page .soft-badge.danger { background: rgba(239, 68, 68, .12); color: var(--danger); }
.monitoring-page .soft-badge.neutral { background: #eef2ff; color: var(--primary); }

.monitoring-page .assignment-list {
    max-width: 360px;
    min-width: 0;
}

.monitoring-page .assignment-list .soft-badge,
.monitoring-page .assignment-badge {
    justify-content: flex-start;
    max-width: 100%;
    min-width: 0;
    overflow-wrap: anywhere;
    text-align: left;
}

.monitoring-page .mobile-details {
    width: 100%;
}

.monitoring-page .mobile-row-details {
    display: none;
}

.monitoring-page .mobile-details summary {
    display: none;
}

.monitoring-page .mobile-details summary::-webkit-details-marker {
    display: none;
}

.monitoring-page .mobile-details:not([open]) > .mobile-details-body {
    display: block;
}

.monitoring-page .mobile-summary-main {
    color: var(--ink);
    font-weight: 800;
    min-width: 0;
    overflow-wrap: anywhere;
}

.monitoring-page .mobile-summary-meta {
    color: var(--muted);
    display: block;
    font-size: 12px;
    margin-top: 2px;
    overflow-wrap: anywhere;
}

.monitoring-page .mobile-summary-link {
    color: var(--primary);
    flex: 0 0 auto;
    font-size: 12px;
    font-weight: 800;
}

.monitoring-page .mobile-detail-section {
    border-top: 1px solid #eef2f7;
    padding: 12px 0;
}

.monitoring-page .mobile-detail-section:first-child {
    border-top: 0;
    padding-top: 0;
}

.monitoring-page .mobile-detail-section:last-child {
    padding-bottom: 0;
}

.monitoring-page .mobile-detail-title {
    color: var(--muted);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .03em;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.monitoring-page .metric-grid {
    display: grid;
    gap: 8px;
    grid-template-columns: repeat(2, minmax(112px, 1fr));
    min-width: 210px;
}

.monitoring-page .metric-chip {
    background: var(--soft);
    border: 1px solid #eef2f7;
    border-radius: 10px;
    min-width: 0;
    padding: 9px 10px;
}

.monitoring-page .metric-chip span {
    color: var(--muted);
    display: block;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.monitoring-page .metric-chip strong {
    color: var(--ink);
    display: block;
    font-size: 17px;
    line-height: 1.25;
    overflow-wrap: anywhere;
    word-break: normal;
}

.monitoring-page .money-chip strong {
    font-size: 16px;
    white-space: nowrap;
}

.monitoring-page .progress-wrap {
    min-width: 170px;
}

.monitoring-page .progress-track {
    background: #e5e7eb;
    border-radius: 999px;
    height: 10px;
    overflow: hidden;
}

.monitoring-page .progress-fill {
    border-radius: inherit;
    height: 100%;
}

.monitoring-page .progress-fill.high { background: var(--success); }
.monitoring-page .progress-fill.medium { background: var(--warning); }
.monitoring-page .progress-fill.low { background: var(--danger); }
.monitoring-page .progress-caption {
    color: var(--muted);
    display: flex;
    flex-wrap: wrap;
    font-size: 12px;
    gap: 6px;
    justify-content: space-between;
    margin-top: 6px;
}

.monitoring-page .progress-caption span {
    overflow-wrap: anywhere;
}

.monitoring-page .money-value {
    font-weight: 800;
    white-space: nowrap;
}

.monitoring-page .money-value.success { color: var(--success); }
.monitoring-page .money-value.danger { color: var(--danger); }

.monitoring-page .empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 42px 20px;
    text-align: center;
}

.monitoring-page .empty-state i {
    color: #94a3b8;
    font-size: 42px;
    margin-bottom: 12px;
}

.monitoring-page .empty-state h6 {
    color: var(--ink);
    font-weight: 800;
}

.monitoring-page .empty-state p {
    color: var(--muted);
    margin-bottom: 0;
}

.monitoring-page .info-panel {
    align-items: flex-start;
    display: flex;
    gap: 12px;
    padding: 16px;
}

.monitoring-page .info-panel h6 {
    color: var(--ink);
    font-weight: 800;
    margin-bottom: 4px;
}

.monitoring-page .info-panel p {
    color: var(--muted);
    margin-bottom: 0;
}

@media (max-width: 767.98px) {
    .monitoring-page .page-panel,
    .monitoring-page .content-card-header {
        align-items: stretch;
        flex-direction: column;
    }

    .monitoring-page .scope-pill {
        justify-content: center;
        width: 100%;
    }

    .monitoring-page .filter-toolbar {
        align-items: stretch;
        flex-direction: column;
        width: 100%;
    }

    .monitoring-page .filter-toolbar .form-control,
    .monitoring-page .filter-toolbar .form-select,
    .monitoring-page .filter-toolbar .btn {
        min-width: 0;
        width: 100%;
    }

    .monitoring-page .content-card-body {
        padding: 14px;
    }

    .monitoring-page .table-responsive {
        overflow-x: visible;
    }

    .monitoring-page .table-clean thead {
        display: none;
    }

    .monitoring-page .table-clean tbody tr {
        border: 1px solid var(--line);
        border-radius: 12px;
        display: block;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .monitoring-page .table-clean tbody td {
        align-items: flex-start;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        gap: 14px;
        justify-content: space-between;
        padding: 12px 14px;
        text-align: right !important;
        white-space: normal;
    }

    .monitoring-page .table-clean tbody td > * {
        min-width: 0;
    }

    .monitoring-page .table-clean tbody td::before {
        color: var(--muted);
        content: attr(data-label);
        flex: 0 0 110px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .03em;
        text-align: left;
        text-transform: uppercase;
    }

    .monitoring-page .table-clean tbody td:last-child {
        border-bottom: 0;
    }

    .monitoring-page .table-clean tbody td.complex-cell {
        display: block;
        text-align: left !important;
    }

    .monitoring-page .table-clean tbody td.complex-cell::before {
        display: block;
        margin-bottom: 10px;
    }

    .monitoring-page .table-clean tbody td.complex-cell > * {
        max-width: 100%;
    }

    .monitoring-page .table-clean tbody td.desktop-detail-cell {
        display: none !important;
    }

    .monitoring-page .table-clean tbody td.mobile-primary-cell {
        display: block;
        text-align: left !important;
    }

    .monitoring-page .table-clean tbody td.mobile-primary-cell::before {
        display: block;
        margin-bottom: 10px;
    }

    .monitoring-page .mobile-row-details {
        display: block;
        margin-top: 12px;
        width: 100%;
    }

    .monitoring-page .mobile-row-details summary {
        align-items: flex-start;
        background: var(--soft);
        border: 1px solid #eef2f7;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 10px 12px;
        user-select: none;
        width: 100%;
    }

    .monitoring-page .mobile-row-details summary::-webkit-details-marker {
        display: none;
    }

    .monitoring-page .mobile-row-details[open] summary {
        border-color: rgba(67, 97, 238, .22);
    }

    .monitoring-page .mobile-row-details[open] .mobile-summary-link::after {
        content: " ^";
    }

    .monitoring-page .mobile-row-details-body {
        display: none;
    }

    .monitoring-page .mobile-row-details[open] .mobile-row-details-body {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 10px;
        display: block;
        margin-top: 8px;
        padding: 12px;
    }

    .monitoring-page .mobile-details summary {
        align-items: flex-start;
        background: var(--soft);
        border: 1px solid #eef2f7;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 10px 12px;
        user-select: none;
        width: 100%;
    }

    .monitoring-page .mobile-details[open] summary {
        border-color: rgba(67, 97, 238, .22);
    }

    .monitoring-page .mobile-details[open] .mobile-summary-link::after {
        content: " ^";
    }

    .monitoring-page .mobile-details:not([open]) > .mobile-details-body {
        display: none;
    }

    .monitoring-page .mobile-details[open] > .mobile-details-body {
        display: block;
        margin-top: 10px;
    }

    .monitoring-page .assignment-list {
        justify-content: flex-start !important;
        max-width: 100%;
        width: 100%;
    }

    .monitoring-page .assignment-list .soft-badge,
    .monitoring-page .assignment-badge {
        width: auto;
    }

    .monitoring-page .metric-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        min-width: 0;
        width: 100%;
    }

    .monitoring-page .progress-wrap {
        min-width: 0;
        width: 100%;
    }
}

@media (max-width: 575.98px) {
    .monitoring-page .assignment-list {
        align-items: stretch;
        flex-direction: column;
    }

    .monitoring-page .assignment-list .soft-badge,
    .monitoring-page .assignment-badge {
        width: 100%;
    }

    .monitoring-page .complex-cell .metric-grid {
        grid-template-columns: 1fr;
    }

    .monitoring-page .metric-chip {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }

    .monitoring-page .metric-chip span,
    .monitoring-page .metric-chip strong {
        text-align: left;
    }

    .monitoring-page .metric-chip strong {
        flex: 0 0 auto;
        text-align: right;
    }

    .monitoring-page .money-chip {
        align-items: flex-start;
        display: block;
    }

    .monitoring-page .money-chip span,
    .monitoring-page .money-chip strong {
        text-align: left;
    }

    .monitoring-page .money-chip strong {
        font-size: 17px;
        margin-top: 6px;
        white-space: normal;
    }
}
</style>
