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
    const tabs = [...storiesSection.querySelectorAll<HTMLAnchorElement>('aside > a')];
    const articles = [...storiesSection.querySelectorAll<HTMLElement>('article')];

    const activate = (index: number) => {
        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.toggle('active', i === index);
            articles[i].hidden = i !== index;
        }
    };

    for (const tab of tabs) {
        tab.addEventListener('click', e => {
            activate(tabs.indexOf(tab));

            e.preventDefault();
        });
    }

    // Rotate the active story on a 10s timer, driven by the progress bar animation.
    const progress = storiesSection.querySelector<HTMLElement>('.progress');
    const storiesBox = storiesSection.querySelector<HTMLElement>(':scope > div');
    if (progress && storiesBox && tabs.length > 1 && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
        let enabled = true;
        let activeIndex = tabs.findIndex(tab => tab.classList.contains('active'));

        const pause = () => {
            if (enabled) {
                progress.style.animationPlayState = 'paused';
            }
        };
        const resume = () => {
            if (enabled) {
                progress.style.animationPlayState = 'running';
            }
        };
        const rotate = () => {
            activeIndex = (activeIndex + 1) % tabs.length;
            activate(activeIndex);
        };
        const disable = () => {
            enabled = false;
            progress.style.display = 'none';
            progress.removeEventListener('animationiteration', rotate);
            storiesBox.removeEventListener('mouseenter', pause);
            storiesBox.removeEventListener('mouseleave', resume);
            storiesBox.removeEventListener('click', disable);
        };

        progress.addEventListener('animationiteration', rotate);
        storiesBox.addEventListener('mouseenter', pause);
        storiesBox.addEventListener('mouseleave', resume);
        storiesBox.addEventListener('click', disable);

        // If the pointer is already over the box at load, mouseenter never fires —
        // start paused so hover-pause is consistent from the first frame.
        if (storiesBox.matches(':hover')) {
            pause();
        }
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
