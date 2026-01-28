const toggleModal = (id, show) => {
    const modal = document.getElementById(`modal-${id}`);
    if (modal) {
        modal.classList.toggle('hidden', !show);
    }
};

const setTableLoading = (tableRoot, loading) => {
    const tableBody = tableRoot?.querySelector('[data-table-body]');
    if (!tableRoot || !tableBody) {
        return;
    }
    if (loading) {
        tableRoot.dataset.loading = 'true';
        const columnCount = tableRoot.querySelectorAll('[data-table-header] [data-sortable], [data-table-header] span').length || 1;
        tableBody.innerHTML = `
            <div class="py-6 text-sm text-gray-500 animate-pulse" style="grid-column: span ${columnCount};">
                Actualizando información...
            </div>
        `;
    } else {
        delete tableRoot.dataset.loading;
    }
};

const parseCopValue = (value) => {
    const raw = value.replace(/[^\d,.-]/g, '');
    if (raw.includes(',')) {
        return Number(raw.replace(/\./g, '').replace(',', '.'));
    }
    return Number(raw.replace(/\./g, '').replace(/,/g, ''));
};

const formatNumericInput = (input) => {
    const number = parseCopValue(input.value);
    if (Number.isNaN(number)) {
        return;
    }
    input.value = new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number);
};

const normalizeNumericValue = (value) => {
    const number = parseCopValue(value);
    return Number.isNaN(number) ? value : number.toString();
};

const normalizeFormNumericFields = (form) => {
    form.querySelectorAll('[data-format]').forEach((field) => {
        field.value = normalizeNumericValue(field.value);
    });
};

const validateDateRange = (form) => {
    const startField = form.querySelector('[data-date-start]');
    const endField = form.querySelector('[data-date-end]');
    if (!startField || !endField) {
        return true;
    }
    if (!startField.value || !endField.value) {
        return true;
    }
    const start = new Date(startField.value);
    const end = new Date(endField.value);
    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return true;
    }
    if (start > end) {
        alert('La fecha de inicio no puede ser mayor a la fecha de finalización.');
        return false;
    }
    return true;
};

const bindNumericFormatting = (root = document) => {
    root.querySelectorAll('[data-format]').forEach((input) => {
        if (input.dataset.bound) {
            return;
        }
        input.dataset.bound = 'true';
        input.addEventListener('input', () => formatNumericInput(input));
        input.addEventListener('blur', () => formatNumericInput(input));

        const form = input.closest('form');
        if (form && !form.dataset.normalizeBound) {
            form.dataset.normalizeBound = 'true';
            form.addEventListener('submit', () => normalizeFormNumericFields(form));
        }
    });
};

const normalizeDateInput = (value) => {
    if (!value) {
        return '';
    }

    if (value.includes('T')) {
        return value.split('T')[0];
    }

    if (value.includes(' ')) {
        return value.split(' ')[0];
    }

    return value;
};

const parseCellValue = (value) => {
    const normalized = value.replace(/[^\d.,-]/g, '').replace(/\./g, '').replace(',', '.');
    const numeric = Number(normalized);
    if (!Number.isNaN(numeric) && normalized !== '') {
        return { type: 'number', value: numeric };
    }
    return { type: 'string', value: value.toLowerCase() };
};

const sortTableRows = (tableBody, columnIndex, direction) => {
    const rows = Array.from(tableBody.querySelectorAll('[data-row]'));
    rows.sort((a, b) => {
        const aCell = a.querySelectorAll('[data-cell]')[columnIndex];
        const bCell = b.querySelectorAll('[data-cell]')[columnIndex];
        const aValue = parseCellValue(aCell?.textContent?.trim() || '');
        const bValue = parseCellValue(bCell?.textContent?.trim() || '');
        if (aValue.type === 'number' && bValue.type === 'number') {
            return direction === 'desc' ? bValue.value - aValue.value : aValue.value - bValue.value;
        }
        if (aValue.value < bValue.value) {
            return direction === 'desc' ? 1 : -1;
        }
        if (aValue.value > bValue.value) {
            return direction === 'desc' ? -1 : 1;
        }
        return 0;
    });
    rows.forEach((row) => tableBody.appendChild(row));
};

const refreshTableTarget = async (url, targetSelector) => {
    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!response.ok) {
        return;
    }
    const html = await response.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const nextTable = doc.querySelector(targetSelector);
    const currentTable = document.querySelector(targetSelector);
    if (nextTable && currentTable) {
        currentTable.innerHTML = nextTable.innerHTML;
        attachTableHandlers(currentTable);
        attachModalListeners(currentTable);
        bindNumericFormatting(currentTable);
    }
    const refreshTargets = currentTable?.dataset.refreshTarget
        ? currentTable.dataset.refreshTarget.split(',').map((target) => target.trim()).filter(Boolean)
        : [];
    refreshTargets.forEach((selector) => {
        const nextTarget = doc.querySelector(selector);
        const currentTarget = document.querySelector(selector);
        if (!nextTarget || !currentTarget) {
            return;
        }
        currentTarget.innerHTML = nextTarget.innerHTML;
        attachTableHandlers(currentTarget);
        attachModalListeners(currentTarget);
        bindNumericFormatting(currentTarget);
    });
};

