require('dotenv').config();
const mix = require('laravel-mix');
let productionSourceMaps = true;

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/scss/all.scss', 'public/css')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .sourceMaps(productionSourceMaps, 'source-map')
    .copy('resources/images', 'public/images')
    .copy('resources/favicons', 'public/favicons')
    .copy('resources/favicons/favicon.ico', 'public/favicon.ico');

if (mix.inProduction()) {
    mix.version();
}
if (process.env.APP_ENV !== 'production') {
    mix.browserSync(process.env.APP_URL + (process.env.APP_PORT ? ':' + process.env.APP_PORT : ''));
}
