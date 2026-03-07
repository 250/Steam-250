const path = require('path');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const Dotenv = require('dotenv-webpack');

const outDir = process.env.APP_ENV === 'headless' ? 'headless' : 'site';

module.exports = {
    mode: 'development',
    context: path.resolve(__dirname, '..', 'assets'),
    entry: {
        250: {
            import: [
                './js/250.ts',
                './js/LazyLoad.ts',

                './css/250.less',
                './css/inc/3col.less',

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
        ],
        about: [
            './css/about.less',
        ],
        search: ['./css/search.less'],
    },

    output: {
        path: path.resolve(__dirname, '..', `${outDir}/c`),
        hashFunction: 'xxhash64',
    },

    plugins: [
        new MiniCssExtractPlugin(),
        new Dotenv({
            path: path.join(__dirname, '.env.local'),
            defaults: path.join(__dirname, '.env'),
            systemvars: true,
        }),
        new CopyPlugin({
            patterns: [
                // Root of asset directory assets.
                { from: '*', to: '..' },
                // Subdirectory assets. TODO: Exclude CSS when no longer sourced internally.
                { from: '!({js,}/*)*/**/*.!(less)', to: '..' }
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
                test: /\.tsx?$/,
                loader: 'ts-loader',
            },
            {
                test: /\.(less|css)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
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
                            sourceMap: true,
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
        ],
    },

    optimization: {
        minimizer: [
            new TerserPlugin(),
            new CssMinimizerPlugin(),
        ],
    },
}
