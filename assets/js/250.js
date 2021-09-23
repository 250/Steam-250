import {parseParam} from './Query';
import User from './User';

class S250 {
    constructor() {
        // Menu stuff.
        this.initMenuScrollbarTransitions();
        this.constrainDropdownMenuPositions();

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

        // Fancy stuff.
        this.initAppLinkMenu();
        this.initRankingHoverItems();
    }

    initLogInOut() {
        const form = document.querySelector('#lout form');

        if (!form) {
            console.debug('Steam user area unavailable: skipped.');
            return;
        }

        // Redirect back to same page without query or hash.
        form['openid.realm'].value = process.env.CLUB_250_BASE_URL;
        form['openid.return_to'].value =
            `${process.env.CLUB_250_BASE_URL}/steam/login?r=${location.origin + location.pathname}`

        document.querySelector('#lout button').addEventListener('click', _ => localStorage.setItem('login', 'sync'));
        document.querySelector('#lin button').addEventListener('click', _ => User.logout());

        // Trigger login sync on post-back.
        if (localStorage.getItem('login') === 'sync') {
            localStorage.removeItem('login');
            User.syncLogin();
        }
    }

    /**
     * Apply transitioning (t11g) class whilst menu is opening or closing to prevent scrollbars during this state.
     */
    initMenuScrollbarTransitions() {
        const T11G = 't11g';

        // transitionstart event emulation for Chrome.
        document.querySelectorAll('ol.menu li').forEach(e => {
            const ol = e.querySelector('ol');
            if (!ol) return;

            e.addEventListener('mouseenter', _ => ol.clientHeight === 0 && ol.classList.add(T11G));
            e.addEventListener('mouseleave', _ => ol.classList.add(T11G));
        });

        document.querySelectorAll('ol.menu > li ol').forEach(e => {
            e.addEventListener('transitionend', _ => e.classList.remove(T11G));

            // Prevent window scrolling whilst over submenu.
            e.addEventListener('wheel', (event) => {
                if (e.clientHeight + e.scrollTop + event.deltaY > e.scrollHeight) {
                    e.scrollTop = e.scrollHeight;

                    event.preventDefault();
                } else if (e.scrollTop + event.deltaY < 0) {
                    e.scrollTop = 0;

                    event.preventDefault();
                }

                // Forbid parent from handling event.
                event.stopPropagation();
            });
        });
    }

    /**
     * Ensure each drop-down menu is completely visible within the viewport.
     */
    constrainDropdownMenuPositions() {
        document.querySelectorAll('ol.menu > li > ol').forEach(e => {
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
        document.querySelectorAll('.fixedlinks a').forEach(a => {
            a.addEventListener('click', e => {
                this.scrollToHash(a.hash);

                e.preventDefault();
            })
        });
    }

    /**
     * Scrolls to an element taking into consideration the fixed navigation menu height.
     */
    scrollToHash(hash) {
        const HIGHLIGHT = 'highlight',
            games = document.querySelectorAll('.ranking [id]');

        games.forEach(e => e.classList.remove(HIGHLIGHT));

        if (!hash) return;

        const menuHeight = document.querySelector('ol.menu').getBoundingClientRect().height,
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
                pageXOffset,
                pageYOffset + Math.ceil(yOffset)
            );
        }

        function isInViewport(elem) {
            const rect = elem.getBoundingClientRect();

            return (
                rect.top >= menuHeight &&
                rect.left >= 0 &&
                rect.bottom <= innerHeight &&
                rect.right <= innerWidth
            );
        }
    }

    resolveHashTarget(hash) {
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

        return document.getElementById(hash.substr(1));
    }

    static isLoggedIn() {
        return User.isLoggedIn();
    }

    static syncLogin() {
        return User.syncLogin();
    }

    static syncLogout() {
        return User.syncLogout();
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
            document.querySelectorAll('input[name=q]').forEach(i => i.value = q.replace(/\+/g, ' '));
        }
    }

    initAppLinkMenu() {
        const menu = document.getElementById('linkmenu'),
            ACTIVE = 'show';

        let link;

        document.querySelectorAll('.ranking .links').forEach(a => {
            a.addEventListener('click', e => {
                menu.style.top = a.offsetTop + a.offsetHeight + 5 + 'px';
                menu.style.left = a.offsetLeft + 'px';
                menu.querySelector('a:first-of-type > span').innerHTML = a.closest('[id]').id;

                menu.classList.toggle(ACTIVE, link !== a ? true : undefined);
                a.classList.toggle(ACTIVE, menu.classList.contains(ACTIVE));

                link = a;

                e.preventDefault();
            });

            a.addEventListener('blur', _ => {
                menu.classList.remove(ACTIVE);
                a.classList.remove(ACTIVE);
            });
        });

        document.querySelectorAll('#linkmenu a').forEach(a => {
            a.addEventListener('click', _ => {
                if (a.classList.contains('cp')) {
                    if (a.classList.contains('rank')) {
                        this.copyToClipboard(link.href);
                    }

                    if (a.classList.contains('app')) {
                        const id = this.findSteamAppId(link.closest('[id]')),
                            name = encodeURIComponent(this.findSteamAppName(link.closest('div')));

                        this.copyToClipboard(`${location.origin}${location.pathname}#app/${id}/${name}`)
                    }
                }
            });
        });
    }

    initRankingHoverItems() {
        document.querySelectorAll('.compact.ranking li > .title').forEach(a => {
            const shadow = a.appendChild(a.cloneNode(true));

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

    findSteamAppId(elem) {
        const img = elem.querySelector('img[src]');

        if (img) {
            return img.src.match(/\/(\d+)\//)[1];
        }
    }

    findSteamAppName(elem) {
        return elem.querySelector('.title > a').innerText;
    }

    copyToClipboard(text) {
        if (window.clipboardData && window.clipboardData.setData) {
            // IE specific code path to prevent textarea being shown while dialog is visible.
            return clipboardData.setData('Text', text);
        } else if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
            const textarea = document.createElement('textarea');

            textarea.textContent = text;
            // Prevent scrolling to bottom of page in Edge.
            textarea.style.position = 'fixed';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                return document.execCommand('copy');
            } catch (e) {
                // TODO: Client error message.
                console.debug('Failed to copy clipboard data.');

                return false;
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }
} new S250;
window.S250 = S250;
