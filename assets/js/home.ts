import EmblaCarousel from 'embla-carousel'
import { EmblaCarouselLimitOverdrag } from './embla-carousel-limit-overdrag'

for (const selector of ['section.hcards > div', 'section.vcards > div']) {
    for (const viewportNode of document.querySelectorAll<HTMLElement>(selector)) {
        EmblaCarousel(
            viewportNode,
            {
                dragFree: true,
            },
            [
                EmblaCarouselLimitOverdrag({maxOverdragFraction: .05}),
            ],
        );
    }
}

const storiesSection = document.querySelector<HTMLElement>('section.stories');
if (storiesSection) {
    const tabs = Array.from(storiesSection.querySelectorAll<HTMLAnchorElement>('aside > a'));
    const articles = Array.from(storiesSection.querySelectorAll<HTMLElement>('article'));

    for (const tab of tabs) {
        tab.addEventListener('click', e => {
            for (const t of tabs) {
                t.classList.toggle('active', t === tab);
            }

            for (const article of articles) {
                article.hidden = articles.indexOf(article) !== tabs.indexOf(tab);
            }

            e.preventDefault();
        });
    }

    // Waterfall of triangles behind the active tab.
    const triCount = 48;
    const maxDur = 8;
    for (const tab of tabs) {
        const triBackground = document.createElement('div');
        triBackground.className = 'tri-bg';

        for (let i = 0; i < triCount; i++) {
            const tri = document.createElement('i');
            tri.className = 'tri';

            const size = 10 + Math.random() * 22;
            const rotation = Math.random() * 360;
            const spin = (180 + Math.random() * 180) * (Math.random() < .5 ? -1 : 1);
            const duration = 4 + Math.random() * 4;
            // Stratified negative delays (plus jitter) keep the stream constant.
            const delay = -(i / triCount * maxDur + Math.random() * maxDur / triCount);

            // Each triangle fades out at 60-100% of half the tab's width.
            tri.dataset.travelFactor = (0.6 + Math.random() * .4).toFixed(2);

            tri.style.setProperty('--size', `${size}px`);
            tri.style.setProperty('--margin', `-${size}px`);
            tri.style.setProperty('--top', `${Math.random() * 110 - 5}%`);
            tri.style.setProperty('--r0', `${rotation}deg`);
            tri.style.setProperty('--r1', `${rotation + spin}deg`);
            tri.style.setProperty('--dur', `${duration}s`);
            tri.style.setProperty('--delay', `${delay}s`);
            tri.style.setProperty('--peak', (Math.random() * .3 + .3).toFixed(2));
            tri.style.setProperty('--c', Math.random() < .2 ? '#f1bf69' : '#d3beeb');

            triBackground.appendChild(tri);
        }

        tab.prepend(triBackground);
    }

    // Each triangle traverses up to half the tab's width before fading out.
    const updateTravel = () => {
        for (const tab of tabs) {
            const half = tab.clientWidth / 2;
            for (const tri of tab.querySelectorAll<HTMLElement>('.tri')) {
                tri.style.setProperty('--travel', `${half * parseFloat(tri.dataset.travelFactor ?? '1')}px`);
            }
        }
    };
    updateTravel();
    new ResizeObserver(updateTravel).observe(storiesSection);
}
