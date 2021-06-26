import {Args, Meta, Story} from '@storybook/html';
import template from 'T/partial/movement.twig';

export default {
    title: 'Steam 250/Movement',
    argTypes: {
        movement: {control: 'text'},
    },
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (args: Args) => {
        return {
            html: await template({movement: args.args.movement}),
        }
    },
];

export const Up = Template.bind({});
Up.loaders = createLoaders();
Up.args = {
    movement: '12',
};

export const Down = Template.bind({});
Down.loaders = createLoaders();
Down.args = {
    movement: '-12',
};

export const New = Template.bind({});
New.loaders = createLoaders();
New.args = {
    movement: null,
};

export const NoChange = Template.bind({});
NoChange.loaders = createLoaders();
NoChange.args = {
    movement: '0',
};

export const Removed = Template.bind({});
Removed.loaders = createLoaders();
Removed.args = {
    movement: 'dead',
};
