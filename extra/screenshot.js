const
    BASE_URL = 'http://steam250.com/',
    PAGES = [
        'index',
    ],
    webpage = require('webpage').create()
;

webpage.viewportSize = {width: 1260, height: screen.height};

(async _ => {
    for (const page of PAGES) {
        await webpage.open(BASE_URL + page, _ => {
            webpage.evaluate(_ => {
                // Remove ads.
                document.querySelectorAll('ins').forEach(e => e.remove());

                // Hide fixed links.
                document.querySelector('.fixedlinks').style.display = 'none';
            });

            webpage.render(`${page}.png`);
        });
    }
})().then(_ => slimer.exit());
