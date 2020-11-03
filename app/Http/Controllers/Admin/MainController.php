<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\App;
use App\Models\Main;
use App\Models\Upload;
use Illuminate\Http\Request;
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
        $title = __('a.Dashboard');
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'key'));
    }


    // Записывает в куку локаль
    public function locale($locale)
    {
        if (in_array($locale, config('admin.locales') ?: [])) {
            return redirect()->back()->withCookie(config('add.name') . '_loc', $locale);
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
        if ($request->ajax()) {
            $key = $request->key;
            if ($key) {
                Upload::getNewKey($key);
                session()->flash('success', __('a.key_success'));
                return __('a.key_success');
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
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
}
