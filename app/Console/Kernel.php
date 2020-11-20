<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helpers\Upload;
use Illuminate\Support\Facades\File;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        /*
         * Настроим крон на работу каждый час в 00 минуту.
         * По документации: * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
         * На сервере для php 7.4:
         * 1 * * * *
         * cd ~/site.ru/public_html && /usr/local/bin/php7.3 artisan schedule:run >> /dev/null 2>&1
         */

        // Код для проверки крон
        /*$schedule->call(function () {

            // Создайте файл /storage/test.log
            $logFile = storage_path('test.log');
            if (File::isFile($logFile)) {

                // Каждый час в первую минуть запишем дату в файл
                File::append($logFile, 'Cron ' . date('d-m-Y H:i') . PHP_EOL);
            }
        })->hourlyAt(1);*/


        // Резервное копирование веб-сайта
        //$schedule->command('backup:clean')->monthlyOn(1, '02:00');
        //$schedule->command('backup:run')->monthlyOn(1, '03:00');

        // Обновление веб-сайта (в 1 день месяца)
        /*$schedule->call(function () {
            Upload::getUpload();
        })->monthlyOn(1, '04:00');*/
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
