export default class BuildMonitor {
    constructor(date) {
        this.nextBuild = date && moment(date).add({days: 1});
        this.element = BuildMonitor.createElement();
        this.blink = 0;

        // Build is pending.
        if (!this.nextBuild) {
            return this.showBuilding();
        }

        const now = moment();

        // Build is overdue.
        if (this.nextBuild <= now) {
            // Estimate next build based on time of last successful build, even though the last scheduled build failed.
            this.nextBuild.add({days: moment.duration(now - this.nextBuild).days() + 1});
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
        this.element.innerHTML = '<a onclick="location.reload()">Ready for launch</a>';
    }

    calculateDuration() {
        return moment.utc(moment.duration(this.nextBuild - moment()).asMilliseconds());
    }
}
