/**
 * Grav Problems Report — Web Component for admin-next reports.
 *
 * Receives `report` property with structured problem data from the API.
 * Each item in report.items is a Problem: { id, status, level, msg, help, details }
 *
 * Uses CSS custom properties from the admin-next theme for light/dark mode support.
 */
const TAG = window.__GRAV_REPORT_TAG || 'grav-problems--problems-report';

class ProblemsReportElement extends HTMLElement {
    #report = null;

    set report(val) {
        this.#report = val;
        this.render();
    }

    get report() {
        return this.#report;
    }

    connectedCallback() {
        if (this.#report) this.render();
    }

    render() {
        const report = this.#report;
        if (!report) return;

        const items = report.items || [];
        const style = document.createElement('style');
        style.textContent = `
            :host {
                display: block;
                font-family: inherit;
            }
            .status-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 8px 16px;
                font-size: 13px;
                font-weight: 500;
                border-bottom: 1px solid var(--border, #e5e7eb);
            }
            .status-bar.success {
                background: color-mix(in srgb, #22c55e 12%, transparent);
                color: color-mix(in srgb, #16a34a 80%, var(--foreground, #1f2937));
            }
            .status-bar.error {
                background: color-mix(in srgb, #ef4444 12%, transparent);
                color: color-mix(in srgb, #dc2626 80%, var(--foreground, #1f2937));
            }
            .status-bar.warning {
                background: color-mix(in srgb, #a78bfa 12%, transparent);
                color: color-mix(in srgb, #7c3aed 70%, var(--foreground, #1f2937));
            }
            .status-bar .msg {
                flex: 1;
            }
            .status-bar .msg strong {
                font-weight: 700;
            }
            .help-link {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                color: var(--muted-foreground, #6b7280);
                text-decoration: none;
                font-size: 11px;
                font-weight: 500;
                padding: 2px 8px;
                border-radius: 4px;
                border: 1px solid var(--border, #e5e7eb);
                background: var(--card, #fff);
                white-space: nowrap;
                transition: border-color 0.15s;
            }
            .help-link:hover {
                border-color: var(--foreground, #1f2937);
                color: var(--foreground, #1f2937);
            }
            .help-link svg {
                width: 12px;
                height: 12px;
            }
            .detail-list {
                border-top: none;
            }
            .detail-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 7px 16px;
                font-size: 13px;
                color: var(--foreground, #1f2937);
                border-bottom: 1px solid var(--border, #e5e7eb);
            }
            .detail-item:last-child {
                border-bottom: none;
            }
            .detail-msg {
                flex: 1;
                min-width: 0;
                color: var(--muted-foreground, #6b7280);
            }
            .detail-msg .module-name {
                font-weight: 600;
                margin-right: 2px;
            }
            .detail-msg .module-name.error-name {
                color: color-mix(in srgb, #ef4444 85%, var(--foreground, #1f2937));
            }
            .detail-msg .module-name.warning-name {
                color: var(--primary, #3b82f6);
            }
            .detail-msg .module-name.success-name {
                color: color-mix(in srgb, #22c55e 85%, var(--foreground, #1f2937));
            }
            .status-icon {
                flex-shrink: 0;
                width: 18px;
                height: 18px;
                border-radius: 3px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .status-icon svg {
                width: 12px;
                height: 12px;
            }
            .status-icon.success-icon {
                background: color-mix(in srgb, #22c55e 15%, transparent);
                color: color-mix(in srgb, #22c55e 85%, var(--foreground, #1f2937));
            }
            .status-icon.warning-icon {
                background: color-mix(in srgb, var(--primary, #3b82f6) 15%, transparent);
                color: var(--primary, #3b82f6);
            }
            .status-icon.error-icon {
                background: color-mix(in srgb, #ef4444 15%, transparent);
                color: color-mix(in srgb, #ef4444 85%, var(--foreground, #1f2937));
            }
        `;

        const shadow = this.shadowRoot || this.attachShadow({ mode: 'open' });
        shadow.innerHTML = '';
        shadow.appendChild(style);

        for (const item of items) {
            const section = document.createElement('div');
            section.className = 'problem-section';

            // Determine bar color
            let barClass = 'success';
            if (!item.status && item.level === 'critical') barClass = 'error';
            else if (!item.status && item.level === 'warning') barClass = 'warning';
            else if (item.status && this.hasWarnings(item)) barClass = 'warning';

            // Status bar
            const bar = document.createElement('div');
            bar.className = `status-bar ${barClass}`;

            const msgSpan = document.createElement('span');
            msgSpan.className = 'msg';
            msgSpan.innerHTML = `<strong>${this.escHtml(item.id)}:</strong> ${item.msg}`;
            bar.appendChild(msgSpan);

            if (item.help) {
                const helpLink = document.createElement('a');
                helpLink.className = 'help-link';
                helpLink.href = item.help;
                helpLink.target = '_blank';
                helpLink.rel = 'noopener';
                helpLink.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg> Help`;
                bar.appendChild(helpLink);
            }

            section.appendChild(bar);

            // Detail items (errors, warnings, success)
            if (item.details && typeof item.details === 'object') {
                const detailList = document.createElement('div');
                detailList.className = 'detail-list';

                this.renderDetails(detailList, item.details.errors, 'error');
                this.renderDetails(detailList, item.details.warning, 'warning');
                this.renderDetails(detailList, item.details.success, 'success');

                if (detailList.children.length > 0) {
                    section.appendChild(detailList);
                }
            }

            shadow.appendChild(section);
        }
    }

    hasWarnings(item) {
        return item.details?.warning && Object.keys(item.details.warning).length > 0;
    }

    renderDetails(container, details, type) {
        if (!details || typeof details !== 'object') return;

        for (const [module, message] of Object.entries(details)) {
            const row = document.createElement('div');
            row.className = 'detail-item';

            const msgEl = document.createElement('span');
            msgEl.className = 'detail-msg';
            msgEl.innerHTML = `<span class="module-name ${type}-name">${this.escHtml(module)}</span> - ${this.escHtml(message)}`;
            row.appendChild(msgEl);

            const icon = document.createElement('span');
            icon.className = `status-icon ${type}-icon`;
            icon.innerHTML = type === 'success'
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
            row.appendChild(icon);

            container.appendChild(row);
        }
    }

    escHtml(str) {
        if (typeof str !== 'string') return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

customElements.define(TAG, ProblemsReportElement);
