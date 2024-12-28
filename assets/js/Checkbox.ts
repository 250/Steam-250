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
    public static initCheckboxes() {
        document.querySelectorAll<HTMLDivElement>('[role=checkbox]').forEach(div => {
            const
                radios = [...div.querySelectorAll('input')],
                radio = radios[0],
                gutter = radio.parentNode!.querySelector('span')!,

                measure = () => {
                    gutterWidth ||= parseFloat(getComputedStyle(gutter, ':before').width);
                    lowerBound ||= gutterWidth / 3;

                    return upperBound ||= lowerBound * 2;
                },

                go2 = radio.go2 = (state: TriState) => {
                    const go = (state: TriState) => {
                        currentState = state;

                        radios.filter(r => r.value === state.toString())[0].checked = true;
                        div.ariaChecked = {
                            [TriState.Middle]: 'mixed',
                            [TriState.Right]: 'true',
                            [TriState.Left]: 'false',
                        }[state];
                    }

                    if (measure()) go(state);
                    // WebKit cannot measure gutter on DOM ready. setTimeout is slightly more efficient than load event.
                    else setTimeout(() => measure() && go(state));
                }
            ;

            let
                gutterWidth: number,
                lowerBound: number,
                upperBound: number,
                currentState: TriState = +(radios.filter(r => r.checked)[0]?.value ?? 0)
            ;

            // Initialize UI state.
            go2(currentState);

            radio.addEventListener('click', ev => {
                const x = ev.clientX - gutter.getBoundingClientRect().x;

                if (x >= lowerBound && x <= upperBound) {
                    // When click in mid-zone, always move knob to mid-zone.
                    go2(TriState.Middle);
                } else if (currentState === TriState.Middle) {
                    // When knob is in mid-zone but click is outside, move knob left or right.
                    go2(x > upperBound ? TriState.Right : TriState.Left);
                } else {
                    // When knob is left or right, revert to mid-zone or opposite extreme, depending on click zone.
                    if (currentState === TriState.Left) {
                        go2(x < lowerBound ? TriState.Middle : TriState.Right)
                    } else {
                        go2(x > upperBound ? TriState.Middle : TriState.Left);
                    }
                }
            });

            // Cycle states when pressing [spacebar].
            div.addEventListener('keydown', ev => {
                if (ev.key === ' ') {
                    go2(++currentState % 3);

                    ev.preventDefault();
                }
            });
        });

        // Toggle checked state for binary checkboxes when pressing [spacebar].
        document.querySelectorAll<HTMLElement>('label[tabindex]:has(input[type=checkbox])').forEach(label =>
            label.addEventListener('keydown', ev => {
                if (ev.key === ' ') {
                    label.click();

                    ev.preventDefault();
                }
            })
        );
    }
}
