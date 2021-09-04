const mix = require('laravel-mix');

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

// Set project paths
// const localDomain = 'https://outlay.lan';

mix
    // Utilities
    .sourceMaps()
    .options({
        processCssUrls: false
    })

    // Add global libraries
    .autoload({
        jquery: ['$', 'jQuery'],
        uikit: 'UIkit',
        flatpickr: 'flatpickr',
    })

    // Suppress success messages
    .disableSuccessNotifications()

    // Compile Javascript (ES6)
    .js('resources/js/app.js', 'public/js').extract()

    /// Combine and minify DataTables scripts
    .combine('resources/js/datatables/*.js', 'public/js/datatables.bundle.js')
    .minify('public/js/datatables.bundle.js', 'public/js')

    /// Chart scripts
    .combine('resources/js/charts/*.js', 'public/js/charts.bundle.js')
    .minify('public/js/charts.bundle.js', 'public/js')

    // Compile Sass
    .sass('resources/scss/app.scss', 'public/css')

    /// Lightweight print style
    .sass('resources/scss/print.scss', 'public/css')

// .copy('resources/img', 'public/img')
// .copy('resources/fonts', 'public/fonts')

// Setup BrowserSync
// .browserSync({
//   proxy: localDomain,
//   host: localDomain.replace(/^https?:\/\//, ''),
//   notify: false,
//   open: false,
//   injectChanges: true,
//   // https: {
//   //   key: '/Users/YOUR_COMPUTER/.config/valet/Certificates/YOUR_SITE.test.key',
//   //   cert: '/Users/YOUR_COMPUTER/.config/valet/Certificates/YOUR_SITE.test.crt'
//   // }
// })

// Setup versioning (cache-busting)
if (mix.inProduction()) {
    mix.version()
}
