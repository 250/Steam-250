const
    BASE_URL = 'http://steam250.com/',
    PAGES = [
        'index.html',
        'hidden_gems.html',
        'most_played.html',
        'bottom100.html',
        'vr250.html',
    ]
;

let webpage = require('webpage').create();

webpage.viewportSize = {width: 1160, height: screen.height};

(async () => {
    for (let page of PAGES) {
        await webpage.open(BASE_URL + page, () => {
            webpage.evaluate(() => {
                // Remove ads.
                document.querySelectorAll('ins').forEach(e => e.remove());

                // Hide fixed links.
                document.querySelector('.fixedlinks').style.display = 'none';
            });

            webpage.render(`${page}.png`);
        });
    }
})().then(() => slimer.exit());
