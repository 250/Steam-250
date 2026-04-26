export default class User {
    static isLoggedIn() {
        return localStorage.hasOwnProperty('user') // S250.
            || localStorage.hasOwnProperty('whoami') // C250.
        ;
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
            this.rewriteLinks();
            this.hideObsoleteTiers();
        }

        if (S250.isClub250()) return;

        const userBar = document.querySelector('#header > :last-child');
        if (!userBar) return;

        const classes = userBar.classList;
        classes.remove('lin', 'lout');
        classes.add(this.isLoggedIn() ? 'lin' : 'lout');

        if (this.isLoggedIn()) {
            this.updateUserBar();
        }
    }

    static hideObsoleteTiers() {
        const user = this.fetchUser();

        if (user.tier) {
            document.querySelectorAll('nav .micro.tier' + (S250.isClub250() ? '' : ', #body .micro.tier'))
                .forEach(e => {
                    const tier = +[...e.classList].filter(s => /^t\d$/.test(s))[0].substring(1);

                    user.tier >= tier && e.remove();
                })
            ;
        }
    }

    static rewriteLinks() {
        const user = this.fetchUser();

        if (!user.tier) return;

        this.rewriteTagLinks();
        this.rewriteRankingLinks();
    }

    private static rewriteTagLinks() {
        document.querySelectorAll<HTMLAnchorElement>('a[data-id]').forEach(a => {
            if (new RegExp('^https://[^/]+/tag/').test(a.href)) {
                a.href = `${process.env.CLUB_250_BASE_URL}/tag/${a.dataset.id}`;
            }
        });
    }

    private static rewriteRankingLinks() {
        document.querySelectorAll<HTMLAnchorElement>('a[href*="/top250"], a[href*="/hidden_gems"]').forEach(a => {
            const match = new RegExp('^https://[^/]+/(top250|hidden_gems)\\b').exec(a.href);

            if (match) {
                a.href = `${process.env.CLUB_250_BASE_URL}/ranking/${match[1] === 'top250' ? '250' : 'gems'}`;
            }
        });
    }

    private static updateUserBar() {
        const IMAGE_BASE_URL = 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images';

        const user = this.fetchUser();

        const a = document.querySelector<HTMLAnchorElement>('#header .user-avatar');
        if (!a) return;
        a.href = `${process.env.CLUB_250_BASE_URL}/me`;

        const src = user.avatar.includes('/')
            ? `${IMAGE_BASE_URL}/items/${user.avatar}`
            : `${IMAGE_BASE_URL}/avatars/${user.avatar.substring(0, 2)}/${user.avatar}_full.jpg'`;

        a.insertAdjacentHTML(
            'afterbegin',
            `<span title="${user.name}'s profile"><img alt="${user.name}" src="${src}"></span>`
        );
    }

    private static fetchUser() {
        const userJson = localStorage.getItem('user');
        if (!userJson) return;

        return JSON.parse(userJson);
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
