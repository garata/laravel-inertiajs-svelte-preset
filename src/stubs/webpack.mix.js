const mix = require('laravel-mix');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.alias({
    ziggy: path.resolve('vendor/tightenco/ziggy/dist'), // or 'vendor/tightenco/ziggy/dist/vue' if you're using the Vue plugin
});

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig({
        experiments: {
            topLevelAwait: true
        },
        output: { chunkFilename: 'js/[name].js?id=[chunkhash]' },
        resolve: {
            mainFields: ['svelte', 'browser', 'module', 'main'],
            alias: {
                '@': path.resolve('resources/js'),
            },
        },
        module: {
            rules: [
                {
                    test: /\.(svelte)$/,
                    use: {
                        loader: 'svelte-loader',
                        options: {
                            emitCss: true,
                            hotReload: true,
                        },
                    },
                },
            ],
        },
    })
    .sourceMaps();