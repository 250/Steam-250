import EmblaCarousel from 'embla-carousel'
import { EmblaCarouselLimitOverdrag } from './embla-carousel-limit-overdrag'

for (const selector of ['section.trend > div', 'section.yr > div']) {
    const viewportNode = document.querySelector<HTMLElement>(selector)!;

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
