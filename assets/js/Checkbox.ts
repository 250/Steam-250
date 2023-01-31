enum TriState {
    Middle,
    Right,
    Left,
}

export default class Checkbox {
    public static initTristateCheckboxes() {
        document.querySelectorAll<HTMLInputElement>('.tri > input[type=checkbox]').forEach(checkbox => {
            const
                gutter = checkbox.parentNode!.querySelector('span')!,
                gutterWidth = parseFloat(getComputedStyle(gutter, ':before').width),
                lowerBound = gutterWidth / 3,
                upperBound = lowerBound * 2,
                go2 = (state: TriState) => {
                    checkbox.value = state.toString();

                    // Setting checked state should have no visual consequence, but ensures value is submitted on post.
                    checkbox.checked = state !== TriState.Middle;
                }
            ;

            checkbox.addEventListener('click', ev => {
                const x = ev.clientX - gutter.getBoundingClientRect().x;

                if (x >= lowerBound && x <= upperBound) {
                    // When click in mid-zone, always move knob to mid-zone.
                    go2(TriState.Middle);
                } else if (checkbox.value === TriState.Middle.toString()) {
                    // When knob is in mid-zone but click is outside, move knob left or right.
                    go2(x > upperBound ? TriState.Right : TriState.Left);
                } else {
                    // When knob is left or right, revert to mid-zone or opposite extreme, depending on click zone.
                    if (checkbox.value === TriState.Left.toString()) {
                        go2(x < lowerBound ? TriState.Middle : TriState.Right)
                    } else {
                        go2(x > upperBound ? TriState.Middle : TriState.Left);
                    }
                }
            });
        });
    }
}
