new class {
    constructor() {
        this.container = document.body.appendChild(document.createElement('div'));
        this.video = this.container.appendChild(document.createElement('video'));
        this.page = document.querySelector('#page');

        this.deactivate();
        this.init();
    }

    init() {
        this.initContainer();
        this.initVideo();
        this.initVideoLinks();
        this.initKeyboard();

        document.addEventListener('click', _ => this.deactivate());
    }

    initContainer() {
        this.container.id = 'video-player';
    }

    initVideo() {
        this.video.controls = this.video.autoplay = true;

        const volume = this.loadVolumeState();
        this.video.volume = volume.volume;
        this.video.muted = volume.muted;

        this.video.addEventListener('volumechange', _ => this.saveVolumeState());
        this.video.addEventListener('click', e => e.stopPropagation());
    }

    initVideoLinks() {
        document.querySelectorAll('[data-video]').forEach(
            a => a.addEventListener('click', e => {
                this.play(a.getAttribute('data-video'));

                e.stopPropagation();
                e.preventDefault();
            })
        );
    }

    initKeyboard() {
        document.addEventListener('keydown', e => e.key === 'Escape' && this.deactivate());
    }

    loadVolumeState() {
        return localStorage.hasOwnProperty('video.volume')
            ? JSON.parse(localStorage.getItem('video.volume'))
            : {
                volume: .5,
                muted: false,
            }
        ;
    }

    saveVolumeState() {
        localStorage.setItem(
            'video.volume',
            JSON.stringify({
                volume: this.video.volume,
                muted: this.video.muted,
            })
        );
    }

    activate() {
        this.container.classList.add('active');
        this.page.classList.add('video');
    }

    deactivate() {
        this.video.pause();

        this.container.classList.remove('active');
        this.page.classList.remove('video');
    }

    play(id) {
        while (this.video.lastChild) {
            this.video.removeChild(this.video.lastChild);
        }

        const webm = this.video.appendChild(document.createElement('source'));
        webm.src = `https://steamcdn-a.akamaihd.net/steam/apps/${id}/movie480.webm`;
        webm.type = 'video/webm';

        const mp4 = this.video.appendChild(document.createElement('source'));
        mp4.src = `https://steamcdn-a.akamaihd.net/steam/apps/${id}/movie480.mp4`;
        mp4.type = 'video/mp4';

        this.activate();
        this.video.load();
    }
};
