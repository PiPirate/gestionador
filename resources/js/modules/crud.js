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

const attachContinuationToggles = (root = document) => {
    root.querySelectorAll('[data-continuation-select]').forEach((select) => {
        if (select.dataset.bound) {
            return;
        }
        select.dataset.bound = 'true';
        const form = select.closest('form');
        if (!form) {
            return;
        }

        const toggleFields = () => {
            const fields = form.querySelectorAll('[data-continuation-field]');
            const isContinuation = Boolean(select.value);
            fields.forEach((field) => {
                if (field.dataset.continuationRequired === undefined) {
                    field.dataset.continuationRequired = field.required ? 'true' : 'false';
                }
                field.disabled = isContinuation;
                if (isContinuation) {
                    field.required = false;
                    field.value = '';
                } else {
                    field.required = field.dataset.continuationRequired === 'true';
                }
            });
        };

        select.addEventListener('change', toggleFields);
        toggleFields();
    });
};

const formatCopDisplay = (value) => {
    return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
};

const calcMonthlyProfit = (amount, tiers) => {
    if (!amount || amount <= 0) {
        return 0;
    }
    const normalized = [...tiers]
        .map((tier) => ({
            upTo: tier.upTo ?? tier.up_to ?? null,
            rate: Math.max(0, Number(tier.rate || 0)),
        }))
        .sort((a, b) => {
            const aCap = a.upTo === null ? Infinity : Number(a.upTo);
            const bCap = b.upTo === null ? Infinity : Number(b.upTo);
            return aCap - bCap;
        });

    let remaining = amount;
    let previousCap = 0;
    let profit = 0;

    normalized.forEach((tier) => {
        if (remaining <= 0) {
            return;
        }
        const cap = tier.upTo === null ? Infinity : Math.max(Number(tier.upTo), previousCap);
        const chunk = Math.min(remaining, cap - previousCap);
        if (chunk > 0) {
            profit += chunk * tier.rate;
            remaining -= chunk;
        }
        previousCap = cap;
    });

    return profit;
};

const attachProfitRuleHandlers = (root = document) => {
    root.querySelectorAll('[data-profit-rule]').forEach((form) => {
        if (form.dataset.boundProfit) {
            return;
        }
        form.dataset.boundProfit = 'true';

        let tiers = [];
        try {
            tiers = JSON.parse(form.dataset.profitTiers || '[]');
        } catch (error) {
            tiers = [];
        }

        const amountField = form.querySelector('[name="amount_cop"]');
        const startField = form.querySelector('[data-profit-start]');
        const endField = form.querySelector('[data-profit-end]');
        const effectiveLabel = form.querySelector('[data-profit-effective]');
        const monthlyLabel = form.querySelector('[data-profit-monthly]');
        const dailyLabel = form.querySelector('[data-profit-daily]');

        const updatePreview = () => {
            if (!amountField || !effectiveLabel || !monthlyLabel || !dailyLabel) {
                return;
            }
            const amount = parseCopValue(amountField.value || '0');
            const monthlyProfit = calcMonthlyProfit(amount, tiers);
            const referenceDate = endField?.value || startField?.value;
            let monthDays = 0;
            if (referenceDate) {
                const parsed = new Date(referenceDate);
                if (!Number.isNaN(parsed.getTime())) {
                    monthDays = new Date(parsed.getFullYear(), parsed.getMonth() + 1, 0).getDate();
                }
            }
            const dailyProfit = monthDays > 0 ? monthlyProfit / monthDays : 0;
            const effectiveRate = amount > 0 ? (monthlyProfit / amount) * 100 : 0;

            effectiveLabel.textContent = `${effectiveRate.toFixed(2)}%`;
            monthlyLabel.textContent = monthlyProfit ? formatCopDisplay(monthlyProfit) : '—';
            dailyLabel.textContent = dailyProfit ? formatCopDisplay(dailyProfit) : '—';
        };

        amountField?.addEventListener('input', updatePreview);
        amountField?.addEventListener('blur', updatePreview);
        startField?.addEventListener('change', updatePreview);
        endField?.addEventListener('change', updatePreview);
        updatePreview();
    });
};

