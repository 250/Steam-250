import path from 'node:path';
import {fileURLToPath} from 'node:url';
import autoprefixer from 'autoprefixer';
import {mergeConfig} from 'vite';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');

export default {
    stories: [
        './stories/**/*.stories.@(js|jsx|ts|tsx)',
    ],
    staticDirs: [
        {
            from: '../assets/img',
            to: '/img',
        },
        {
            from: '../assets/svg',
            to: '/svg',
        },
    ],
    core: {
        disableTelemetry: true,
    },
    previewHead: head => `${head}
<meta name="darkreader-lock">`,
    addons: [
        '@whitespace/storybook-addon-html',
    ],
    framework: {
        name: '@storybook/html-vite',
        options: {},
    },
    viteFinal: config => mergeConfig(config, {
        resolve: {
            alias: {
                T: path.resolve(projectRoot, 'template'),
                '@components': path.resolve(projectRoot, 'vendor/250/components'),
            },
        },
        css: {
            postcss: {
                plugins: [
                    autoprefixer(),
                ],
            },
            preprocessorOptions: {
                less: {
                    relativeUrls: false,
                    globalVars: {
                        C250: `'${process.env.CLUB_250_STATIC_BASE_URL}'`,
                    },
                    strictUnits: true,
                },
            },
        },
    }),
};
