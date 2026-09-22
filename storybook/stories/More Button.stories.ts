import type {Meta, StoryContext, StoryObj} from '@storybook/html-vite';
import {renderTemplate} from '../twig';

type MoreButtonArgs = {
    caption: string;
};

const meta = {
    title: 'Form/More Button',
} satisfies Meta<MoreButtonArgs>;

export default meta;

type Story = StoryObj<MoreButtonArgs>;

export const MoreButton: Story = {
    name: 'More Button',
    loaders: [
        async ({args}: StoryContext<MoreButtonArgs>) => ({
            html: await renderTemplate('component/more button.twig', args),
        }),
    ],
    render: (_args, {loaded: {html}}) => {
        requestAnimationFrame(() => {
            document.querySelectorAll('.more-button').forEach(link => {
                if (link.children.length !== 2) return;

                const chevron = link.lastElementChild!;
                link.append(chevron.cloneNode(), chevron.cloneNode());
            });
        });

        return html;
    },
    args: {
        caption: 'More',
    },
};
