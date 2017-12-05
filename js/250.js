new class {
    constructor() {
        this.initLogInOut();
        this.syncLogInOutState();
        this.tryParseOpenIdPostback();
        this.startCountdown();
        this.loadDisqus();
    }

    initLogInOut() {
        let form = document.querySelector('#lout form');
        form['openid.return_to'].value = this.isLocal() ? 'http://steam250.com' : location.href;

        document.querySelector('#lin button').addEventListener('click', () => this.logout());
    }

    syncLogInOutState() {
        let classes = document.getElementById('user').classList;

        classes.remove('lin', 'lout');
        classes.add(this.isLoggedIn() ? 'lin' : 'lout');

        if (this.isLoggedIn()) {
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

        document.querySelector('#user .owned').innerText =
            document.querySelectorAll('#ranking .owned').length
            + '/'
            + document.querySelectorAll('#ranking .title').length
        ;
    }

    updateUserBar() {
        let steam = JSON.parse(localStorage.getItem('steam'));

        let a = document.querySelector('#lin .avatar');
        a.href = `http://steamcommunity.com/profiles/${steam.id}/`;

        let img = document.createElement('img');
        img.src = steam.avatar;
        a.appendChild(img);
    }

    isLoggedIn() {
        return localStorage.hasOwnProperty('steam') && localStorage.hasOwnProperty('games');
    }

    isLocal() {
        return !location.host;
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
                        games[game['appid']] = game['hours_forever'];

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
            }
        );
    }

    startCountdown() {
        let div = document.createElement('div');
        div.classList.add('countdown');
        div.innerHTML = 'Initializing...';

        document.getElementById('header').appendChild(div);

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
            date => {
                let nextBuild = moment(date).add({days: 1}), blink = 0;

                let timer = setInterval(() => {
                    let duration = moment(moment.duration(nextBuild - moment()).asMilliseconds());

                    if (duration < 0) {
                        clearInterval(timer);

                        div.classList.add('ready');

                        return div.innerHTML = '<a href="https://youtu.be/Mu0cE9RgK5M">Ready for launch</a>';
                    }

                    let formattedDuration = duration.format('HH:mm.ss');

                    div.innerText = 'Next update ' + (
                        (blink ^= 1)
                            ? formattedDuration
                            : formattedDuration.replace(/:/, ' ')
                    );
                }, 500)
            }
        );
    }

    loadDisqus() {
        if (!location.host) return;

        let d = document, s = d.createElement('script');
        s.src = 'https://steam250.disqus.com/embed.js';
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    }

    parseParam(name) {
        let match = RegExp('[?&]' + name + '=([^&]*)').exec(location.search);

        return match && decodeURIComponent(match[1]);
    }
};
