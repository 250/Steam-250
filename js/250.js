class S250 {
    constructor() {
        this.initLogInOut();
        this.initMenuScrollbars();
        this.syncLogInOutState();
        this.tryParseOpenIdPostback();
        this.startCountdown();
    }

    initLogInOut() {
        let form = document.querySelector('#lout form');

        // Redirect back to same page without query or hash.
        form['openid.return_to'].value = location.origin + location.pathname;

        document.querySelector('#lin button').addEventListener('click', () => this.logout());
    }

    /**
     * Apply transitioning (t11g) class whilst menu is opening or closing to prevent scrollbars during this state.
     */
    initMenuScrollbars() {
        const T11G = 't11g';

        // transitionstart event emulation for Chrome.
        document.querySelectorAll('ol.menu > li').forEach(e => {
            const ol = e.querySelector('ol');

            e.addEventListener('mouseenter', () => ol.clientHeight === 0 && ol.classList.add(T11G));
            e.addEventListener('mouseleave', () => ol.classList.add(T11G));
        });

        document.querySelectorAll('ol.menu > li ol').forEach(e => {
            e.addEventListener('transitionend', () => e.classList.remove(T11G));

            // Prevent window scrolling whilst over submenu.
            e.addEventListener('wheel', (event) => {
                if (e.clientHeight + e.scrollTop + event.deltaY > e.scrollHeight) {
                    e.scrollTop = e.scrollHeight;

                    event.preventDefault();
                } else if (e.scrollTop + event.deltaY < 0) {
                    e.scrollTop = 0;

                    event.preventDefault();
                }
            });
        });
    }

    syncLogInOutState() {
        let classes = document.getElementById('user').classList;

        classes.remove('lin', 'lout');
        classes.add(S250.isLoggedIn() ? 'lin' : 'lout');

        if (S250.isLoggedIn()) {
            this.markOwnedGames();
            this.updateUserBar();
        }
    }

    markOwnedGames() {
        let games = JSON.parse(localStorage.getItem('games'));

        document.querySelectorAll('#ranking > tbody > tr > td:first-of-type > a').forEach(a => {
            let id = a.href.match(/(\d+)\/?$/)[1];

            if (games.hasOwnProperty(id)) {
                a.classList.add('owned');
                a.setAttribute('data-content', games[id] + ' hours');
            }
        });

        let current, max;
        document.querySelector('#user .owned').innerText =
            (current = document.querySelectorAll('#ranking .owned').length)
            + '/'
            + (max = document.querySelectorAll('#ranking .title').length)
            + ' ('
            + Math.round(current / max * 100)
            + '%)'
        ;
    }

    updateUserBar() {
        let steam = JSON.parse(localStorage.getItem('steam'));

        let a = document.querySelector('#lin .avatar');
        a.href = `http://steamcommunity.com/profiles/${steam.id}/`;
        a.title = steam.name;

        let img = document.createElement('img');
        img.src = steam.avatar;
        a.appendChild(img);
    }

    static isLoggedIn() {
        return localStorage.hasOwnProperty('steam') && localStorage.hasOwnProperty('games');
    }

    logout() {
        localStorage.removeItem('steam');
        localStorage.removeItem('games');

        this.syncLogInOutState();
    }

    tryParseOpenIdPostback() {
        let claimdId = this.parseParam('openid.claimed_id');

        if (!claimdId) return;

        let userId = claimdId.replace(/.*\//, '');

        fetch(
            `http://cors-anywhere.herokuapp.com/http://steamcommunity.com/profiles/${userId}/games/?tab=all`,
        ).then(
            response => response.text()
        ).then((data) => {
            let matches = data.match(/var rgGames = ([^\n]+);/);

            if (!matches || matches.length !== 2) {
                console.error('Failed to find games JSON.');

                return;
            }

            let games = JSON.parse(matches[1]).reduce(
                (games, game) => {
                    games[game['appid']] = game['hours_forever'] || 0;

                    return games;
                },
                {}
            );

            localStorage.setItem('games', JSON.stringify(games));

            let dom = new DOMParser().parseFromString(data, 'text/html');
            localStorage.setItem('steam', JSON.stringify({
                id: userId,
                name: dom.querySelector('.profile_small_header_name').innerText,
                avatar: dom.querySelector('.playerAvatar > img')['src'].replace('_medium', ''),
            }));

            location.replace(location.pathname);
        });
    }

    startCountdown() {
        let element = BuildMonitor.createElement();

        fetch(
            'https://api.travis-ci.org/repo/15937062/builds?event_type=cron&limit=1',
            {
                headers: {
                    'Travis-API-Version': 3,
                },
            }
        ).then(
            response => response.json().then(
                data => data.builds[0].finished_at
            )
        ).then(
            date => new BuildMonitor(date, element)
        );
    }

    parseParam(name) {
        let match = RegExp('[?&]' + name + '=([^&]*)').exec(location.search);

        return match && decodeURIComponent(match[1]);
    }
} new S250;
