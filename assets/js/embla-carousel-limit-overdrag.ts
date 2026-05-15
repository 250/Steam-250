import type { EmblaCarouselType, EmblaPluginType } from 'embla-carousel'

type Options = {
    maxOverdragFraction?: number
}

export function EmblaCarouselLimitOverdrag(opts: Options = {}): EmblaPluginType {
    const { maxOverdragFraction = 0.1 } = opts

    let emblaApi: EmblaCarouselType
    let originalConstrain: (pointerDown: boolean) => void

    function viewSize(): number {
        const engine = emblaApi.internalEngine()
        const axis = engine.options.axis
        const root = emblaApi.rootNode()
        return axis === 'y' ? root.offsetHeight : root.offsetWidth
    }

    function init(api: EmblaCarouselType): void {
        emblaApi = api

        const { scrollBounds, limit, target, offsetLocation, scrollBody } =
            api.internalEngine()

        originalConstrain = scrollBounds.constrain

        scrollBounds.constrain = function (pointerDown: boolean): void {
            // Hard clamp target regardless of where location is.
            // This handles the flick case where target leaps far ahead of location
            // before shouldConstrain would otherwise return true.
            const size = viewSize()
            const maxPx = size * maxOverdragFraction

            const targetLeadsOut =
                (target.get() > limit.max + maxPx && target.get() >= offsetLocation.get()) ||
                (target.get() < limit.min - maxPx && target.get() <= offsetLocation.get())

            if (targetLeadsOut) {
                target.set(Math.max(limit.min - maxPx, Math.min(limit.max + maxPx, target.get())))
            }

            // Friction ramp for the rubber-band feel during normal dragging.
            if (!scrollBounds.shouldConstrain()) return

            const edgeOffsetTolerance = maxPx
            const pullBackThreshold = edgeOffsetTolerance * 0.2

            const edge = limit.reachedMin(offsetLocation.get()) ? 'min' : 'max'
            const diffToEdge = Math.abs(limit[edge] - offsetLocation.get())
            const diffToTarget = target.get() - offsetLocation.get()

            const friction = Math.max(0.1, Math.min(0.99, diffToEdge / edgeOffsetTolerance))
            target.subtract(diffToTarget * friction)

            if (!pointerDown && Math.abs(diffToTarget) < pullBackThreshold) {
                target.set(limit.constrain(target.get()))
                scrollBody.useDuration(25).useBaseFriction()
            }
        }
    }

    function destroy(): void {
        if (emblaApi && originalConstrain) {
            emblaApi.internalEngine().scrollBounds.constrain = originalConstrain
            originalConstrain = undefined!
        }
        emblaApi = undefined!
    }

    return {
        name: 'EmblaCarouselLimitOverdrag',
        options: {},
        init,
        destroy,
    }
}