const attachTableHandlers = (root = document) => {
    root.querySelectorAll('[data-sortable]').forEach((header) => {
        if (header.dataset.bound) {
            return;
        }
        header.dataset.bound = 'true';
        header.addEventListener('click', () => {
            const tableRoot = header.closest('[data-table-root]');
            const tableBody = tableRoot?.querySelector('[data-table-body]');
            const columnIndex = Number(header.dataset.sortColumn || 0);
            if (!tableBody) {
                return;
            }
            const currentDirection = header.dataset.sortDirection || '';
            const nextDirection = currentDirection === 'asc' ? 'desc' : currentDirection === 'desc' ? '' : 'asc';
            header.dataset.sortDirection = nextDirection;
            if (nextDirection) {
                sortTableRows(tableBody, columnIndex, nextDirection);
            } else {
                const originalRows = Array.from(tableBody.querySelectorAll('[data-row]'))
                    .sort((a, b) => Number(a.dataset.index || 0) - Number(b.dataset.index || 0));
                originalRows.forEach((row) => tableBody.appendChild(row));
            }
            tableRoot.querySelectorAll('[data-sortable]').forEach((item) => {
                const arrow = item.querySelector('[data-sort-arrow]');
                if (!arrow) {
                    return;
                }
                arrow.textContent = item === header && nextDirection ? (nextDirection === 'asc' ? '↑' : '↓') : '';
                if (item !== header) {
                    item.dataset.sortDirection = '';
                }
            });
        });
    });
};

