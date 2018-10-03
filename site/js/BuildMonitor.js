class BuildMonitor {
    constructor(date, element) {
        this.nextBuild = date && moment(date).add({days: 1});
        this.element = element;
        this.blink = 0;

        // Build is pending.
        if (!this.nextBuild) {
            return this.showBuilding();
        }

        // Build is overdue.
        if (this.nextBuild <= moment()) {
            // Remove clock because scheduled build has failed to start on time, usually due to scheduled import error.
            this.element.remove()
        }

        // Time remains on the clock.
        this.monitor();
    }

    static createElement() {
        let element = document.createElement('div');
        element.classList.add('countdown');
        element.innerHTML = 'Initializing...';

        document.querySelector('.body').appendChild(element);

        return element;
    }

    monitor() {
        this.timer = setInterval(() => this.showNextUpdate(), 500);
        this.showNextUpdate();
    }

    showNextUpdate() {
        let duration = this.calculateDuration();

        if (duration <= 0) {
            clearInterval(this.timer);

            return this.showReady();
        }

        let formattedDuration = duration.format('[<span>]HH:mm.ss[</span>]');

        this.element.innerHTML = 'Next update in ' + (
            (this.blink ^= 1)
                ? formattedDuration
                : formattedDuration.replace(/:/, ' ')
        );
    }

    showBuilding() {
        this.element.classList.add('building');
        this.element.innerHTML = 'Building update' + '<span>.</span>'.repeat(3);
    }

    showReady() {
        this.element.classList.add('ready');
        this.element.innerHTML = '<a href="https://youtu.be/Mu0cE9RgK5M">Ready for launch</a>';
    }

    calculateDuration() {
        return moment.utc(moment.duration(this.nextBuild - moment()).asMilliseconds());
    }
}
