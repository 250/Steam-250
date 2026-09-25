const PARTICLE_LIMIT = 360;
const PARTICLE_RATE = 420;
const BURST_SIZE = 110;
const FOREGROUND_PARTICLE_RATE = .08;
const CANVAS_PADDING = 64;
const LOGO_IMAGE_WIDTH = 90;
const LOGO_IMAGE_HEIGHT = 96;

const HUES = [238, 243, 248, 253, 258, 263, 268, 273, 278, 283, 288] as const;
const REFRACTION_PADDING = 4;
const REFRACTION_COLOURS = [
    [103, 111, 255],
    [214, 82, 255],
] as const;

export default class LogoSparkler {
    private static readonly instances = new WeakMap<HTMLAnchorElement, LogoSparkler>();
    private static sprites?: HTMLCanvasElement[];

    private readonly heading: HTMLElement;
    private readonly canvas: HTMLCanvasElement;
    private readonly context: CanvasRenderingContext2D;
    private readonly foregroundCanvas: HTMLCanvasElement;
    private readonly foregroundContext: CanvasRenderingContext2D;
    private readonly refractionCanvas: HTMLCanvasElement;
    private readonly refractionContext: CanvasRenderingContext2D;
    private readonly resizeObserver: ResizeObserver;
    private readonly motionPreference = matchMedia('(prefers-reduced-motion: reduce)');

    private readonly x = new Float32Array(PARTICLE_LIMIT);
    private readonly y = new Float32Array(PARTICLE_LIMIT);
    private readonly velocityX = new Float32Array(PARTICLE_LIMIT);
    private readonly velocityY = new Float32Array(PARTICLE_LIMIT);
    private readonly age = new Float32Array(PARTICLE_LIMIT);
    private readonly lifetime = new Float32Array(PARTICLE_LIMIT);
    private readonly size = new Float32Array(PARTICLE_LIMIT);
    private readonly phase = new Float32Array(PARTICLE_LIMIT);
    private readonly colour = new Uint8Array(PARTICLE_LIMIT);
    private readonly foreground = new Uint8Array(PARTICLE_LIMIT);
    private refractionSprites: HTMLCanvasElement[] = [];

    private logoLeft = 0;
    private logoTop = 0;
    private logoWidth = 0;
    private logoHeight = 0;
    private particleCount = 0;
    private emissionRemainder = 0;
    private previousFrameTime = 0;
    private animationFrame = 0;
    private refractionIntensity = 0;
    private hovered = false;
    private focused = false;
    private enabled = false;

    static init(root: ParentNode = document) {
        const instances: LogoSparkler[] = [];

        root.querySelectorAll<HTMLAnchorElement>('h1 > a').forEach(link => {
            let instance = this.instances.get(link);
            if (!instance) {
                instance = new LogoSparkler(link);
                this.instances.set(link, instance);
            }

            instances.push(instance);
        });

        return () => instances.forEach(instance => instance.destroy());
    }

    private constructor(private readonly link: HTMLAnchorElement) {
        this.heading = link.parentElement!;
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'logo-sparkler';
        this.canvas.setAttribute('aria-hidden', 'true');
        this.context = this.canvas.getContext('2d', {alpha: true})!;
        this.foregroundCanvas = document.createElement('canvas');
        this.foregroundCanvas.className = 'logo-sparkler-front';
        this.foregroundCanvas.setAttribute('aria-hidden', 'true');
        this.foregroundContext = this.foregroundCanvas.getContext('2d', {alpha: true})!;
        this.refractionCanvas = document.createElement('canvas');
        this.refractionCanvas.className = 'logo-refraction';
        this.refractionCanvas.setAttribute('aria-hidden', 'true');
        this.refractionContext = this.refractionCanvas.getContext('2d', {alpha: true})!;
        this.resizeObserver = new ResizeObserver(this.resize);
        this.loadRefractionSprites();

        this.motionPreference.addEventListener('change', this.updateMotionPreference);
        this.updateMotionPreference();
    }

