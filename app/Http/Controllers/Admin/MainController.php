<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\App;
use App\Models\Main;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MainController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $this->c = strtolower($this->class);
        $view = $this->view = Str::snake($this->class);
        View::share(compact('class','view'));
    }


    public function index()
    {
        $key = Upload::getKeyAdmin();
        $f = __FUNCTION__;
        $title = __('a.dashboard');
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'key'));
    }


    // Записывает в куку локаль
    public function locale($locale)
    {
        if (in_array($locale, config('admin.locales') ?: [])) {
            return redirect()->back()->withCookie(Str::slug(config('add.name')) . '_loc', $locale);
        }
        Main::getError("Invalid locale $locale", __METHOD__);
    }


    // Записывает в куку
    public function getCookie(Request $request)
    {
        $key = $request->query('key');
        $val = $request->query('val');
        if ($key && $val) {
            return redirect()->back()->withCookie($key, $val);
        }
        return redirect()->back();
    }


    public function toChangeKey(Request $request)
    {
        $key = $request->key;
        $oldKey = Upload::getKeyAdmin();
        if ($key && $key !== $oldKey) {
            Upload::getNewKey($key);
            return redirect()->back()->with('success', __('a.key_success'));
        }
        return redirect()->back()->with('error', __('s.whoops_error'));
    }


    public function getSlug(Request $request)
    {
        if ($request->ajax()) {
            $slug = $request->slug;
            return App::cyrillicToLatin($slug);
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    public function pagination(Request $request)
    {
        $val = $request->input('val');
        if ((int)$val) {
            session()->put('pagination', (int)$val);
            return redirect()->back();
        }
        Main::getError('Request to', __METHOD__);
    }


    public function sidebarMini(Request $request)
    {
        $val = $request->input('val');
        $values = ['mini', 'full'];
        if ($val && in_array($val, $values)) {
            return redirect()->back()->withCookie('sidebar_mini', $val);
        }
        Main::getError('Request to', __METHOD__);
    }


    public function newOrder(Request $request)
    {
        if ($request->ajax()) {
            return DB::table('orders')
                ->whereNull('deleted_at')
                ->where('status', config('admin.order_statuses')[0])
                ->count();
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
