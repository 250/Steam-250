import {Args, Meta, Story} from '@storybook/html';
import template from 'T/component/checkbox.twig';

export default {
    title: 'Form/Checkbox',
    decorators: [
        Story => `<form style="font-size: 200%">${Story()}</form>`,
    ],
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (args: Args) => {
        return {
            html: await template(args.args),
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
