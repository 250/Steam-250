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