    private readonly updateMotionPreference = () => {
        if (this.motionPreference.matches) {
            this.disable();
        } else {
            this.enable();
        }
    };

    private enable() {
        if (this.enabled) return;

        this.enabled = true;
        this.heading.append(this.canvas, this.refractionCanvas, this.foregroundCanvas);
        this.resizeObserver.observe(this.link);
        this.link.addEventListener('pointerenter', this.startHovering);
        this.link.addEventListener('pointerleave', this.stopHovering);
        this.link.addEventListener('focus', this.startFocusing);
        this.link.addEventListener('blur', this.stopFocusing);

        this.hovered = this.link.matches(':hover');
        this.focused = this.link === document.activeElement;
        this.resize();

        if (this.isEmitting()) {
            this.excite();
        }
    }

    private disable() {
        if (!this.enabled) return;

        this.enabled = false;
        this.resizeObserver.unobserve(this.link);
        this.link.removeEventListener('pointerenter', this.startHovering);
        this.link.removeEventListener('pointerleave', this.stopHovering);
        this.link.removeEventListener('focus', this.startFocusing);
        this.link.removeEventListener('blur', this.stopFocusing);
        this.canvas.remove();
        this.foregroundCanvas.remove();
        this.refractionCanvas.remove();
        cancelAnimationFrame(this.animationFrame);

        this.animationFrame = 0;
        this.particleCount = 0;
        this.emissionRemainder = 0;
        this.refractionIntensity = 0;
        this.hovered = false;
        this.focused = false;
    }

    private destroy() {
        this.disable();
        this.resizeObserver.disconnect();
        this.motionPreference.removeEventListener('change', this.updateMotionPreference);
        LogoSparkler.instances.delete(this.link);
    }

    private readonly startHovering = () => {
        this.hovered = true;
        this.excite();
    };

    private readonly stopHovering = () => {
        this.hovered = false;
    };

    private readonly startFocusing = () => {
        this.focused = true;
        this.excite();
    };

    private readonly stopFocusing = () => {
        this.focused = false;
    };

    private excite() {
        if (!this.enabled) return;

        for (let i = 0; i < BURST_SIZE; i++) {
            this.emitParticle(1.2);
        }

        if (!this.animationFrame) {
            this.previousFrameTime = performance.now();
            this.animationFrame = requestAnimationFrame(this.animate);
        }
    }

    private readonly animate = (time: number) => {
        const elapsed = Math.min((time - this.previousFrameTime) / 1000, 1 / 30);
        this.previousFrameTime = time;

        if (this.isEmitting()) {
            this.emissionRemainder += elapsed * PARTICLE_RATE;
            const emitCount = Math.floor(this.emissionRemainder);
            this.emissionRemainder -= emitCount;

            for (let i = 0; i < emitCount; i++) {
                this.emitParticle(1);
            }
        } else {
            this.emissionRemainder = 0;
        }

        this.updateParticles(elapsed);

        const refractionTarget = this.isEmitting() ? 1 : Math.min(1, this.particleCount / BURST_SIZE);
        const refractionResponse = 1 - Math.exp(-elapsed * (refractionTarget > this.refractionIntensity ? 12 : 4));
        this.refractionIntensity += (refractionTarget - this.refractionIntensity) * refractionResponse;
        if (!refractionTarget && this.refractionIntensity < .005) {
            this.refractionIntensity = 0;
        }
        this.drawParticles();
        this.drawRefraction(time);

        if (this.isEmitting() || this.particleCount || this.refractionIntensity) {
            this.animationFrame = requestAnimationFrame(this.animate);
        } else {
            this.animationFrame = 0;
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.foregroundContext.clearRect(
                0,
                0,
                this.foregroundCanvas.width,
                this.foregroundCanvas.height,
            );
            this.refractionContext.clearRect(
                0,
                0,
                this.refractionCanvas.width,
                this.refractionCanvas.height,
            );
        }
    };

    private isEmitting() {
        return this.hovered || this.focused;
    }

