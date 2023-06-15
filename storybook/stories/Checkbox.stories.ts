import {Args, Meta, Story} from '@storybook/html';
import template from '@components/checkbox.twig';
import Checkbox from '../../assets/js/Checkbox';

// Only respond to Storybook emulated DOM loaded event to prevent double-loading.
addEventListener('DOMContentLoaded', e => e.isTrusted || Checkbox.initTristateCheckboxes());

export default {
    title: 'Form/Checkbox',
    args: {
        enlarge: true,
    },
    argTypes: {
        caption_on: {
            type: {name: 'string', required: true},
        },
    },
    parameters: {
        controls: {
            sort: 'requiredFirst',
        },
    },
    decorators: [
        (Story, ctx) => `<form ${ctx.args.enlarge && 'style="font-size: 200%"'}>${Story()}</form>`,
    ],
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (ctx: Args) => {
        return {
            html: await template({...ctx.args, ...ctx.parameters}),
        }
    },
];

export const OnOff = Template.bind({});
OnOff.loaders = createLoaders();
OnOff.storyName = 'On/Off';
OnOff.args = {
    caption_on: 'Click me',
    negative: false,
};

export const AB = Template.bind({});
AB.loaders = createLoaders();
AB.storyName = 'A/B';
AB.args = {
    caption_on: 'Option A',
    caption_off: 'Option B',
};

export const Tri = Template.bind({});
Tri.loaders = createLoaders();
Tri.storyName = 'Tri-state';
Tri.args = {
    caption_on: 'Include',
    caption_off: 'Exclude',
};
Tri.parameters = {
    tri: true,
    name: 'foo',
}
