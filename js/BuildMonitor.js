class BuildMonitor {
    constructor(date, element) {
        this.nextBuild = date && moment(date).add({days: 1});
        this.element = element;
        this.blink = 0;

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

        let formattedDuration = duration.format('HH:mm.ss');

        this.element.innerHTML = 'Next update in ' + (
            (this.blink ^= 1)
                ? formattedDuration
                : formattedDuration.replace(/:/, ' ')
        );
    }

    showBuilding() {
        this.element.classList.add('ready');
        this.element.innerHTML = 'Building update...';
    }

    showReady() {
        this.element.classList.add('ready');
        this.element.innerHTML = '<a href="https://youtu.be/Mu0cE9RgK5M">Ready for launch</a>';
    }

    calculateDuration() {
        return moment(moment.duration(this.nextBuild - moment()).asMilliseconds());
    }
}
