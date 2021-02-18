const path = require('path');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    mode: 'development',
    context: path.resolve(__dirname, 'assets'),
    entry: {
        250: [
            './js/250.js',
            './js/VideoPlayer.js',

            './css/inc/reset.css',
            './css/inc/250.less',
        ],
        internal: [
            './js/Internal.ts',
            './js/BuildMonitor.ts',
            './js/Filter.ts',
            './js/Home.js',
        ],
        home: [
            './css/home.less',
        ],
    },

    output: {
        path: path.resolve(__dirname, 'site/c'),
    },

    plugins: [
        new MiniCssExtractPlugin(),
        new CopyPlugin({
            patterns: [
                // Root directory assets.
                { from: '*', to: '..' },
                // Subdirectory assets. TODO: Exclude JS and CSS when they're no longer sourced externally.
                { from: '*/**/*.!(ts|less)', to: '..' }
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
