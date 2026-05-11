import {parseParam} from './Query';
import User from './User';
import Checkbox from './Checkbox';
import chroma from 'chroma-js';
import posthog from 'posthog-js';
import VideoPlayer from './VideoPlayer';

type S250_static = typeof S250;

declare global {
    var S250: S250_static;
}

class S250 {
    private static player: VideoPlayer;

    constructor() {
        // Menu stuff.
        this.constrainDropdownMenuPositions();
        addEventListener('resize', _ => this.constrainDropdownMenuPositions())

        // UI stuff.
        S250.initRatingColourGradient();
        Checkbox.initCheckboxes();
        S250.initChevrons();
        this.initContextNav();
        this.initStatusLights();

        // User stuff.
        this.initLogInOut();
        User.syncLoginUi();
        S250.tryRemoveAds();

        // Hash stuff.
        this.scrollToCurrentHash();
        this.overrideHashChange();
        this.overrideFixedLinks();

        // Search stuff.
        this.initSearchValue();

        // Tracking stuff.
        this.initTracker();

        // Fancy stuff.
        S250.initRankingHoverItems();
        S250.player = new VideoPlayer();
    }

    initLogInOut() {
        const form = document.querySelector<HTMLFormElement>('#lout form');

        if (!form) {
            console.debug('Steam user area unavailable: skipped.');
            return;
        }

        // Redirect back to same page without query or hash.
        form['openid.realm'].value = process.env.CLUB_250_BASE_URL;
        form['openid.return_to'].value =
            `${process.env.CLUB_250_BASE_URL}/steam/login?r=${location.origin + location.pathname}`

        document.querySelector('#lout button')!.addEventListener('click', _ => localStorage.setItem('login', 'sync'));

        // Club 250 logout signal.
        if (location.hash === '#!') {
            User.syncLogout();
            history.replaceState(null, '', location.pathname + location.search);
            console.debug('Club 250 logout signal received.');
        }

        // Trigger login sync on post-back.
        if (localStorage.getItem('login') === 'sync') {
            localStorage.removeItem('login');
            User.syncLogin();
        }
    }

    /**
     * Ensure each drop-down menu is completely visible within the viewport.
     */
    constrainDropdownMenuPositions() {
        document.querySelectorAll<HTMLElement>('nav > ol > li > div').forEach(e => {
            const rect = e.getBoundingClientRect();

            if (rect.left < 0) {
                e.style.left = `calc(${getComputedStyle(e).left} - ${rect.left}px)`;
            }
            if (rect.right > document.documentElement.clientWidth) {
                e.style.left =
                    `calc(${getComputedStyle(e).left} - ${rect.right - document.documentElement.clientWidth}px)`;
            }
        });
    }

    scrollToCurrentHash() {
        addEventListener('load', _ => this.scrollToHash(location.hash));
    }

    overrideHashChange() {
        addEventListener('hashchange', _ => this.scrollToHash(location.hash));
    }

    /**
     * Prevent fixed links modifying hash.
     */
    overrideFixedLinks() {
        document.querySelectorAll<HTMLAnchorElement>('.fixedlinks a').forEach(a => {
            a.addEventListener('click', e => {
                this.scrollToHash(a.hash);

                e.preventDefault();
            })
        });
    }

    /**
     * Scrolls to an element taking into consideration the fixed navigation menu height.
     */
    scrollToHash(hash: string) {
        const HIGHLIGHT = 'highlight',
            games = document.querySelectorAll('.ranking [id]');

        games.forEach(e => e.classList.remove(HIGHLIGHT));

        if (!hash) return;

        const menuHeight = document.querySelector('nav')!.getBoundingClientRect().height,
            target = this.resolveHashTarget(hash);

        if (target) {
            let yOffset = target.getBoundingClientRect().top - menuHeight;

            // Highlight ranking element if it exists (will not exist on non-ranking pages).
            const ranking = document.querySelector('.applist, .main.ranking');
            if (ranking && ranking.contains(target)) {
                target.classList.add(HIGHLIGHT);

                // Will never be true for hash change events because browser has already scrolled.
                if (isInViewport(target)) {
                    // No need to scroll to visible element.
                    return;
                }

                // Place target element at 1/3rd viewport height instead of at the very top.
                yOffset += target.getBoundingClientRect().height / 2 - innerHeight / 3;
            }

            scrollTo(
                scrollX,
                scrollY + Math.ceil(yOffset)
            );
        }

        function isInViewport(elem: Element) {
            const rect = elem.getBoundingClientRect();

            return (
                rect.top >= menuHeight &&
                rect.left >= 0 &&
                rect.bottom <= innerHeight &&
                rect.right <= innerWidth
            );
        }
    }

    resolveHashTarget(hash: string) {
        // App tracking.
        if (hash.startsWith('#app/')) {
            let [, id, name] = hash.split('/', 3),
                a = document.querySelector(`.applist a[href$="/${id}"], .ranking a[href$="/${id}"]`);

            if (!a) {
                // TODO: Client flash message error.
                console.error(`Couldn't find game on this ranking: "${decodeURIComponent(name)}".`);

                return;
            }

            return a.closest('[id]');
        }

        return document.getElementById(hash.substring(1));
    }

    static isLoggedIn() {
        return User.isLoggedIn();
    }

    static isClub250() {
        return location.origin === process.env.CLUB_250_BASE_URL;
    }

    static syncLogin() {
        return User.syncLogin();
    }

    static syncLogout() {
        return User.syncLogout();
    }

    static syncGames() {
        User.syncGames();
    }

    static showAds() {
        const userJson = localStorage.getItem('user');

        if (userJson) {
            const user = JSON.parse(userJson);

            return !(user.hasOwnProperty('noads') && user.noads)
        }

        return true;
    }

