// При изменении настроек в config/modules.php modules необходимо запустить метод \App\Helpers\Upload::resourceInit();

const mix = require('laravel-mix');

mix
.js('app/Modules/js/before/index.js', 'public/js/before.js')
.js('app/Modules/js/index.js', 'public/js/app.js')
.sass('app/Modules/sass/index.scss', 'public/css/app.css')
	.options({
		processCssUrls: false,
	})

.js('app/Modules/Admin/js/index.js', 'public/js/append.js')
.sass('app/Modules/Admin/sass/index.scss', 'public/css/append.css')
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