const attachModalListeners = (root = document) => {
    root.querySelectorAll('[data-modal-target]').forEach((btn) => {
        if (btn.dataset.bound) {
            return;
        }
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-modal-target');
            toggleModal(target, true);

            if (target === 'user-edit' && btn.dataset.user) {
                const user = JSON.parse(btn.dataset.user);
                const form = document.getElementById('user-edit-form');
                form.action = `/settings/users/${user.id}`;
                document.getElementById('edit-name').value = user.name;
                document.getElementById('edit-email').value = user.email;
                document.getElementById('edit-role').value = user.role;
                document.getElementById('edit-status').value = user.status;
                document.getElementById('edit-password').value = '';
            }

            if (target === 'investor-edit' && btn.dataset.investor) {
                const investor = JSON.parse(btn.dataset.investor);
                const form = document.getElementById('investor-edit-form');
                form.action = `/investors/${investor.id}`;
                document.getElementById('investor-name').value = investor.name;
                document.getElementById('investor-document').value = investor.document;
                document.getElementById('investor-email').value = investor.email || '';
                document.getElementById('investor-phone').value = investor.phone || '';
                document.getElementById('investor-monthly').value = investor.monthly_rate || 0;
                document.getElementById('investor-status').value = investor.status || 'Activo';
            }

            if (target === 'investment-edit' && btn.dataset.investment) {
                const investment = JSON.parse(btn.dataset.investment);
                const form = document.getElementById('investment-edit-form');
                form.action = `/investments/${investment.id}`;
                document.getElementById('investment-investor').value = investment.investor_id;
                document.getElementById('investment-code').value = investment.code;
                const amountInput = document.getElementById('investment-amount');
                amountInput.value = investment.amount_cop;
                formatNumericInput(amountInput);
                document.getElementById('investment-rate').value = investment.monthly_rate;
                document.getElementById('investment-start').value = normalizeDateInput(investment.start_date);
                document.getElementById('investment-end').value = normalizeDateInput(investment.end_date);
                document.getElementById('investment-status').value = investment.status;
                const updatedAtLabel = document.getElementById('investment-updated-at');
                if (updatedAtLabel) {
                    updatedAtLabel.textContent = investment.updated_at
                        ? new Date(investment.updated_at).toLocaleString('es-CO')
                        : '—';
                }
                const updatedByLabel = document.getElementById('investment-updated-by');
                if (updatedByLabel) {
                    updatedByLabel.textContent = investment.updated_by_name || investment.updated_by || 'No registrado';
                }
            }

            if (target === 'investment-edit-investor' && btn.dataset.investment) {
                const investment = JSON.parse(btn.dataset.investment);
                const form = document.getElementById('investment-edit-investor-form');
                if (form) {
                    form.action = `/investments/${investment.id}`;
                }
                document.getElementById('investment-edit-code').value = investment.code;
                const amountInput = document.getElementById('investment-edit-amount');
                amountInput.value = investment.amount_cop;
                formatNumericInput(amountInput);
                document.getElementById('investment-edit-rate').value = investment.monthly_rate;
                document.getElementById('investment-edit-start').value = normalizeDateInput(investment.start_date);
                document.getElementById('investment-edit-end').value = normalizeDateInput(investment.end_date);
                document.getElementById('investment-edit-status').value = investment.status;
            }

            if (target === 'transaction-edit' && btn.dataset.transaction) {
                const tx = JSON.parse(btn.dataset.transaction);
                const form = document.getElementById('transaction-edit-form');
                form.action = `/transactions/${tx.id}`;
                document.getElementById('tx-type').value = tx.type;
                document.getElementById('tx-reference').value = tx.reference || '';
                document.getElementById('tx-amount').value = tx.amount_usd;
                document.getElementById('tx-rate').value = tx.rate;
                document.getElementById('tx-cop').value = tx.amount_cop;
                document.getElementById('tx-counterparty').value = tx.counterparty;
                document.getElementById('tx-method').value = tx.method || '';
                document.getElementById('tx-profit').value = tx.profit_cop || '';
                document.getElementById('tx-date').value = tx.transacted_at;
            }

            if (target === 'movement-edit' && btn.dataset.movement) {
                const movement = JSON.parse(btn.dataset.movement);
                const form = document.getElementById('movement-edit-form');
                form.action = `/cash/movements/${movement.id}`;
                document.getElementById('movement-date').value = movement.date;
                document.getElementById('movement-type').value = movement.type;
                document.getElementById('movement-description').value = movement.description;
                document.getElementById('movement-amount').value = movement.amount_cop;
                document.getElementById('movement-reference').value = movement.reference || '';
                const accountSelect = document.getElementById('movement-account');
                if (accountSelect) {
                    accountSelect.value = movement.account_id || '';
                }
            }

            if (target === 'account-edit' && btn.dataset.account) {
                const account = JSON.parse(btn.dataset.account);
                const form = document.getElementById('account-edit-form');
                form.action = `/cash/accounts/${account.id}`;
                document.getElementById('account-name').value = account.name;
                document.getElementById('account-type').value = account.type;
                document.getElementById('account-balance-cop').value = account.balance_cop;
                document.getElementById('account-balance-usd').value = account.balance_usd;
            }

            if (target === 'liquidation-edit' && btn.dataset.liquidation) {
                const liq = JSON.parse(btn.dataset.liquidation);
                const form = document.getElementById('liquidation-edit-form');
                form.action = `/liquidations/${liq.id}`;
                document.getElementById('liquidation-investor').value = liq.investor_id;
                document.getElementById('liquidation-rate').value = liq.monthly_rate;
                document.getElementById('liquidation-amount').value = liq.amount_usd;
                document.getElementById('liquidation-start').value = liq.period_start;
                document.getElementById('liquidation-end').value = liq.period_end;
                document.getElementById('liquidation-due').value = liq.due_date || '';
                document.getElementById('liquidation-status').value = liq.status;
            }
        });
    });

    root.querySelectorAll('[data-close-modal]').forEach((btn) => {
        if (btn.dataset.bound) {
            return;
        }
        btn.dataset.bound = 'true';
        btn.addEventListener('click', () => {
            btn.closest('.fixed')?.classList.add('hidden');
        });
    });

    root.querySelectorAll('[data-table-filter]').forEach((form) => {
        if (form.dataset.bound) {
            return;
        }
        form.dataset.bound = 'true';
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const targetSelector = form.dataset.tableTarget;
            if (!targetSelector) {
                return;
            }
            const tableRoot = document.querySelector(targetSelector);
            setTableLoading(tableRoot, true);
            const url = new URL(form.action || window.location.href, window.location.origin);
            const formData = new FormData(form);
            formData.forEach((value, key) => {
                url.searchParams.set(key, value.toString());
            });
            await refreshTableTarget(url.toString(), targetSelector);
            setTableLoading(tableRoot, false);
        });
    });

    root.querySelectorAll('[data-table-update]').forEach((form) => {
        if (form.dataset.bound) {
            return;
        }
        form.dataset.bound = 'true';
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const targetSelector = form.dataset.tableTarget;
            if (!targetSelector) {
                return;
            }
            if (!validateDateRange(form)) {
                return;
            }
            const modalRoot = form.closest('.fixed');
            if (modalRoot) {
                modalRoot.classList.add('hidden');
            }
            const tableRoot = document.querySelector(targetSelector);
            setTableLoading(tableRoot, true);
            normalizeFormNumericFields(form);
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            if (response.ok) {
                await refreshTableTarget(window.location.href, targetSelector);
            }
            setTableLoading(tableRoot, false);
        });
    });

    root.querySelectorAll('[data-validate-dates]').forEach((form) => {
        if (form.dataset.boundDates) {
            return;
        }
        form.dataset.boundDates = 'true';
        form.addEventListener('submit', (event) => {
            if (!validateDateRange(form)) {
                event.preventDefault();
            }
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    attachModalListeners();
    attachTableHandlers();
    bindNumericFormatting();
});