    static tryRemoveAds() {
        if (!S250.showAds()) {
            document.querySelectorAll('ins').forEach(e => e.remove());
        }
    }

    initSearchValue() {
        const q = parseParam('q');

        if (q !== null) {
            document.querySelectorAll<HTMLInputElement>('input[name=q]').forEach(i => i.value = q.replace(/\+/g, ' '));
        }
    }

    initTracker() {
        process.env.NOTRACK === '' || posthog.init('phc_Rjmhrs656s6Say2ICy0A5xFi0HHGD38KTXxh1XI4ntD',
            {
                api_host: '//ph.steam250.com',
                person_profiles: 'always',
            },
        );
    }

    static initRankingHoverItems() {
        document.querySelectorAll<HTMLElement>('.compact.ranking li > .title').forEach(a => {
            a.querySelector(':scope > .title')?.remove();
            const shadow = a.appendChild(a.cloneNode(true)) as HTMLElement;

            // Prevent rapid re-entry when shape is clipped.
            shadow.style.pointerEvents = 'none';

            a.addEventListener('mouseenter', _ => {
                // Cancel any currently running animation on re-entry.
                shadow.classList.remove('animate');
                // Reflow hack to force immediate application.
                shadow.offsetWidth;

                shadow.classList.add('animate');
            })
            a.addEventListener('animationend', _ => {
                shadow.classList.remove('animate');
            })
        });
    }

    static initVideoLinks(links: NodeListOf<HTMLElement>) {
        S250.player.initVideoLinks(links);
    }

    private static initRatingColourGradient() {
        const grad = chroma.scale(['#da3e41', '#eab308', '#22c55e']).domain([0, 60, 100]);

        document.querySelectorAll<HTMLElement>('.rating').forEach(el => {
            const span = el.querySelector('span');

            el.style.color = grad(parseFloat(span?.style.width || el.textContent)).hex();
        });
    }

    // This is just to save generating superfluous markup.
    static initChevrons() {
        document.querySelectorAll('.more-button > span:last-of-type').forEach(span => {
            span.parentNode!.appendChild(span.cloneNode());
            span.parentNode!.appendChild(span.cloneNode());
        });
    }

    private initContextNav() {
        const nav = document.querySelector<HTMLElement>('aside > nav')!;
        const sel = nav?.querySelectorAll<HTMLElement>('.sel') || [];

        if (sel.length < 2) return;

        sel[0].insertAdjacentHTML('beforeend', `<span>${nav.dataset.ctx || sel[1].innerText}</span>`);

        // Scroll local nav to selected element.
        const container = sel[1].closest('ol')!;
        const elementRect = sel[1].getBoundingClientRect();
        const elementMid = elementRect.top - container.getBoundingClientRect().top
            + container.scrollTop + elementRect.height / 2;

        container.scrollTop = elementMid - container.clientHeight / 2;
    }

    private initStatusLights() {
        const statusBar = document.querySelector('#footer > :nth-child(3) > ol');
        const dataSnapshot = statusBar?.querySelector<HTMLElement>('li:nth-child(1)')!;
        const pageBuild = statusBar?.querySelector<HTMLElement>('li:nth-child(2)')!;
        const curatorSync = statusBar?.querySelector<HTMLElement>('li:nth-child(3)')!;

        if (!(statusBar && dataSnapshot && pageBuild && curatorSync)) return;

        const io = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    io.unobserve(entry.target);

                    fetchStatuses();
                }
            })
        }, {rootMargin: '50%'});
        io.observe(statusBar);

        function fetchStatuses() {
            fetch('https://dev.azure.com/ScriptFUSION/Steam%20250/_apis/build/builds'
                + '?definitions=1&$top=1&queryOrder=queueTimeDescending')
                .then(async response => {
                    const json = await response.json();
                    const {status, result} = json.value[0];

                    if (status === 'completed' && result === 'succeeded') {
                        dataSnapshot.classList.add('1');
                    } else if (status === 'inProgress') {
                        dataSnapshot.classList.add('2');
                    } else {
                        dataSnapshot.classList.add('3');
                    }
                });

            fetch('https://api.github.com/repos/250/Steam-250/actions/workflows/Build.yml/runs?per_page=1')
                .then(r => parseGitHubResponse(r, pageBuild));

            fetch('https://api.github.com/repos/250/Steam-curator/actions/workflows/Curator%20sync.yml/runs?per_page=1')
                .then(r => parseGitHubResponse(r, curatorSync));
        }

        async function parseGitHubResponse(response: Response, target: HTMLElement) {
            const json = await response.json();
            const {status, conclusion} = json.workflow_runs[0];

            if (status === 'completed' && conclusion === 'success') {
                target.classList.add('1');
            } else if (status === 'queued' || status === 'in_progress') {
                target.classList.add('2');
            } else {
                target.classList.add('3');
            }
        }
    }

    findSteamAppId(elem: HTMLElement) {
        const img = elem.querySelector<HTMLImageElement>('img[src]');

        if (img) {
            return img.src.match(/\/(\d+)\//)![1];
        }
    }

    findSteamAppName(elem: HTMLElement) {
        return elem.querySelector<HTMLElement>('.title > a')!.innerText;
    }

    copyToClipboard(text: string) {
        if (!document.queryCommandSupported || !document.queryCommandSupported('copy')) {
            alert('Clipboard is not supported in this browser!');
        }

        const textarea = document.createElement('textarea');

        textarea.textContent = text;
        // Prevent scrolling to bottom of page in Edge.
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            return document.execCommand('copy');
        } catch (e) {
            alert('Failed to copy clipboard data.');

            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

window.S250 = S250;
new S250;
