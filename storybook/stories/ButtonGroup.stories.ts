import type {Meta, StoryObj} from '@storybook/html-vite';

const meta = {
    title: 'Form/Button group',
} satisfies Meta;

export default meta;

type Story = StoryObj;

export const Linear: Story = {
    render: () => `
        <div class="button-group">
            <a class="button">Button 1</a>
            <a class="button">Button 2</a>
            <a class="button">Button 3</a>
            <a class="button">Button 4</a>
        </div>
    `,
};

export const Wrapped: Story = {
    render: () => `
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
};

export const MultiGroup: Story = {
    render: () => `
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
};
