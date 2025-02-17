const mix = require('laravel-mix');


require('dotenv').config()


let webpack = require('webpack')

let dotenvplugin = new webpack.DefinePlugin({
    'process.env': {
        APP_NAME: JSON.stringify(process.env.APP_NAME || 'Laravel'),
        NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development'),
        GOOGLE_MAP_KEY: JSON.stringify(process.env.GOOGLE_MAP_KEY),
        STRIPE_KEY: JSON.stringify(process.env.STRIPE_KEY),
        STRIPE_SECRET: JSON.stringify(process.env.STRIPE_SECRET),
        SETTINGS: JSON.stringify(process.env.SETTINGS),
    }
})

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

mix.webpackConfig({
    plugins: [
        dotenvplugin,
    ]
})

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .sass('resources/sass/app.scss', 'public/css');
