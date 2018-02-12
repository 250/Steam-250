const
    BASE_URL = 'http://steam250.com/',
    PAGES = [
        'index',
        'hidden_gems',
        'discounts',
        '2017',
    ]
;

let webpage = require('webpage').create();

webpage.viewportSize = {width: 1260, height: screen.height};

(async () => {
    for (const page of PAGES) {
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
