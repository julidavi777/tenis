const mix = require('laravel-mix');

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
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
        require('autoprefixer')
    ])

    .js('./node_modules/flowbite/dist/flowbite.js', 'public/js')
    .postCss('./node_modules/flowbite/dist/flowbite.css', 'public/css')
    .js('./node_modules/flowbite/dist/datepicker.js', 'public/js')
    .js('resources/js/inscripciones/Jugador.js', 'public/js/inscripciones')
    .browserSync('localhost:8000');
