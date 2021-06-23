module.exports = {
  stories: [
    '../src/**/*.stories.mdx',
    '../src/**/*.stories.@(js|jsx|ts|tsx)'
  ],
  addons: [
    '@storybook/addon-links',
    '@storybook/addon-essentials',
    '@whitespace/storybook-addon-html',
  ],
  webpackFinal: (config) => {
    config.module.rules.push({
      test: /\.twig$/,
      loader: 'twing-loader',
      options: {
        environmentModulePath: require.resolve('./twig.js'),
      },
    });

    return config;
  }
};
