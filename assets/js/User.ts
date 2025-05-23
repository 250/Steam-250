export default class User {
    static isLoggedIn() {
        return localStorage.hasOwnProperty('user') // S250.
            || localStorage.hasOwnProperty('whoami') // C250.
        ;
    }

    static logout() {
        document.body.insertAdjacentHTML('beforeend', `
            <form method="post" action="${process.env.CLUB_250_BASE_URL}/logout" name="logout">
                <input type="hidden" name="r" value="${location.origin + location.pathname}">
            </form>
        `);

        this.syncLogout();

        document.forms.namedItem('logout')!.submit();
    }

    static async syncLogin() {
        const response = await fetch(`${process.env.CLUB_250_BASE_URL}/api/whoami`, {credentials: 'include'});
        const body = await response.text();

        if (!response.ok) {
            return console.error('Login sync failed:', response.status, response.statusText, body);
        }

        const [userId, identity, noads, tier] = body.split("\0", 4);
        let avatar = identity.match(/^(?:\d+\/[\da-z]{40}\..{3}|[\da-z]{40})/)?.[0];

        localStorage.setItem('user', JSON.stringify({
            id: userId,
            avatar,
            name: avatar ? identity.substring(avatar.length) : 'ERROR',
            noads: noads === '1',
            tier: +tier,
        }));

        this.postClub250Message('login synced');

        S250.tryRemoveAds();
        this.syncLoginUi();
        this.syncGames();
    }

    static syncLogout() {
        localStorage.removeItem('user');

        this.postClub250Message('logout synced');
    }

    static syncGames() {
        this.postClub250Message('games');

        addEventListener('message', message => {
            if (message.origin !== process.env.CLUB_250_BASE_URL) return;

            console.debug('C250:', message.data);

            if (message.data.message === 'games') {
                localStorage.setItem('games', message.data.games);
                localStorage.setItem('games.date', message.data.modified);
            }
        });

        // Remove old games format by detecting date stamp.
        if (!localStorage.getItem('games.date')) {
            localStorage.removeItem('games');
        }
    }

    static syncLoginUi() {
        if (this.isLoggedIn()) {
            this.rewireTagLinks();
            this.hideObsoleteTiers();
        }

        if (S250.isClub250()) return;

        const userBar = document.getElementById('user');
        if (!userBar) return;

        const classes = userBar.classList;
        classes.remove('lin', 'lout');
        classes.add(this.isLoggedIn() ? 'lin' : 'lout');

        if (this.isLoggedIn()) {
            this.updateUserBar();
        }
    }

    static hideObsoleteTiers() {
        const userJson = localStorage.getItem('user');
        if (!userJson) return;

        const user = JSON.parse(userJson);

        if (user.tier) {
            document.querySelectorAll('ol.menu .micro.tier' + (S250.isClub250() ? '' : ', #body .micro.tier'))
                .forEach(e => {
                    const tier = +[...e.classList].filter(s => /^t\d$/.test(s))[0].substring(1);

                    user.tier >= tier && e.remove();
                })
            ;
        }
    }

    static rewireTagLinks() {
        const re = new RegExp(`^https://[^/]+/tag/`);

        document.querySelectorAll<HTMLAnchorElement>('a[data-id]').forEach(a => {
            if (re.test(a.href)) {
                a.href = `${process.env.CLUB_250_BASE_URL}/tag/${a.dataset.id}`;
            }
        });
    }

    private static updateUserBar() {
        const IMAGE_BASE_URL = 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images';

        const userJson = localStorage.getItem('user');
        if (!userJson) return;

        const user = JSON.parse(userJson);

        const a = document.querySelector<HTMLAnchorElement>('#lin .avatar');
        if (!a) return;
        a.href = `${process.env.CLUB_250_BASE_URL}/me`;

        const img = a.appendChild(document.createElement('img'));
        img.alt = img.title = user.name;
        if (user.avatar.includes('/')) {
            img.src = `${IMAGE_BASE_URL}/items/${user.avatar}`;
        } else {
            img.src = `${IMAGE_BASE_URL}/avatars/${user.avatar.substring(0, 2)}/${user.avatar}.jpg'`;
        }

        this.markOwnedGames();
    }

    private static markOwnedGames() {
        const
            gamesJson = localStorage.getItem('games'),
            gamesOwned = document.querySelector<HTMLElement>('#user .owned')
        ;
        if (!gamesJson || !gamesOwned) return;

        const
            games = JSON.parse(gamesJson),
            ranks = document.querySelectorAll<HTMLAnchorElement>('.main.ranking > div[id] > div:first-of-type > a')
        ;

        ranks.forEach(a => {
            const id = a.href.match(/\/app\/(\d+)/)![1];

            if (games.hasOwnProperty(id)) {
                a.classList.add('owned');
                a.setAttribute('data-content', this.formatTimePlayed(games[id]));
            }
        });

        const total = ranks.length;
        if (total) {
            const owned = document.querySelectorAll('.main.ranking .owned').length;

            gamesOwned.innerText = `${owned}/${total} (${Math.round(owned / total * 100)}%)`;
        } else {
            gamesOwned.closest('dl')!.remove();
        }
    }

    private static formatTimePlayed(minutes: number) {
        return minutes < 60 ? minutes + ' minute' + (minutes !== 1 ? 's' : '')
            : (minutes / 60).toFixed(1) + ' hour' + (minutes !== 60 ? 's' : '');
    }

    private static postClub250Message(message: any) {
        if (parent !== window) {
            parent.postMessage(message, process.env.CLUB_250_BASE_URL!);
        }
    }
};