const attachLiquidationFormHandlers = (root = document) => {
    root.querySelectorAll('[data-liquidation-form]').forEach((form) => {
        if (form.dataset.liquidationBound) {
            return;
        }
        form.dataset.liquidationBound = 'true';

        const investorSelect = form.querySelector('[data-liquidation-investor]');
        const investmentSelect = form.querySelector('[data-liquidation-investment]');
        const gainInput = form.querySelector('[data-liquidation-gain]');
        const capitalInput = form.querySelector('[data-liquidation-capital]');
        const gainAvailable = form.querySelector('[data-liquidation-available-gain]');
        const capitalAvailable = form.querySelector('[data-liquidation-available-capital]');

        if (!investorSelect || !investmentSelect) {
            return;
        }

        const updateInvestmentOptions = () => {
            const investorId = investorSelect.value;
            const options = Array.from(investmentSelect.options);
            options.forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }
                const matchesInvestor = !investorId || option.dataset.investorId === investorId;
                const availableGain = option.dataset.availableGain ? Number(option.dataset.availableGain) : 0;
                const availableCapital = option.dataset.availableCapital ? Number(option.dataset.availableCapital) : 0;
                const requiresAvailable = form.dataset.liquidationFilter === 'available';
                const hasAvailable = availableGain > 0 || availableCapital > 0;
                option.hidden = !matchesInvestor || (requiresAvailable && !hasAvailable);
            });
            if (investmentSelect.selectedOptions.length && investmentSelect.selectedOptions[0].hidden) {
                investmentSelect.value = '';
            }
            updateInvestmentDetails();
        };

        const updateInvestmentDetails = () => {
            const option = investmentSelect.selectedOptions[0];
            const availableGain = option?.dataset.availableGain ? Number(option.dataset.availableGain) : 0;
            const availableCapital = option?.dataset.availableCapital ? Number(option.dataset.availableCapital) : 0;

            if (gainAvailable) {
                gainAvailable.textContent = formatCopDisplay(availableGain);
            }
            if (capitalAvailable) {
                capitalAvailable.textContent = formatCopDisplay(availableCapital);
            }
            if (gainInput) {
                gainInput.max = availableGain.toFixed(2);
                gainInput.disabled = availableGain <= 0;
                if (gainInput.disabled) {
                    gainInput.value = '';
                }
                const currentGain = parseCopValue(gainInput.value || '0');
                if (!gainInput.disabled && currentGain > availableGain) {
                    gainInput.value = availableGain.toFixed(2);
                    formatNumericInput(gainInput);
                }
            }

            if (capitalInput) {
                capitalInput.max = availableCapital.toFixed(2);
                capitalInput.disabled = availableCapital <= 0;
                if (capitalInput.disabled) {
                    capitalInput.value = '';
                }
                const currentCapital = parseCopValue(capitalInput.value || '0');
                if (!capitalInput.disabled && currentCapital > availableCapital) {
                    capitalInput.value = availableCapital.toFixed(2);
                    formatNumericInput(capitalInput);
                }
            }
        };

        investorSelect.addEventListener('change', updateInvestmentOptions);
        investmentSelect.addEventListener('change', updateInvestmentDetails);

        updateInvestmentOptions();
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
        attachContinuationToggles(currentTable);
        attachLiquidationFormHandlers(currentTable);
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
        attachContinuationToggles(currentTarget);
        attachLiquidationFormHandlers(currentTarget);
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
                const effectiveRateLabel = document.getElementById('investment-effective-rate');
                if (effectiveRateLabel) {
                    const effectiveRate = investment.monthly_profit_snapshot && investment.amount_cop
                        ? (investment.monthly_profit_snapshot / investment.amount_cop) * 100
                        : investment.monthly_rate;
                    effectiveRateLabel.textContent = `${Number(effectiveRate || 0).toFixed(2)}%`;
                }
                const monthlyProfitLabel = document.getElementById('investment-monthly-profit');
                if (monthlyProfitLabel) {
                    monthlyProfitLabel.textContent = investment.monthly_profit_snapshot
                        ? formatCopDisplay(investment.monthly_profit_snapshot)
                        : '—';
                }
                const dailyProfitLabel = document.getElementById('investment-daily-profit');
                if (dailyProfitLabel) {
                    dailyProfitLabel.textContent = investment.daily_interest_snapshot
                        ? formatCopDisplay(investment.daily_interest_snapshot)
                        : '—';
                }
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
                const effectiveRateLabel = document.getElementById('investment-edit-effective-rate');
                if (effectiveRateLabel) {
                    const effectiveRate = investment.monthly_profit_snapshot && investment.amount_cop
                        ? (investment.monthly_profit_snapshot / investment.amount_cop) * 100
                        : investment.monthly_rate;
                    effectiveRateLabel.textContent = `${Number(effectiveRate || 0).toFixed(2)}%`;
                }
                const monthlyProfitLabel = document.getElementById('investment-edit-monthly-profit');
                if (monthlyProfitLabel) {
                    monthlyProfitLabel.textContent = investment.monthly_profit_snapshot
                        ? formatCopDisplay(investment.monthly_profit_snapshot)
                        : '—';
                }
                const dailyProfitLabel = document.getElementById('investment-edit-daily-profit');
                if (dailyProfitLabel) {
                    dailyProfitLabel.textContent = investment.daily_interest_snapshot
                        ? formatCopDisplay(investment.daily_interest_snapshot)
                        : '—';
                }
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
                const investorSelect = document.getElementById('liquidation-investor');
                const investmentSelect = document.getElementById('liquidation-investment');
                const gainInput = document.getElementById('liquidation-gain');
                const capitalInput = document.getElementById('liquidation-capital');

                investorSelect.value = liq.investor_id;
                if (investmentSelect) {
                    investmentSelect.value = liq.investment_id || '';
                    const selectedOption = investmentSelect.selectedOptions[0];
                    if (selectedOption) {
                        const extraGain = Number(liq.withdrawn_gain_cop ?? liq.gain_cop ?? 0);
                        const extraCapital = Number(liq.withdrawn_capital_cop ?? 0);
                        const currentGain = selectedOption.dataset.availableGain ? Number(selectedOption.dataset.availableGain) : 0;
                        const currentCapital = selectedOption.dataset.availableCapital ? Number(selectedOption.dataset.availableCapital) : 0;
                        selectedOption.dataset.availableGain = (currentGain + extraGain).toString();
                        selectedOption.dataset.availableCapital = (currentCapital + extraCapital).toString();
                    }
                }

                if (gainInput) {
                    gainInput.value = liq.withdrawn_gain_cop ?? liq.gain_cop ?? 0;
                    formatNumericInput(gainInput);
                }

                if (capitalInput) {
                    capitalInput.value = Number(liq.withdrawn_capital_cop ?? 0);
                    formatNumericInput(capitalInput);
                }

                document.getElementById('liquidation-due').value = normalizeDateInput(liq.due_date);
                document.getElementById('liquidation-status').value = liq.status;

                attachLiquidationFormHandlers(form);
                investorSelect.dispatchEvent(new Event('change'));
                investmentSelect?.dispatchEvent(new Event('change'));
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
    attachContinuationToggles();
    attachLiquidationFormHandlers();
    attachProfitRuleHandlers();
});
