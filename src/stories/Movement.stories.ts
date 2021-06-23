import {Meta, Story} from '@storybook/html';
import template from '../../template/partial/movement.twig';

export default {
    title: 'Steam 250/Movement',
} as Meta;

const Template: Story = (args, { loaded: { html } }) => html;

const movement = (movement?: string|number) => [
    async () => ({
        html: await template({movement: movement}),
    }),
];

export const Up = Template.bind({});
// @ts-ignore
Up.loaders = movement(123);

export const Down = Template.bind({});
// @ts-ignore
Down.loaders = movement(-123);
