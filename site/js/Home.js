new class {
    constructor() {
        this.initChevrons();
    }

    // This is just to save generating superfluous markup.
    initChevrons() {
        document.querySelectorAll('.top10 > footer > a > span:last-of-type').forEach(span => {
            span.parentNode.appendChild(span.cloneNode());
            span.parentNode.appendChild(span.cloneNode());
        });
    }
};
