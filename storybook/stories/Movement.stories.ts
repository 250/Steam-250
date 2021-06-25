import {Args, Meta, Story} from '@storybook/html';
import template from '../../template/partial/movement.twig';

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

export const No_change = Template.bind({});
No_change.loaders = createLoaders();
No_change.args = {
    movement: '0',
};

export const Dead = Template.bind({});
Dead.loaders = createLoaders();
Dead.args = {
    movement: 'dead',
};