    private emitParticle(energy: number) {
        if (this.particleCount === PARTICLE_LIMIT) return;

        const index = this.particleCount++;
        const direction = Math.random() * Math.PI * 2;
        const directionX = Math.cos(direction);
        const directionY = Math.sin(direction);
        const halfLogoWidth = this.logoWidth / 2;
        const halfLogoHeight = this.logoHeight / 2;
        const edgeDistance = 1 / Math.hypot(directionX / halfLogoWidth, directionY / halfLogoHeight);
        const originDistance = edgeDistance * .5;
        const originX = this.logoLeft + halfLogoWidth + directionX * originDistance;
        const originY = this.logoTop + halfLogoHeight + directionY * originDistance;
        const angle = direction + (Math.random() - .5) * .24;
        const large = Math.random() < .05;
        const speed = (22 + Math.random() * 58) * energy * (large ? .38 : 1);

        this.x[index] = originX + (Math.random() - .5) * 2;
        this.y[index] = originY + (Math.random() - .5) * 2;
        this.velocityX[index] = Math.cos(angle) * speed;
        this.velocityY[index] = Math.sin(angle) * speed;
        this.age[index] = 0;
        this.lifetime[index] = large ? .65 + Math.random() * .3 : .36 + Math.random() * .34;
        this.size[index] = large ? 15 + Math.random() * 7 : 5 + Math.random() * 10 * energy;
        this.phase[index] = Math.random() * Math.PI * 2;
        this.colour[index] = Math.floor(Math.pow(Math.random(), .75) * HUES.length);
        this.foreground[index] = Number(Math.random() < FOREGROUND_PARTICLE_RATE);
    }

    private updateParticles(elapsed: number) {
        const drag = Math.pow(.35, elapsed);
        let index = 0;

        while (index < this.particleCount) {
            this.age[index] += elapsed;
            if (this.age[index] >= this.lifetime[index]) {
                this.removeParticle(index);
                continue;
            }

            this.velocityX[index] *= drag;
            this.velocityY[index] = this.velocityY[index] * drag - 5 * elapsed;
            this.x[index] += this.velocityX[index] * elapsed;
            this.y[index] += this.velocityY[index] * elapsed;
            index++;
        }
    }

    private removeParticle(index: number) {
        const last = --this.particleCount;
        if (index === last) return;

        this.x[index] = this.x[last];
        this.y[index] = this.y[last];
        this.velocityX[index] = this.velocityX[last];
        this.velocityY[index] = this.velocityY[last];
        this.age[index] = this.age[last];
        this.lifetime[index] = this.lifetime[last];
        this.size[index] = this.size[last];
        this.phase[index] = this.phase[last];
        this.colour[index] = this.colour[last];
        this.foreground[index] = this.foreground[last];
    }

    private drawParticles() {
        const backgroundContext = this.context;
        const foregroundContext = this.foregroundContext;
        const sprites = LogoSparkler.getSprites();
        backgroundContext.clearRect(0, 0, this.canvas.width, this.canvas.height);
        foregroundContext.clearRect(0, 0, this.foregroundCanvas.width, this.foregroundCanvas.height);
        backgroundContext.globalCompositeOperation = 'lighter';
        foregroundContext.globalCompositeOperation = 'lighter';

        for (let i = 0; i < this.particleCount; i++) {
            const progress = this.age[i] / this.lifetime[i];
            const fadeIn = Math.min(1, progress * 10);
            const fadeOut = 1 - progress;
            const flicker = .72 + Math.sin(this.phase[i] + this.age[i] * 38) * .28;
            const diameter = this.size[i] * (.72 + progress * .9);
            const context = this.foreground[i] ? foregroundContext : backgroundContext;

            context.globalAlpha = fadeIn * fadeOut * flicker;
            context.drawImage(
                sprites[this.colour[i]],
                this.x[i] - diameter / 2,
                this.y[i] - diameter / 2,
                diameter,
                diameter,
            );
        }

        backgroundContext.globalAlpha = 1;
        backgroundContext.globalCompositeOperation = 'source-over';
        foregroundContext.globalAlpha = 1;
        foregroundContext.globalCompositeOperation = 'source-over';
    }

