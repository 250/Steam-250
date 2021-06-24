module.exports = {
    stories: [
        './stories/*.stories.@(js|jsx|ts|tsx|mdx)',
    ],
    addons: [
        '@storybook/addon-links',
        '@storybook/addon-essentials',
        '@whitespace/storybook-addon-html',
    ],
    webpackFinal: (config) => {
        config.module.rules.push(
            {
                test: /\.twig$/,
                loader: 'twing-loader',
                options: {
                    environmentModulePath: require.resolve('./twig.js'),
                },
            },
            {
                test: /\.less$/,
                use: [
                    {
                        loader: 'style-loader',
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    [
                                        'autoprefixer',
                                    ],
                                ],
                            },
                        },
                    },
                    {
                        loader: 'less-loader',
                    },
                ],
            },
        );

        return config;
    }
};
