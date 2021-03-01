class S250 {
    constructor() {
        // Menu stuff.
        this.initMenuScrollbarTransitions();
        this.constrainDropdownMenuPositions();

        // User stuff.
        this.tryParseOpenIdPostback();
        this.initLogInOut();
        this.syncLogInOutState();

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
        form['openid.return_to'].value = location.origin + location.pathname;

        document.querySelector('#lin button').addEventListener('click', _ => this.logout());
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

            // Highlight ranking element if it exists.
            const ranking = document.querySelector('.ranking');
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
                a = document.querySelector(`.ranking a[href$="/${id}"]`);

            if (!a) {
                // TODO: Client flash message error.
                console.error(`Couldn't find game on this ranking: "${decodeURIComponent(name)}".`);

                return;
            }

            return a.closest('[id]');
        }

        return document.getElementById(hash.substr(1));
    }

    syncLogInOutState() {
        const classes = document.getElementById('user').classList;

        classes.remove('lin', 'lout');
        classes.add(S250.isLoggedIn() ? 'lin' : 'lout');

        if (S250.isLoggedIn()) {
            this.markOwnedGames();
            this.updateUserBar();
        }
    }

    markOwnedGames() {
        const
            games = JSON.parse(localStorage.getItem('games')),
            ranks = document.querySelectorAll('.ranking > div[id] > div:first-of-type > a')
        ;

        ranks.forEach(a => {
            const id = a.href.match(/\/app\/(\d+)/)[1];

            if (games.hasOwnProperty(id)) {
                a.classList.add('owned');
                a.setAttribute('data-content', games[id] + ' hours');
            }
        });

        let current, max;
        document.querySelector('#user .owned').innerText =
            ranks.length ?
                (current = document.querySelectorAll('.ranking .owned').length)
                + '/'
                + (max = ranks.length)
                + ' ('
                + Math.round(current / max * 100)
                + '%)'
            : 'n/a'
        ;
    }

    unmarkOwnedGames() {
        document.querySelectorAll('.ranking a.owned').forEach(a => {
            a.classList.remove('owned');
        });
    }

    updateUserBar() {
        const steam = JSON.parse(localStorage.getItem('steam'));

        const a = document.querySelector('#lin .avatar');
        a.href = `http://steamcommunity.com/profiles/${steam.id}/`;
        a.title = steam.name;

        const img = document.createElement('img');
        img.src = steam.avatar;
        a.appendChild(img);
    }

    static isLoggedIn() {
        return localStorage.hasOwnProperty('steam') && localStorage.hasOwnProperty('games');
    }

    logout() {
        localStorage.removeItem('steam');
        localStorage.removeItem('games');

        this.unmarkOwnedGames();
        this.syncLogInOutState();
    }

    tryParseOpenIdPostback() {
        const claimdId = this.parseParam('openid.claimed_id');

        if (!claimdId) return;

        const userId = claimdId.replace(/.*\//, '');

        fetch(
            `https://cors.bridged.cc/https://steamcommunity.com/profiles/${userId}/games/?tab=all`,
        ).then(
            response => response.text()
        ).then((data) => {
            const matches = data.match(/var rgGames = ([^\n]+);/);

            if (!matches || matches.length !== 2) {
                alert('Unable to load your profile. This is usually because your Steam profile is not public.\n'
                    + 'Try setting your Steam Community profile visibility to public, then refresh this page to '
                    + 'try again.');

                return;
            }

            const games = JSON.parse(matches[1]).reduce(
                (games, game) => {
                    games[game['appid']] = game['hours_forever'] || 0;

                    return games;
                },
                {}
            );

            if (!Object.keys(games).length) {
                alert('No games found in your account! This is usually because your game details are not public.\n'
                    + 'Try setting your game details to public on your Steam Community privacy settings page, then '
                    + 'refresh this page to try again.');

                return;
            }

            localStorage.setItem('games', JSON.stringify(games));

            const dom = new DOMParser().parseFromString(data, 'text/html');
            localStorage.setItem('steam', JSON.stringify({
                id: userId,
                name: dom.querySelector('.profile_small_header_name').innerText.trim(),
                avatar: dom.querySelector('.playerAvatar > img')['src'].replace('_medium', ''),
            }));

            location.replace(location.pathname);
        });
    }

    initSearchValue() {
        const q = this.parseParam('q');

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

    parseParam(name) {
        const match = RegExp('[?&]' + name + '=([^&]*)').exec(location.search);

        return match && decodeURIComponent(match[1]);
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

                return false;
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }
} new S250;
