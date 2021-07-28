export default class User {
    static isLoggedIn() {
        return localStorage.hasOwnProperty('user');
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

        const [userId, rest] = body.split("\0", 2);

        localStorage.setItem('user', JSON.stringify({
            id: userId,
            name: rest.substring(40),
            avatar: rest.substring(0, 40),
        }));

        this.postClub250Message('login synced');

        this.syncLoginUi();
        this.syncGames();
    }

    static syncLogout() {
        localStorage.removeItem('user');

        this.postClub250Message('logout synced');
    }

    static async syncGames() {
        const userJson = localStorage.getItem('user');
        if (!userJson) return;

        const userId = JSON.parse(userJson).id;

        const data = await (await fetch(encodeURI(
            `https://cors.bridged.cc/https://steamcommunity.com/profiles/[U:1:${userId}]/games/?tab=all`,
        ))).text();

        const matches = data.match(/var rgGames = ([^\n]+);/);

        if (!matches || matches.length !== 2) {
            alert('Unable to load your profile. This is usually because your Steam profile is not public.\n'
                + 'Try setting your Steam Community profile visibility to public, then refresh this page to '
                + 'try again.');

            return;
        }

        const games = JSON.parse(matches[1]).reduce(
            (games: {[key: string]: any}, game: {[key: string]: any}) => {
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

        this.markOwnedGames();
    }

    static syncLoginUi() {
        const userBar = document.getElementById('user');
        if (!userBar) return;

        const classes = userBar.classList;
        classes.remove('lin', 'lout');
        classes.add(this.isLoggedIn() ? 'lin' : 'lout');

        this.isLoggedIn() && this.updateUserBar();
    }

    private static updateUserBar() {
        const userJson = localStorage.getItem('user');
        if (!userJson) return;

        const user = JSON.parse(userJson);

        const a = document.querySelector<HTMLAnchorElement>('#lin .avatar');
        if (!a) return;

        a.href = `https://steamcommunity.com/profiles/[U:1:${user.id}]/`;

        const img = a.appendChild(document.createElement('img'));
        img.alt = img.title = user.name;
        img.src = 'https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/'
            + `${user.avatar.substring(0, 2)}/${user.avatar}.jpg'`;

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
            ranks = document.querySelectorAll<HTMLAnchorElement>('.ranking > div[id] > div:first-of-type > a')
        ;

        ranks.forEach(a => {
            const id = a.href.match(/\/app\/(\d+)/)![1];

            if (games.hasOwnProperty(id)) {
                a.classList.add('owned');
                a.setAttribute('data-content', games[id] + ' hours');
            }
        });

        const current = document.querySelectorAll('.ranking .owned').length, max = ranks.length;
        gamesOwned.innerText = max ? `${current}/${max} (${Math.round(current / max * 100)}%)` : 'n/a';
    }

    private static postClub250Message(message: any) {
        if (parent !== window) {
            parent.postMessage(message, process.env.CLUB_250_BASE_URL!);
        }
    }
};
