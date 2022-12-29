import {Args, Meta, Story} from '@storybook/html';

export default {
    title: 'Form/Button',
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (args: Args) => {
        return {
            html: `<button class="button"${args.args.disabled ? 'disabled' : ''}>${args.args.caption}</button>`,
        }
    },
];

export const Button = Template.bind({});
Button.loaders = createLoaders();
Button.args = {
    caption: 'Click me',
    disabled: false,
};
