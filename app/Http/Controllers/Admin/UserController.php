<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\App;
use App\Helpers\Admin\DbSort;
use App\Helpers\Admin\Img;
use App\Models\Role;
use App\Models\UserAdmin;
use App\Models\UserLastData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends AppController
{
    public static $guardedLast = ['id', 'note', 'accept', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'];


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\UserAdmin";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c','model', 'table', 'route', 'view'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'name',
            'email',
            'tel',
            'ip',
            'id',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Метод для поиска и сортировки запроса БД
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);

        // Передать поля для вывода, значение l - с переводом, t - дата
        $thead = [
            'name' => null,
            'email' => null,
            'tel' => null,
            'ip' => null,
            'id' => null,
        ];

        // Не показываем кнопки удаления
        //$deleteBtn = true;

        $f = __FUNCTION__;
        $title = __('a.' . Str::ucfirst($this->table));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values', 'queryArr', 'col', 'cell', 'thead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Статусы пользователей
        $statuses = config('admin.user_statuses');

        // Роли пользователей
        $roles = Role::pluck('title', 'id');

        // Если не Админ, то запишим id роли Админ
        $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->roleAdminId() : null;

        $f = __FUNCTION__;
        $title = __('a.' . Str::ucfirst($f));
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'roles', 'statuses', 'roleIdAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $magesExt = implode(config('admin.acceptedImagesExt') ?? [], ',');

        $rules = [
            'name' => 'required|string|max:250',
            'email' => "required|string|email|unique:{$this->table},email|max:250",
            'tel' => 'nullable|tel|max:250',
            'password' => 'required|min:6|same:password_confirmation',
            'img' => "nullable|mimes:{$magesExt}", // |max:2000
            //'tel' => 'required|string|max:250',
        ];
        $request->validate($rules);
        $data = $request->all();


        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class);

        } else {

            // Если нет картинки
            $data['img'] = config("admin.img{$this->class}Default");
        }

        // Если нет картинки
        if (empty($data['img'])) {
            $data['img'] = config("admin.img{$this->class}Default");
        }

        // Поле подтверждение пароля удаляется
        unset($data['password_confirmation']);

        // Если есть пароль, то он хэшируется
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        // Создаём экземкляр модели
        $values = new UserAdmin();

        // Заполняем модель новыми данными
        $values->fill($data);


        // Если не Админ выбирает роль Админ, то ошибка или Если не Админ редактирует Админа
        if ($values->noAdmintoAdmin($request->role_ids) || $values->noAdminEditAdmin()) {

            // Сообщение об ошибке
            return redirect()
                ->back()
                ->with('error', __('s.admin_choose_admin'));
        }


        // Если нет роли, то по умолчанию назначим роль Гость
        $roleIds = $request->role_ids;
        if (!$roleIds) {
            $roleIds[0] = $values->roleGuestId();
        }


        // Сохраняем элемент
        $values->save();


        // Сохраняем роли
        $values->roles()->sync($request->role_ids);


        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.created_successfully', ['id' => $values->id]));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $values = $this->model::with('roles')->findOrFail($id);

        // Статусы пользователей
        $statuses = config('admin.user_statuses');

        // Роли пользователей
        $roles = Role::pluck('title', 'id');


        // DROPZONE DATA
        // Передаём начальную часть названия для передаваемой картинки Dropzone JS
        $imgRequestName = $this->imgRequestName = App::cyrillicToLatin($values->name, 32);

        // ID элемента, для которого картинка Dropzone JS
        $imgUploadID = $this->imgUploadID = $values->id;

        // Если не Админ, то запишим id роли Админ
        $roleIdAdmin = !auth()->user()->isAdmin() ? auth()->user()->roleAdminId() : null;

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'roles', 'statuses', 'imgRequestName', 'imgUploadID', 'roleIdAdmin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        $magesExt = implode(config('admin.acceptedImagesExt') ?? [], ',');

        $rules = [
            'name' => 'required|string|max:250',
            'email' => "required|string|email|unique:{$this->table},email,{$id}|max:250",
            'tel' => 'nullable|tel|max:250',
            'password' => 'nullable|min:6|same:password_confirmation',
            'img' => "nullable|mimes:{$magesExt}", // |max:2000
            //'tel' => 'required|string|max:250',
        ];
        $request->validate($rules);
        $data = $request->all();


        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class, $values->img);

        } else {

            // Если нет картинки
            $data['img'] = config("admin.img{$this->class}Default");
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

        // Если не Админ выбирает роль Админ, то ошибка или Если не Админ редактирует Админа
        if ($values->noAdmintoAdmin($request->role_ids) || $values->noAdminEditAdmin()) {

            // Сообщение об ошибке
            return redirect()
                ->route("admin.{$this->route}.edit", $values->id)
                ->with('error', __('s.admin_choose_admin'));
        }

        // Сохраняем предыдущие данные пользователя, если данные были изменены
        UserLastData::diffSaveLastUser($values);

        // Если нет роли, то по умолчанию назначим роль Гость
        $roleIds = $request->role_ids;
        if (!$roleIds) {
            $roleIds[0] = $values->roleGuestId();
        }

        // Сохраняем роли
        $values->roles()->sync($roleIds);


        // Обновляем элемент
        $values->update();
        // Если меняются данные текущего пользователя, то изменим их в объекте auth
        if ($values->id === auth()->user()->id) {
            $auth = auth()->user()->toArray();
            if ($auth) {
                unset($auth['img']); // Удалим из массива картинку, т.к. она меняется сразу при смене картинки
                foreach ($auth as $authKey => $authValue) {
                    if (isset($data[$authKey]) && $data[$authKey] != $authValue) {
                        auth()->user()->update([$authKey => $data[$authKey]]);
                    }
                }
            }
        }

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.saved_successfully', ['id' => $values->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);
        $img = $values->img ?? null;


        // Если не Админ удаляет Админа
        if ($values->noAdmintoAdmin()) {

            // Сообщение об ошибке
            return redirect()
                ->back()
                ->with('error', __('s.admin_choose_admin'));
        }


        // Если у пользователя есть формы, то ошибка
        if ($values->forms && $values->forms->count()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->with('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }


        // Удаляем элемент
        $values->delete();

        // Удалим картинку с сервера, кроме картинки по-умолчанию
        Img::deleteImg($img, config("admin.img{$this->class}Default"));

        // Если пользователь удаляет сам себя
        if (auth()->user()->id == $id) {
            return redirect()->route('logout');
        }

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }


    // Разлогинить пользователя
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        return redirect()->route('index');
    }
}
