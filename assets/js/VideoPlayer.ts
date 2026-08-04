import shaka from 'shaka-player';

export default class {
    private container!: HTMLDivElement;
    private frame!: HTMLDivElement;
    private header!: HTMLElement;
    private video!: HTMLVideoElement;
    private footer!: HTMLElement;
    private page!: Element;
    private player?: shaka.Player;

    constructor() {
        if (this.initDom() !== false) {
            this.initVideo();
            this.initVideoLinks();
            this.initKeyboard();
            this.initFooterScroll();
        }
    }

    initDom() {
        if (!document.body) {
            console.debug("Video player aborted: body not ready.");
            return false;
        }

        this.container = document.body.appendChild(document.createElement('div'));
        this.frame = this.container.appendChild(document.createElement('div'));
        this.header = this.frame.appendChild(document.createElement('header'));
        this.video = this.frame.appendChild(document.createElement('video'));
        this.footer = this.frame.appendChild(document.createElement('footer'))
        this.page = document.querySelector('#page')!;

        this.container.id = 'video-container';
        this.container.addEventListener('click', e => e.target === this.container && this.deactivate());
    }

    initVideo() {
        this.video.controls = this.video.autoplay = true;

        const volume = this.loadVolumeState();
        this.video.volume = volume.volume;
        this.video.muted = volume.muted;

        this.video.addEventListener('volumechange', _ => this.saveVolumeState());
        this.video.addEventListener('resize', _ => {
            // Restore video dimensions to auto.
            this.video.removeAttribute('width');
            this.video.removeAttribute('height');

            // Keep child elements in sync with video width.
            this.syncFooterSize();
        });
        addEventListener('resize', () => this.syncFooterSize());
    }

    syncFooterSize() {
        this.footer.style.maxWidth = `${this.video.clientWidth}px`;
    }

    initVideoLinks(links = document.querySelectorAll<HTMLElement>('[data-video]')) {
        links.forEach(
            a => a.addEventListener('click', e => {
                // Ignore clicks bubbling up from other link elements or their children.
                const link = e.target instanceof Element ? e.target.closest('a') : null;
                if (link && link !== a) return;

                this.loadThumbs(
                    ('href' in a ? <string>a.href : location.pathname).match(/\/app\/(\d+)/)![1],
                    a.getAttribute('data-video')!.split(','),
                    a.getAttribute('data-hx')!.split(','),
                );

                this.header.innerHTML = a.getAttribute('data-title')!;
                if ('href' in a) this.header.innerHTML = `<a href="${a.href}">${this.header.innerHTML}</a>`;

                // Start playing first video with simulated click.
                this.footer.firstChild!.dispatchEvent(new Event('click'));

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
                    // Smooth scrolling is terrible in Chrome because of a fixed delay before scrolling starts.
                    behavior: 'InstallTrigger' in window ? 'smooth' : 'auto',
                })
            }
        );
    }

    loadThumbs(appId: string, videoIds: string[], videoHashes: string[]) {
        while (this.footer.lastChild) {
            this.footer.removeChild(this.footer.lastChild);
        }

        for (let i = 0; i < videoIds.length; ++i) {
            const thumb = this.footer.appendChild(document.createElement('div'));
            thumb.setAttribute('data-index', String(i + 1));

            let videoId = videoIds[i], thumbHash = '';
            if (videoId.length > 40) {
                thumbHash = videoId.substring(0, 40);
                videoId = videoId.substring(40);
            }

            const img = thumb.appendChild(document.createElement('img'));
            img.src = `https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/${videoId}/${thumbHash}/`
                + (thumbHash ? 'movie_232x130.jpg' : 'movie.184x123.jpg');

            thumb.addEventListener('click', _ => {
                this.play(appId, videoHashes[i]);

                thumb.parentNode!.querySelectorAll('img').forEach(img => img.classList.remove('active'));
                img.classList.add('active');

                this.header.setAttribute('data-video-id', String(i + 1));
                this.header.setAttribute('data-videos', videoIds.length.toString());
            });
        }
    }

    loadVolumeState() {
        return localStorage.hasOwnProperty('video.volume')
            ? JSON.parse(localStorage.getItem('video.volume')!)
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

    async play(appId: string, hash: string) {
        this.player || shaka.polyfill.installAll();
        const player: shaka.Player = this.player ||= new shaka.Player();
        await player.attach(this.video);

        // Don't pick a resolution beyond the screen size.
        player.configure('abr.restrictToScreenSize', true);
        // Assume we can handle the highest bitrate available until we know better.
        player.configure('abr.defaultBandwidthEstimate', Infinity);

        player.addEventListener('adaptation', (event: any) => this.header.dataset.res = event.newTrack.height + 'p');

        // Prevent video resizing between loads because it looks glitchy. Restored when next video loads.
        this.video.width = this.video.clientWidth;
        this.video.height = this.video.clientHeight;

        await this.loadCompatibleMedia(player, appId, hash);

        this.activate();
    }

    async loadCompatibleMedia(player: shaka.Player, appId: string, hash: string) {
        const CDN = 'https://video.akamai.steamstatic.com/store_trailers';
        const base = `${CDN}/${appId}/${hash}`;

        // AV1 Main Profile Level 9.0 8-bit ≈ 4K@60fps.
        if (MediaSource.isTypeSupported('video/mp4; codecs="av01.0.09M.08"')) {
            return await player.load(`${base}/dash_av1.mpd`);
        }

        // H.264 High Profile Level 4.1 = 1080p@60fps.
        if (MediaSource.isTypeSupported('video/mp4; codecs="avc1.640029"')) {
            return await player.load(`${base}/dash_h264.mpd`);
        }

        return await player.load(`${base}/hls_264_master.m3u8`);
    }
};
