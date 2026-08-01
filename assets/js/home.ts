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
}
