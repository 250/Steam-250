import type {Meta, StoryContext, StoryObj} from '@storybook/html-vite';
import Checkbox from '../../assets/js/Checkbox';
import {renderTemplate} from '../twig';

type CheckboxArgs = {
    caption_off?: string;
    caption_on: string;
    disabled: boolean;
    enlarge: boolean;
    negative?: boolean;
};

const meta = {
    title: 'Form/Checkbox',
    args: {
        enlarge: true,
        disabled: false,
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
        (Story, {args}) => `<form${args.enlarge ? ' style="font-size: 200%"' : ''}>${Story()}</form>`,
    ],
} satisfies Meta<CheckboxArgs>;

export default meta;

type Story = StoryObj<CheckboxArgs>;

const loadCheckbox = async ({args, parameters}: StoryContext<CheckboxArgs>) => ({
    html: await renderTemplate('@components/checkbox.twig', {
        ...args,
        ...(parameters.tri ? {tri: true, name: parameters.name} : {}),
    }),
});
const renderCheckbox: Story['render'] = (_args, {loaded: {html}}) => {
    requestAnimationFrame(() => Checkbox.initCheckboxes());

    return html;
};

export const OnOff: Story = {
    name: 'On/Off',
    loaders: [loadCheckbox],
    render: renderCheckbox,
    args: {
        caption_on: 'Click me',
        negative: false,
    },
};

export const AB: Story = {
    name: 'A/B',
    loaders: [loadCheckbox],
    render: renderCheckbox,
    args: {
        caption_on: 'Option A',
        caption_off: 'Option B',
    },
};

export const Tri: Story = {
    name: 'Tri-state',
    loaders: [loadCheckbox],
    render: renderCheckbox,
    args: {
        caption_on: 'Include',
        caption_off: 'Exclude',
    },
    parameters: {
        tri: true,
        name: 'foo',
    },
};
