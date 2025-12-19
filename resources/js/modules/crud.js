const toggleModal = (id, show) => {
    const modal = document.getElementById(`modal-${id}`);
    if (modal) {
        modal.classList.toggle('hidden', !show);
    }
};

const attachModalListeners = () => {
    document.querySelectorAll('[data-modal-target]').forEach((btn) => {
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
                document.getElementById('investor-capital').value = investor.capital_usd || 0;
                document.getElementById('investor-monthly').value = investor.monthly_rate || 0;
                document.getElementById('investor-status').value = investor.status || 'Activo';
            }

            if (target === 'investment-edit' && btn.dataset.investment) {
                const investment = JSON.parse(btn.dataset.investment);
                const form = document.getElementById('investment-edit-form');
                form.action = `/investments/${investment.id}`;
                document.getElementById('investment-investor').value = investment.investor_id;
                document.getElementById('investment-code').value = investment.code;
                document.getElementById('investment-amount').value = investment.amount_usd;
                document.getElementById('investment-rate').value = investment.monthly_rate;
                document.getElementById('investment-gains').value = investment.gains_cop;
                document.getElementById('investment-start').value = investment.start_date;
                document.getElementById('investment-next').value = investment.next_liquidation_date || '';
                document.getElementById('investment-status').value = investment.status;
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
                document.getElementById('movement-amount-cop').value = movement.amount_cop;
                document.getElementById('movement-amount-usd').value = movement.amount_usd;
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

    document.querySelectorAll('[data-close-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            btn.closest('.fixed')?.classList.add('hidden');
        });
    });
};

document.addEventListener('DOMContentLoaded', attachModalListeners);
