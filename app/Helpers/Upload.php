<?php


namespace App\Helpers;


use App\Mail\SendMail;
use App\Models\Main;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Upload
{
    // Запусть этот метод, чтобы обновить сайт \App\Helpers\Upload::getUpload();
    public static function getUpload()
    {
        self::sitemap();
        self::robots();
        self::human();
        self::errorPage();
        //self::htaccess();

        // Обновление ключа, если в настройках change_key отмечено 1
        if (Main::site('change_key')) {
            self::getNewKey();
        }
    }


    // Формирует переменые для sass из настроек, записываем в файл /resources/sass/config/_init.scss.
    public static function resourceInit()
    {
        $sassParams = config('add.scss');
        if ($sassParams) {
            $p = "\n// Settings SASS from \\" . __METHOD__ . "();\n\n";
            foreach ($sassParams as $k => $v) {
                $p .= "\${$k}: {$v};\n";
            }
            $p .= "\$path-img: '" . config('add.img', 'img') . "';\n";

            // Записываем файл _init.scss
            $fileSassInit = resource_path('sass/config/_init.scss');
            if (File::exists(($fileSassInit))) {
                File::replace($fileSassInit, $p);
            }
        }
    }


    // Сформировать карту сайта
    public static function sitemap()
    {
        $itemsDb = config('add.list_of_information_block.tables');
        $routesDb = config('add.list_of_information_block.routes');
        $items = config('add.list_pages_for_sitemap_no_db.items');
        $routes = config('add.list_pages_for_sitemap_no_db.routes');
        $active = config('add.page_statuses')[1] ?: 'active';
        $date = date('Y-m-d');

        $r = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $r .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        if ($itemsDb) {
            foreach ($itemsDb as $key => $table) {

                if (Schema::hasTable($table)) {

                    $route = Route::has($routesDb[$key]) ? $routesDb[$key] : null;
                    $values = DB::table($table)->where('status', $active)->pluck('slug')->toArray();

                    if ($route && $values) {
                        foreach ($values as $slug) {
                            $r .= "\t<url>\n\t\t";
                            $r .= '<loc>' . route($route, $slug) . "</loc>\n\t\t";
                            $r .= "<lastmod>{$date}</lastmod>\n";
                            $r .= "\t</url>\n";
                        }
                    }
                }
            }
        }

        if ($items) {
            foreach ($items as $key => $page) {

                $route = Route::has($routes[$key]) ? $routes[$key] : null;
                if ($route) {
                    $r .= "\t<url>\n\t\t";
                    $r .= '<loc>' . route($route) . "</loc>\n\t\t";
                    $r .= "<lastmod>{$date}</lastmod>\n";
                    $r .= "\t</url>\n";
                }
            }
        }
        $r .= '</urlset>';

        // Создать файл
        Storage::disk('public_folder')->put('sitemap.xml', $r);

        // Создать архив
        $data = implode('', file(public_path('sitemap.xml')));
        $gzdata = gzencode($data, 9);
        Storage::disk('public_folder')->put('sitemap.xml.gz', $gzdata);
    }


    // Сформировать robots.txt
    public static function robots()
    {
        $index = config('add.not_index_website'); // Если не нужно индексировать сайт, то true, если нужно, то false
        $disallow = config('add.disallow');

        $disallow[] = 'not-found';
        $disallow[] = '*.php$';
        $disallow[] = 'js/*.js$';
        $disallow[] = 'css/*.css$';
        $r = 'User-agent: *' . PHP_EOL;
        $url = config('add.url', '/');

        // Если не индексировать
        if ($index) {
            $r .= 'Disallow: /';

        // Если индексировать
        } else {

            if ($disallow) {
                foreach ($disallow as $v) {
                    $r .= "Disallow: /{$v}" . PHP_EOL;
                }
            }

            $r .= PHP_EOL . "Host: {$url}" . PHP_EOL;
            $r .= "Sitemap: {$url}/sitemap.xml" . PHP_EOL;
            $r .= "Sitemap: {$url}/sitemap.xml.gz";
        }
        Storage::disk('public_folder')->put('robots.txt', $r);
    }


    // Сформировать humans.txt
    public static function human()
    {
        $values = config('add.development');
        if ($values && is_array($values)) {
            $r = '';
            foreach ($values as $k => $v) {
                $r .= "{$k}: {$v}\n";
            }
            $r .= 'Last update: ' . date('Y-m-d') . PHP_EOL;
            Storage::disk('public_folder')->put('humans.txt', $r);
        }
    }


    // Создаётся файл /error.php и в нём вид error из /app/Modules/views/errors/preventive.blade.php
    public static function errorPage()
    {
        $noMain = true;
        if (view()->exists('errors.preventive')) {
            $r = view('errors.preventive')
                ->with(compact('noMain'))
                ->render();
            $file = base_path('error.php');

            // Если есть файл, то перезапишем его
            if (File::isFile($file)) {
                File::replace($file, $r);

                // Иначе создадим файл и запишем в него
            } else {
                File::put($file, $r);
            }
        }
    }


    // Возвращает ключ для входа в admin
    public static function getKeyAdmin()
    {
        //return true;
        //return 'testing';
        //$key = 'testing';

        //dd(\Illuminate\Support\Facades\Crypt::decryptString('eyJpdiI6IlZ3XC9EclVUZUFUQXZCMHpwSWVOSjVnPT0iLCJ2YWx1ZSI6IkJwckY0bGpLTEZ6aHRkYWhrdmRRaWc9PSIsIm1hYyI6ImNjNjdmMDQ4ZTg3ZjEzOTQ0ZGFkNDdkZDJlMTMwZjYzNjkxODdmMjMyNDIwN2I4ODdkYWQxZTc5Mzg5NGZlMzUifQ=='));

        //$key = Crypt::encryptString($key); // Зашифровать
        //$key = Crypt::decryptString($key->key); // Расшифровать


        // Взязь из кэша
        if (cache()->has('key_for_site')) {
            return cache()->get('key_for_site');

        } else {

            // Запрос в БД
            $key = DB::table('uploads')->select('key')->orderBy('id', 'desc')->first();

            if (isset($key->key)) {

                // Кэшируется запрос
                cache()->forever('key_for_site', $key->key);

                return $key->key;
            }
        }
        return false;
    }


    /*
     * Сохраниться новый ключ для входа в admin.
     * $newKey - передать новый ключ, необязательный параметр, по-умолчанию сформируется ромдомный.
     * $mailAdmins - если не нужно отправлять письма администраторам и редакторам, то передать false, необязательный параметр.
     */
    public static function getNewKey($newKey = null, $mailAdmins = true)
    {
        $upload = new \App\Models\Upload();
        $key = $upload->key = $newKey ?: Str::lower(Str::random(18));
        $upload->save();


        // Удалить все кэши
        cache()->flush();


        // Отправить письмо всем admin и editor
        if ($mailAdmins) {
            try {
                $roleIds = User::roleIdAdmin();
                $emails = DB::table('users')->select('email')->whereIn('role_id', $roleIds)->get();
                $emails = $emails->toArray();

                if ($emails) {
                    Mail::to($emails)->send(new SendMail(__("a.Key_use_site") . config('add.domain'), $key));
                }
            } catch (\Exception $e) {
                Log::error("Error sending email {$e}, in " . __METHOD__);
            }
        }
    }


    // Сформировать .htaccess
    public static function htaccess()
    {
        $banned_ip = config('add.banned_ip');
        $r = '';
        if (!empty($banned_ip[0])) {
            $r .= '# ===== Closing by ip on the server =====' . PHP_EOL;
            $r .= 'Order Allow,Deny' . PHP_EOL;
            $r .= 'Allow from all' . PHP_EOL;
            $part = '';
            foreach ($banned_ip as $v) {
                $part .= "{$v}, ";
            }
            $part = 'Deny from ' . rtrim($part, ', ');
            $r .= $part . PHP_EOL . PHP_EOL;
        }

        $r .= 'addDefaultCharset utf-8' . PHP_EOL . PHP_EOL;
        $r .= 'ErrorDocument 404 /not-found' . PHP_EOL;
        $r .= 'ErrorDocument 403 /not-found' . PHP_EOL;
        $r .= 'ErrorDocument 500 /error.php' . PHP_EOL . PHP_EOL;
        $r .= 'RewriteEngine On' . PHP_EOL . PHP_EOL;

        if (config('add.protocol') == 'https') {
            $r .= '#no http and www' . PHP_EOL;
            $r .= 'RewriteCond %{HTTPS} off' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP:X-Forwarded-Proto} !https' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP_HOST} ^www\.(.*)$' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ https://%1/$1 [R=301,L]' . PHP_EOL;

        } else {
            $r .= '#no www' . PHP_EOL;
            $r .= 'RewriteCond %{HTTP_HOST} ^www\.(.*)$' . PHP_EOL;
            $r .= 'RewriteRule ^(.*)$ http://%1/$1 [R=301,L]' . PHP_EOL . PHP_EOL;
        }

        $r .= PHP_EOL . 'RewriteCond %{REQUEST_URI} !^public' . PHP_EOL;
        $r .= 'RewriteRule ^(.*)$ public/$1 [L]' . PHP_EOL . PHP_EOL;

        // Если индексирование сайта выключено
        if (config('add.not_index_website')) {
            $r .= PHP_EOL . 'SetEnvIfNoCase User-Agent "^Googlebot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Yandex" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Yahoo" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Aport" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^msnbot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^spider" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Robot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^php" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Mail" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^bot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^igdeSpyder" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Snapbot" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^WordPress" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^BlogPulseLive" search_bot' . PHP_EOL;
            $r .= 'SetEnvIfNoCase User-Agent "^Parser" search_bot' . PHP_EOL;
        }
        Storage::disk('root')->put('.htaccess', $r);
    }
}
