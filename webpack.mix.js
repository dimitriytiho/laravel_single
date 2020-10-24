const mix = require('laravel-mix');

mix
.js('resources/js/before/index.js', 'public/js/before.js')
.js('resources/js/index.js', 'public/js/app.js')
.sass('resources/sass/index.scss', 'public/css/app.css')
	.options({
		processCssUrls: false,
	})
;

/*mix.styles([
    'public/css/vendor/normalize.css',
    'public/css/vendor/videojs.css'
], 'public/css/all.css');*/

/*mix.scripts([
    'public/js/admin.js',
    'public/js/dashboard.js'
], 'public/js/all.js');*/

//mix.browserSync('127.0.0.1:8000');