import {Args, Meta, Story} from '@storybook/html';
import template from 'T/component/more button.twig';

// Only respond to Storybook emulated DOM loaded event to prevent double-loading.
addEventListener('DOMContentLoaded', e => e.isTrusted || S250.initChevrons());

export default {
    title: 'Form/More Button',
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (args: Args) => {
        return {
            html: await template(args.args),
        }
    },
];

export const More_Button = Template.bind({});
More_Button.loaders = createLoaders();
More_Button.args = {
    caption: 'More',
};
