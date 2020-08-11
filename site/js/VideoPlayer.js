new class {
    constructor() {
        this.initContainer();
        this.initVideo();
        this.initVideoLinks();
        this.initKeyboard();
    }

    initContainer() {
        this.container = document.body.appendChild(document.createElement('div'));
        this.frame = this.container.appendChild(document.createElement('div'));
        this.title = this.frame.appendChild(document.createElement('h1'));
        this.video = this.frame.appendChild(document.createElement('video'));
        this.page = document.querySelector('#page');

        this.container.id = 'video-container';
        this.container.addEventListener('click', e => e.target === this.container && this.deactivate());
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
                // Ignore clicks bubbling up from other link elements.
                if (e.target.tagName === 'A' && e.target !== a) return;

                this.play(a.getAttribute('data-video'));
                this.title.innerHTML = a.getAttribute('data-title');

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
