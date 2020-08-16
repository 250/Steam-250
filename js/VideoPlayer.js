new class {
    constructor() {
        this.initDom();
        this.initVideo();
        this.initVideoLinks();
        this.initKeyboard();
        this.initFooterScroll();
    }

    initDom() {
        this.container = document.body.appendChild(document.createElement('div'));
        this.frame = this.container.appendChild(document.createElement('div'));
        this.header = this.frame.appendChild(document.createElement('header'));
        this.video = this.frame.appendChild(document.createElement('video'));
        this.footer = this.frame.appendChild(document.createElement('footer'))
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
        this.video.addEventListener('loadedmetadata', _ => {
            // Prevent video resizing between loads.
            this.video.width = this.video.videoWidth;
            this.video.height = this.video.videoHeight;

            // Keep child elements in sync with video width.
            this.frame.style.maxWidth = this.video.videoWidth + 'px';
        });
    }

    initVideoLinks() {
        document.querySelectorAll('[data-video]').forEach(
            a => a.addEventListener('click', e => {
                // Ignore clicks bubbling up from other link elements.
                if (e.target.tagName === 'A' && e.target !== a) return;

                this.loadThumbs(a.getAttribute('data-video').split(','));

                this.header.innerHTML = a.getAttribute('data-title');
                if ('href' in a) this.header.innerHTML = `<a href="${a.href}">${this.header.innerHTML}</a>`;

                // Start playing first video with simulated click.
                this.footer.firstChild.click();

                e.stopPropagation();
                e.preventDefault();
            })
        );
    }

    initKeyboard() {
        document.addEventListener('keydown', e => e.key === 'Escape' && this.deactivate());
    }

    initFooterScroll() {
        this.footer.addEventListener(
            'mousemove', (e) => {
                const rect = this.footer.getBoundingClientRect();

                this.footer.scroll({
                    // 10% dead zone margin.
                    left: (this.footer.scrollWidth - rect.width) * ((e.clientX - rect.x) / rect.width * 1.2 - .1),
                    behavior: 'smooth',
                })
            }
        );
    }

    loadThumbs(videoIds) {
        while (this.footer.lastChild) {
            this.footer.removeChild(this.footer.lastChild);
        }

        for (let i = 0; i < videoIds.length; ++i) {
            const thumb = this.footer.appendChild(document.createElement('div'));
            thumb.setAttribute('data-index', i + 1);

            const img = thumb.appendChild(document.createElement('img'));
            img.src = `https://cdn.cloudflare.steamstatic.com/steam/apps/${videoIds[i]}/movie.184x123.jpg`;

            thumb.addEventListener('click', _ => {
                this.play(videoIds[i]);

                thumb.parentNode.querySelectorAll('img').forEach(img => img.classList.remove('active'));
                img.classList.add('active');

                this.header.setAttribute('data-video-id', i + 1);
                this.header.setAttribute('data-videos', videoIds.length);
            });
        }
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
