import path from 'path';

import rspack from '@rspack/core';
import { CleanWebpackPlugin } from 'clean-webpack-plugin';
import Dotenv from 'dotenv-webpack';

const __dirname = import.meta.dirname;

const outDir = process.env.APP_ENV === 'headless' ? 'headless' : 'site';

export default (env, options) => {
    const prod = options.mode === 'production';

    return {
        mode: 'development',
        // Absolute base directory for entry points and resource paths.
        context: path.resolve(__dirname, '..', 'assets'),
        devtool: prod ? false : 'source-map',
        entry: {
            250: {
                import: [
                    './js/250.ts',
                    './js/LazyLoad.ts',

                    './css/250.less',

                    './js/Checkbox.ts'
                ],
                library: {
                    name: 'Checkbox',
                    type: 'global',
                    export: 'default',
                },
            },
            ranking: [
                './css/ranking.less'
            ],
            internal: [
                './js/BuildMonitor.ts',
            ],
            home: [
                './css/home.less',

                './js/home.ts'
            ],
            about: [
                './css/about.less',
            ],
            search: ['./css/search.less'],
        },

        output: {
            path: path.resolve(__dirname, '..', `${outDir}/c`),
        },

        plugins: [
            new rspack.CssExtractRspackPlugin({
                ignoreOrder: true,
            }),
            new Dotenv({
                path: path.join(__dirname, '.env.local'),
                defaults: path.join(__dirname, '.env'),
                systemvars: true,
            }),
            new rspack.CopyRspackPlugin({
                patterns: [
                    // Root of asset directory assets.
                    { from: '*', to: '..' },
                    // Subdirectory assets. TODO: Exclude CSS when no longer sourced internally.
                    { from: 'css/**', to: '..', globOptions: { ignore: ['**/*.less'] } },
                    { from: 'img/**', to: '..' },
                    { from: 'svg/**', to: '..' },
                ],
            }),
            new CleanWebpackPlugin({
                cleanOnceBeforeBuildPatterns: [
                    // Remove all files containing a '.' (to distinguish between files and directories).
                    path.join(process.cwd(), `${outDir}/**/*.*`),
                    // Do not remove HTML files.
                    '!' + path.join(process.cwd(), `${outDir}/**/*.html`),
                ],
            }),
        ],

        resolve: {
            extensions: ['.ts', '.js'],
        },

        module: {
            rules: [
                {
                    test: /\.(less|css)$/,
                    use: [
                        {
                            loader: rspack.CssExtractRspackPlugin.loader,
                        },
                        {
                            loader: 'css-loader',
                            options: {
                                url: false,
                                sourceMap: true,
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
                                        [
                                            'postcss-pxtorem',
                                        ],
                                    ],
                                },
                            },
                        },
                        {
                            loader: 'less-loader',
                            options: {
                                sourceMap: !prod,
                                lessOptions: {
                                    relativeUrls: false,
                                    strictUnits: true,
                                    globalVars: {
                                        C250: `'${process.env.CLUB_250_STATIC_BASE_URL}'`,
                                    },
                                },
                            },
                        },
                    ],
                },

                // Process .ts files with esbuild.
                {
                    test: /\.tsx?$/,
                    loader: 'esbuild-loader',
                    options: {
                        target: 'ES2022',
                    },
                },
            ],
        },

        optimization: {
            minimizer: [
                new rspack.SwcJsMinimizerRspackPlugin(),
                new rspack.LightningCssMinimizerRspackPlugin(),
            ],
            nodeEnv: false,
        },
    };
};
