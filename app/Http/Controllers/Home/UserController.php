<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Helpers\Admin\Img;
use App\Helpers\Date;
use App\Models\UserLastData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends AppController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\{$class}";
        $table = $this->table = with(new $model)->getTable();
        $view = $this->view = Str::snake($this->c);

        view()->share(compact('class', 'c', 'model', 'table', 'view'));
    }


    public function index()
    {
        $values = $this->model::findOrFail(auth()->user()->id);
        $f = Str::snake(__FUNCTION__);
        $title = __('s.personal_info');

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->add([[route("home.{$this->c}_index") => $title]])
            ->add([[route('home.index') => 'account']])
            ->get();

        return view("{$this->viewPath}.{$this->view}_{$f}", compact('title', 'breadcrumbs', 'values'));
    }


    public function edit(Request $request)
    {
        // Если у пользователя есть разрешение редактирования пользователей в админке, то здесь ему нельзя редактировать
        if (checkPermission($this->class)) {
            return back()->withErrors(__('s.action_is_not_available'));
        }


        $id = auth()->user()->id;
        $values = $this->model::findOrFail($id);
        $imagesExt = implode(',', config('admin.acceptedImagesExt') ?? []);

        $rules = [
            'name' => 'required|string|max:250',
            'email' => "required|string|email|unique:{$this->table},email,{$id}|max:250",
            'tel' => 'required|tel|max:250',
            'password' => 'nullable|string|min:6|same:password_confirmation',
            'img' => "nullable|mimes:{$imagesExt}|max:2000",
        ];

        // Если есть ключ Recaptcha и не локально запущен сайт
        if (config('add.env') !== 'local' && config('add.recaptcha_public_key')) {
            $rules += [
                'g-recaptcha-response' => 'required|recaptcha',
            ];
        }

        $request->validate($rules);

        // Дата рождения
        $request->date_of_birth = Date::toTimestamp($request->date_of_birth);

        $data = $request->all();


        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class, $values->img);

        } else {

            // Если нет картинки
            $data['img'] = $values->img;
        }

        // Поле подтверждение пароля удаляется
        unset($data['password_confirmation']);

        // Если есть пароль, то он хэшируется
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $values->fill($data);

        // Сохраняем предыдущие данные пользователя, если данные были изменены
        UserLastData::diffSaveLastUser($values);

        // Обновляем элемент
        $values->update();

        // Сообщение об успехе
        return redirect()
            ->route('home.user_index')
            ->with('success', __('s.successfully_changed'));
    }
}
