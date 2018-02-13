new class {
    constructor() {
        this.form = document.querySelector('.ranking form');
        this.filtersButton = this.form.previousElementSibling;
        this.checks = [...this.form.querySelectorAll('input[type=checkbox]')];

        this.initFilterForm();
        this.loadState();
    }

    initFilterForm() {
        // Filters button.
        this.filtersButton.addEventListener('click', _ => this.form.classList.toggle('open'));

        // OK button.
        this.form.parentElement.querySelector('form > button.ok').addEventListener('click', e => {
            this.saveState();
            this.form.classList.remove('open');

            e.preventDefault();
        });

        // Cancel button.
        this.form.parentElement.querySelector('form > button.cancel').addEventListener('click', e => {
            this.loadState();
            this.form.classList.remove('open');

            e.preventDefault();
        });

        // Reset button.
        this.form.parentElement.querySelector('form > button.reset').addEventListener('click', _ => {
            // This handler triggers before controls have been modified.
            setTimeout(_ => this.filterApps());
        });

        // Checkbox changes.
        this.checks.forEach(check => check.addEventListener('change', _ => this.filterApps()));
    }

    filterApps() {
        const ranks = document.querySelectorAll('.ranking > div[id]'),
            checkedChecks = this.checks.filter(check => check.checked);

        let kept = 0;

        // Only filter when previously filtered or less than all checks enabled (optimization to speed loading).
        if (this.filtersButton.getAttribute('data-filtered') || checkedChecks.length < this.checks.length) {
            ranks.forEach(rank => {
                let keep = checkedChecks.some(check => {
                    if (check.name === 'vr') {
                        return !!rank.querySelector('.platforms > .vive, .platforms > .rift, .platforms > .wmr')
                    }

                    return !!rank.querySelector('.platforms > .' + check.name);
                });

                rank.classList.toggle('filtered', !keep);

                if (keep) {
                    rank.classList.remove('primary', 'secondary');
                    rank.classList.add(++kept & 1 ? 'primary' : 'secondary');
                }
            });
        } else {
            kept = ranks.length;
        }

        // Update UI filter count.
        const filteredCount = ranks.length - kept;
        this.filtersButton.setAttribute('data-filtered', filteredCount ? `[-${filteredCount}]` : '');
    }

    saveState() {
        let state = {};

        this.checks.forEach(check => state[check.name] = check.checked);

        localStorage.setItem('filter', JSON.stringify(state));

        return state;
    }

    loadState() {
        let state;

        if (!(state = this.validateState())) {
            // Save the default state.
            state = this.saveState();
        }

        for (const [name, value] of Object.entries(state)) {
            this.form[name].checked = value;
        }

        this.filterApps();
    }

    validateState() {
        try {
            return JSON.parse(localStorage.getItem('filter'));
        } catch (error) {
            return false;
        }
    }
};
