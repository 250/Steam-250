import type {Meta, StoryObj} from '@storybook/html-vite';
import LogoSparkler from '../../assets/js/LogoSparkler';

let disposeSparkler: (() => void) | undefined;

const meta = {
    title: 'Brand/Logo',
    beforeEach() {
        return () => {
            disposeSparkler?.();
            disposeSparkler = undefined;
        };
    },
} satisfies Meta;

export default meta;

type Story = StoryObj;

export const Logo: Story = {
    render: () => {
        requestAnimationFrame(() => {
            disposeSparkler?.();
            disposeSparkler = LogoSparkler.init();
        });

        return '<h1><a href="/" title="Steam 250 Home">Steam 250</a></h1>';
    },
};
