import {createArrayLoader, createEnvironment} from 'twing/light';

const applicationTemplates = import.meta.glob(
    '../template/**/*.twig',
    {eager: true, import: 'default', query: '?raw'},
) as Record<string, string>;
const componentTemplates = import.meta.glob(
    '../vendor/250/components/**/*.twig',
    {eager: true, import: 'default', query: '?raw'},
) as Record<string, string>;

const templates = Object.fromEntries([
    ...Object.entries(applicationTemplates).map(([name, template]) => [
        name.replace('../template/', ''),
        template,
    ] as const),
    ...Object.entries(componentTemplates).map(([name, template]) => [
        name.replace('../vendor/250/components/', '@components/'),
        template,
    ] as const),
]);
const twing = createEnvironment(createArrayLoader(templates));

export const renderTemplate = (name: string, context: Record<string, unknown>) => twing.render(name, context);
