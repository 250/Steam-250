class BuildMonitor {
    constructor(date, element) {
        this.nextBuild = date && moment.utc(date).add({days: 1});
        this.element = element;
        this.blink = 0;

        this.monitor();
    }

    static createElement() {
        return element = document.createElement('div');
        element.classList.add('countdown');
        element.innerHTML = 'Initializing...';

        document.querySelector('.body').appendChild(element);

        return element;
    }

    monitor() {
        if (!this.nextBuild) {
            return this.showBuilding();
        }

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
        this.element.innerHTML = 'Building update<span>.</span><span>.</span><span>.</span>';
    }

    showReady() {
        this.element.classList.add('ready');
        this.element.innerHTML = '<a href="https://youtu.be/Mu0cE9RgK5M">Ready for launch</a>';
    }

    calculateDuration() {
        return moment.utc(moment.duration(this.nextBuild - moment.utc()).asMilliseconds());
    }
}
