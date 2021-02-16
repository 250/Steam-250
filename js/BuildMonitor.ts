import dayjs from 'dayjs';
import {default as duration} from 'dayjs/plugin/duration';

dayjs.extend(duration);

export default class BuildMonitor {
    private element = BuildMonitor.createElement();
    private blink = 0;
    private nextBuild?: dayjs.Dayjs;
    private timer?: number;

    start(date: string) {
        // Build is pending. TODO: Validate this works with GitHub Actions API.
        if (!date) return this.showBuilding();

        this.nextBuild = dayjs(date).add(1, 'day');
        const now = dayjs();

        // Build is overdue.
        if (this.nextBuild <= now) {
            // Estimate next build based on time of last successful build, even though the last scheduled build failed.
            this.nextBuild.add(dayjs.duration(now.diff(this.nextBuild)).days() + 1, 'days');
        }

        // Time remains on the clock.
        this.monitor();
    }

    static createElement() {
        const element = document.createElement('div');
        element.classList.add('countdown');
        element.innerHTML = 'Initializing...';

        document.querySelector('.body')!.appendChild(element);

        return element;
    }

    private monitor() {
        this.timer = window.setInterval(() => this.showNextUpdate(), 500);
        this.showNextUpdate();
    }

    private showNextUpdate() {
        const duration = this.calculateDuration();

        if (duration.asMilliseconds() <= 0) {
            clearInterval(this.timer);

            return this.showReady();
        }

        const formattedDuration = duration.format('[<span>]HH:mm.ss[</span>]');

        this.element.innerHTML = 'Next update in ' + (
            (this.blink ^= 1)
                ? formattedDuration
                : formattedDuration.replace(/:/, ' ')
        );
    }

    private showBuilding() {
        this.element.classList.add('building');
        this.element.innerHTML = 'Building update' + '<span>.</span>'.repeat(3);
    }

    private showReady() {
        this.element.classList.add('ready');
        this.element.innerHTML = '<a onclick="location.reload()">Ready for launch</a>';
    }

    private calculateDuration() {
        return dayjs.duration(this.nextBuild!.diff(dayjs()));
    }
}