    private readonly resize = () => {
        if (!this.enabled) return;

        const headingBounds = this.heading.getBoundingClientRect();
        const linkBounds = this.link.getBoundingClientRect();
        const logicalWidth = headingBounds.width + CANVAS_PADDING * 2;
        const logicalHeight = headingBounds.height + CANVAS_PADDING * 2;
        const pixelRatio = Math.min(devicePixelRatio, 2);

        this.canvas.style.left = `${-CANVAS_PADDING}px`;
        this.canvas.style.top = `${-CANVAS_PADDING}px`;
        this.canvas.style.width = `${logicalWidth}px`;
        this.canvas.style.height = `${logicalHeight}px`;
        this.canvas.width = Math.ceil(logicalWidth * pixelRatio);
        this.canvas.height = Math.ceil(logicalHeight * pixelRatio);
        this.context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);

        this.foregroundCanvas.style.left = `${-CANVAS_PADDING}px`;
        this.foregroundCanvas.style.top = `${-CANVAS_PADDING}px`;
        this.foregroundCanvas.style.width = `${logicalWidth}px`;
        this.foregroundCanvas.style.height = `${logicalHeight}px`;
        this.foregroundCanvas.width = Math.ceil(logicalWidth * pixelRatio);
        this.foregroundCanvas.height = Math.ceil(logicalHeight * pixelRatio);
        this.foregroundContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);

        const backgroundWidth = parseFloat(getComputedStyle(this.link).backgroundSize) || linkBounds.width * .62;
        this.logoWidth = backgroundWidth;
        this.logoHeight = (backgroundWidth * LOGO_IMAGE_HEIGHT) / LOGO_IMAGE_WIDTH;
        this.logoLeft =
            CANVAS_PADDING + linkBounds.left - headingBounds.left + (linkBounds.width - this.logoWidth) / 2;
        this.logoTop =
            CANVAS_PADDING + linkBounds.top - headingBounds.top + (linkBounds.height - this.logoHeight) / 2;

        const refractionWidth = this.logoWidth + REFRACTION_PADDING * 2;
        const refractionHeight = this.logoHeight + REFRACTION_PADDING * 2;
        this.refractionCanvas.style.left = `${this.logoLeft - CANVAS_PADDING - REFRACTION_PADDING}px`;
        this.refractionCanvas.style.top = `${this.logoTop - CANVAS_PADDING - REFRACTION_PADDING}px`;
        this.refractionCanvas.style.width = `${refractionWidth}px`;
        this.refractionCanvas.style.height = `${refractionHeight}px`;
        this.refractionCanvas.width = Math.ceil(refractionWidth * pixelRatio);
        this.refractionCanvas.height = Math.ceil(refractionHeight * pixelRatio);
        this.refractionContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        this.particleCount = 0;
    };

    private drawRefraction(time: number) {
        const context = this.refractionContext;
        context.clearRect(0, 0, this.refractionCanvas.width, this.refractionCanvas.height);
        if (!this.refractionIntensity) return;
        if (this.refractionSprites.length < REFRACTION_COLOURS.length) return;

        const blueOffsetX = Math.sin(time * .012) * 2;
        const blueOffsetY = Math.cos(time * .009) * 1.6;
        const purpleOffsetX = Math.cos(time * .008) * 1.8;
        const purpleOffsetY = Math.sin(time * .011) * 2.1;

        context.globalCompositeOperation = 'lighter';
        context.globalAlpha = (.13 + (Math.sin(time * .01) + 1) * .065) * this.refractionIntensity;
        context.drawImage(
            this.refractionSprites[0],
            REFRACTION_PADDING + blueOffsetX,
            REFRACTION_PADDING + blueOffsetY,
            this.logoWidth,
            this.logoHeight,
        );
        context.globalAlpha = (.16 + (Math.cos(time * .0085) + 1) * .07) * this.refractionIntensity;
        context.drawImage(
            this.refractionSprites[1],
            REFRACTION_PADDING + purpleOffsetX,
            REFRACTION_PADDING + purpleOffsetY,
            this.logoWidth,
            this.logoHeight,
        );
        context.globalAlpha = 1;
        context.globalCompositeOperation = 'source-over';
    }

    private loadRefractionSprites() {
        const backgroundImage = getComputedStyle(this.link).backgroundImage;
        const imageUrl = backgroundImage.match(/^url\(["']?(.*?)["']?\)$/)?.[1];
        if (!imageUrl) return;

        const image = new Image();
        image.crossOrigin = 'anonymous';
        image.addEventListener('load', () => {
            const sample = document.createElement('canvas');
            sample.width = image.naturalWidth;
            sample.height = image.naturalHeight;
            const context = sample.getContext('2d', {willReadFrequently: true})!;
            context.drawImage(image, 0, 0);
            const source = context.getImageData(0, 0, sample.width, sample.height).data;
            const logoPixels = new Uint8Array(sample.width * sample.height);
            const edgeAlpha = new Uint8ClampedArray(logoPixels.length);

            for (let pixel = 0; pixel < logoPixels.length; pixel++) {
                const sourceIndex = pixel * 4;
                const red = source[sourceIndex];
                const green = source[sourceIndex + 1];
                const blue = source[sourceIndex + 2];
                logoPixels[pixel] = Number(red > 65 && red > blue * 1.35 && green > blue * 1.08);
            }

            for (let y = 0; y < sample.height; y++) {
                for (let x = 0; x < sample.width; x++) {
                    const pixel = y * sample.width + x;
                    if (!logoPixels[pixel]) continue;

                    let edge = false;
                    for (let offsetY = -2; offsetY <= 2 && !edge; offsetY++) {
                        for (let offsetX = -2; offsetX <= 2; offsetX++) {
                            const neighbourX = x + offsetX;
                            const neighbourY = y + offsetY;
                            if (
                                neighbourX < 0 ||
                                neighbourX >= sample.width ||
                                neighbourY < 0 ||
                                neighbourY >= sample.height ||
                                !logoPixels[neighbourY * sample.width + neighbourX]
                            ) {
                                edge = true;
                                break;
                            }
                        }
                    }

                    if (edge) {
                        const sourceIndex = pixel * 4;
                        edgeAlpha[pixel] = Math.min(
                            230,
                            Math.max(70, (source[sourceIndex] + source[sourceIndex + 1]) * .6),
                        );
                    }
                }
            }

            this.refractionSprites = REFRACTION_COLOURS.map(([red, green, blue]) => {
                const sprite = document.createElement('canvas');
                sprite.width = sample.width;
                sprite.height = sample.height;
                const spriteContext = sprite.getContext('2d')!;
                const edge = spriteContext.createImageData(sample.width, sample.height);

                for (let pixel = 0; pixel < edgeAlpha.length; pixel++) {
                    const targetIndex = pixel * 4;
                    edge.data[targetIndex] = red;
                    edge.data[targetIndex + 1] = green;
                    edge.data[targetIndex + 2] = blue;
                    edge.data[targetIndex + 3] = edgeAlpha[pixel];
                }

                spriteContext.putImageData(edge, 0, 0);
                return sprite;
            });
        });
        image.src = imageUrl;
    }

    private static getSprites() {
        return (this.sprites ??= HUES.map(hue => {
            const sprite = document.createElement('canvas');
            sprite.width = 64;
            sprite.height = 64;
            const context = sprite.getContext('2d')!;
            const gradient = context.createRadialGradient(32, 32, 0, 32, 32, 32);
            gradient.addColorStop(0, '#fff');
            gradient.addColorStop(.08, `hsl(${hue} 100% 86%)`);
            gradient.addColorStop(.3, `hsl(${hue} 100% 66% / .95)`);
            gradient.addColorStop(1, `hsl(${hue} 100% 66% / 0)`);
            context.fillStyle = gradient;
            context.fillRect(0, 0, 64, 64);

            return sprite;
        }));
    }
}
