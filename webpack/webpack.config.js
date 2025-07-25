const path = require('path');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const Dotenv = require('dotenv-webpack');

module.exports = {
    mode: 'development',
    context: path.resolve(__dirname, '..', 'assets'),
    entry: {
        250: {
            import: [
                './js/250.ts',
                './js/VideoPlayer.js',
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
            './js/Filter.ts',
        ],
        home: [
            './css/home.less',
        ],
        search: ['./css/search.less'],
    },

    output: {
        path: path.resolve(__dirname, '..', 'site/c'),
        hashFunction: 'xxhash64',
    },

    plugins: [
        new MiniCssExtractPlugin(),
        new Dotenv({
            path: path.join(__dirname, '.env.local'),
            defaults: path.join(__dirname, '.env'),
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
                path.join(process.cwd(), 'site/**/*.*'),
                // Do not remove HTML files.
                '!' + path.join(process.cwd(), 'site/**/*.html'),
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
                                ],
                            },
                        },
                    },
                    {
                        loader: 'less-loader',
                        options: {
                            sourceMap: true,
                            lessOptions: {
                                strictUnits: true,
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
