import {Meta, Story, StoryContext} from '@storybook/html';
import template from 'T/component/checkbox.twig';

export default {
    title: 'Form/Checkbox',
    args: {
        enlarge: true,
    },
    argTypes: {
        caption_on: {
            type: {required: true},
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
    async (ctx: StoryContext) => {
        return {
            html: await template(ctx.args),
        }
    },
];

export const OnOff = Template.bind({});
OnOff.loaders = createLoaders();
OnOff.storyName = 'On/Off';
OnOff.args = {
    caption_on: 'Click me',
};

export const AB = Template.bind({});
AB.loaders = createLoaders();
AB.storyName = 'A/B';
AB.args = {
    caption_on: 'Option A',
    caption_off: 'Option B',
};
