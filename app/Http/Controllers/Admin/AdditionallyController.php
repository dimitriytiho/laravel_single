<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\Commands;
use App\Helpers\Upload;
use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdditionallyController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c', 'route', 'view'));
    }


    public function index(Request $request) {

        // Работа с Кэшем
        $cache = $request->query('cache');
        if ($cache) {
            switch ($cache) {
                case 'db':
                    cache()->flush();
                    session()->flash('success', __('a.cache_deleted'));
                    return redirect()->route("admin.{$this->route}");

                case 'views':
                    $res = Commands::getCommand('php artisan view:clear');
                    $res ? session()->flash('success', $res) : session()->flash('error', __('s.something_went_wrong'));
                    return redirect()->route("admin.{$this->route}");

                case 'routes':
                    $res1 = Commands::getCommand('php artisan route:clear');
                    $res1 ? session()->flash('success', $res1) : session()->flash('error', __('s.something_went_wrong'));
                    return redirect()->route("admin.{$this->route}");

                case 'config':
                    $res1 = Commands::getCommand('php artisan config:clear');
                    $res1 ? session()->flash('success', $res1) : session()->flash('error', __('s.something_went_wrong'));
                    return redirect()->route("admin.{$this->route}");
            }
        }

        // Обновление сайта
        $upload = $request->query('upload');
        $uploadDisabled = null;
        if ($upload) {

            if ($upload === 'run') {
                Upload::getUpload();

                return redirect()
                    ->route('admin.additionally')
                    ->with('success', __('a.completed_successfully'));
            }
        }

        // Работа с Backup
        $backup = $request->query('backup');
        $backupDisabled = null;
        if ($backup) {

            if ($backup === 'run') {
                Artisan::call('backup:clean');
                Artisan::call('backup:run');

                return redirect()
                    ->route('admin.additionally')
                    ->with('success', __('a.completed_successfully'));
            }
        }

        // Работа с командами
        if ($request->isMethod('post')) {
            $command = $request->command ?? null;
            if ($command) {
                $res = Commands::getCommand($command);
                if ($res) {
                    return redirect()
                        ->route("admin.{$this->route}")
                        ->with('info', $res);
                }
            }

            // Сообщение об ошибке
            Main::getError('Request', __METHOD__, null);
            return redirect()
                ->route("admin.{$this->route}")
                ->withErrors(__('s.something_went_wrong'));
        }

        $f = __FUNCTION__;
        $title = __("a.{$this->view}");
        return view("{$this->viewPath}.{$this->route}.{$f}", compact('title'));
    }


    public function files(Request $request) {
        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->route}.{$f}", compact('title'));
    }
}
