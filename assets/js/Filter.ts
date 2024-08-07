new class {
    form = <HTMLFormElement>document.querySelector('.ranking .filter form');
    filtersButton = <HTMLButtonElement>this.form?.previousElementSibling;
    checks: HTMLInputElement[] = [];

    constructor() {
        if (this.form) {
            this.checks = [...this.form.querySelectorAll<HTMLInputElement>('input[type=checkbox]')];

            this.initFilterForm();
            this.loadState();
        }
    }

    private initFilterForm() {
        // Filters button.
        this.filtersButton.addEventListener('click', _ => this.form.classList.toggle('open'));

        // OK button.
        this.form.querySelector('form > button.ok')!.addEventListener('click', e => {
            this.saveState();
            this.form.classList.remove('open');

            e.preventDefault();
        });

        // Cancel button.
        this.form.querySelector('form > button.cancel')!.addEventListener('click', e => {
            this.loadState();
            this.form.classList.remove('open');

            e.preventDefault();
        });

        // Reset button.
        this.form.querySelector('form > button.reset')!.addEventListener('click', _ => {
            // This handler triggers before controls have been modified.
            setTimeout(() => this.filterApps());
        });

        // Checkbox changes.
        this.checks.forEach(check => check.addEventListener('change', _ => this.filterApps()));
    }

    private filterApps() {
        const ranks = document.querySelectorAll('#body .ranking > div[id]'),
            platformChecks = this.checks.filter(check => this.form.querySelector('fieldset')!.contains(check)),
            checkedChecks = platformChecks.filter(check => check.checked);

        let kept = 0;

        // Only filter when previously filtered or less than all checks enabled (optimization to speed loading).
        if (this.filtersButton.getAttribute('data-filtered') || checkedChecks.length < platformChecks.length
            // Or hide owned apps checked.
            || this.form['owned'].checked
        ) {
            ranks.forEach(rank => {
                // Keep app if it has no platforms (e.g. devlisher pages).
                let keep = !rank.querySelector('.platforms') ||
                    checkedChecks.some(check => !!rank.querySelector('.platforms > .' + check.name));

                if (this.form['owned'].checked) {
                    keep &&= !rank.querySelector('a.owned');
                }

                rank.classList.toggle('filtered', !keep);

                if (keep) {
                    rank.classList.remove('primary', 'secondary');
                    rank.classList.add(++kept & 1 ? 'secondary' : 'primary');
                }
            });
        } else {
            kept = ranks.length;
        }

        // Update UI filter count.
        const filteredCount = ranks.length - kept;
        this.filtersButton.setAttribute('data-filtered', filteredCount ? `[-${filteredCount}]` : '');
    }

    private saveState() {
        let state: {[index: string]: any} = {};

        this.checks.forEach(check => state[check.name] = check.checked);

        localStorage.setItem('filter', JSON.stringify(state));

        return state;
    }

    private loadState() {
        let state;

        if (!(state = this.validateState())) {
            // Save the default state.
            state = this.saveState();
        }

        for (const [name, value] of Object.entries(state)) {
            // Checkbox name may no longer exist if feature was removed.
            if (this.form[name]) {
                this.form[name].checked = value;
            }
        }

        this.filterApps();
    }

    private validateState() {
        try {
            return JSON.parse(localStorage.getItem('filter')!);
        } catch (error) {
            return false;
        }
    }
};
