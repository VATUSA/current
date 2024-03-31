let mix = require('laravel-mix');

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

mix.js('resources/assets/js/privacy.js', 'public/js')
  //.js('resources/assets/js/vatusa.js', 'public/js')
  //.babel('resources/assets/js/training.js', 'public/js/training.js')
  .sass('resources/assets/sass/vatusa.scss', 'public/css')

if (mix.inProduction()) {
  mix.version();
}
