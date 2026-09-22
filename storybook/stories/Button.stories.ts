import type {Meta, StoryObj} from '@storybook/html-vite';

type ButtonArgs = {
    caption: string;
    disabled: boolean;
};

const meta = {
    title: 'Form/Button',
} satisfies Meta<ButtonArgs>;

export default meta;

type Story = StoryObj<ButtonArgs>;

export const Button: Story = {
    render: args => `<button class="button"${args.disabled ? ' disabled' : ''}>${args.caption}</button>`,
    args: {
        caption: 'Click me',
        disabled: false,
    },
};
