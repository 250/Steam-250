import {Args, Meta, Story} from '@storybook/html';

export default {
    title: 'Form/Button group',
} as Meta;

const Template: Story = (args, {loaded: {html}}) => html;

const createLoaders = () => [
    async (ctx: Args) => {
        return {
            html: ctx.parameters.html,
        }
    },
];

export const Linear = Template.bind({});
Linear.loaders = createLoaders();
Linear.parameters = {
    html: `
        <div class="button-group">
            <a class="button">Button 1</a>
            <a class="button">Button 2</a>
            <a class="button">Button 3</a>
            <a class="button">Button 4</a>
        </div>
    `,
}

export const Wrapped = Template.bind({});
Wrapped.loaders = createLoaders();
Wrapped.parameters = {
    html: `
        <div class="button-group wrap" style="max-width: 30em">
            <a class="button">Button 1</a>
            <a class="button">Button 2</a>
            <a class="button">Button 3</a>
            <a class="button end">Button 4</a>
            <a class="button start">Button 5</a>
            <a class="button">Button 6</a>
            <a class="button">Button 7</a>
            <a class="button">Button 8</a>
        </div>
    `,
}

export const MultiGroup = Template.bind({});
MultiGroup.loaders = createLoaders();
MultiGroup.parameters = {
    html: `
        <div class="button-group wrap" style="max-width: 30em">
            <a class="button">Button 1</a>
            <a class="button">Button 2</a>
            <a class="button">Button 3</a>
            <a class="button end">Button 4</a>
            <a class="button start endgrp">Button 5</a>
            <a class="button">All</a>
            <a class="button">None</a>
        </div>
    `,
}
