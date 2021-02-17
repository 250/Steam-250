// @ts-ignore
import lazySizes from 'lazysizes';

new class {
    constructor() {
        this.initImageLazyloading();
    }

    private initImageLazyloading() {
        lazySizes.init();
    }
}
