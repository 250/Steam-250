(() => {
    let div = document.createElement('div');

    div.setAttribute('class', 'countdown');
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
                // duration = -1;

                if (duration < 0) {
                    clearInterval(timer);

                    div.classList.add('ready');

                    return div.innerHTML = '<a href="https://youtu.be/Mu0cE9RgK5M">Ready for launch</a>';
                }

                let formattedDuration = duration.format('HH:mm.ss');

                div.innerText = 'Next update in ' + (
                    (blink ^= 1)
                        ? formattedDuration
                        : formattedDuration.replace(/:/g, ' ')
                );
            }, 500)
        }
    );
})();
