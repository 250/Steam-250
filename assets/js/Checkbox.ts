enum TriState {
    Middle,
    Right,
    Left,
}

declare global {
    interface HTMLInputElement {
        go2: (state: TriState) => void
    }
}

export default class Checkbox {
    public static initTristateCheckboxes() {
        document.querySelectorAll('div.tri').forEach(div => {
            const
                radios = [...div.querySelectorAll('input')],
                radio = radios[0],
                gutter = radio.parentNode!.querySelector('span')!,
                gutterWidth = parseFloat(getComputedStyle(gutter, ':before').width),
                lowerBound = gutterWidth / 3,
                upperBound = lowerBound * 2,

                go2 = radio.go2 = (state: TriState) => {
                    lastState = state;

                    radios.filter(r => r.value === state.toString())[0].checked = true;
                    radio.parentElement!.dataset['state'] = state.toString();
                }
            ;

            let lastState: TriState = +(radios.filter(r => r.checked)[0]?.value ?? 0);

            // Initialize UI state.
            go2(lastState);

            radio.addEventListener('click', ev => {
                const x = ev.clientX - gutter.getBoundingClientRect().x;

                if (x >= lowerBound && x <= upperBound) {
                    // When click in mid-zone, always move knob to mid-zone.
                    go2(TriState.Middle);
                } else if (lastState === TriState.Middle) {
                    // When knob is in mid-zone but click is outside, move knob left or right.
                    go2(x > upperBound ? TriState.Right : TriState.Left);
                } else {
                    // When knob is left or right, revert to mid-zone or opposite extreme, depending on click zone.
                    if (lastState === TriState.Left) {
                        go2(x < lowerBound ? TriState.Middle : TriState.Right)
                    } else {
                        go2(x > upperBound ? TriState.Middle : TriState.Left);
                    }
                }
            });
        });
    }
}
