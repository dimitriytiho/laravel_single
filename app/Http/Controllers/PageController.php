<?php

namespace App\Http\Controllers;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\{$class}";
        $table = $this->table = with(new $model)->getTable();
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c', 'model', 'table', 'view'));
    }


    public function index()
    {
        //dump(session()->all());

        //Mail::to('dimitriyyuliya@gmail.com')->send(new SendMail(__("{$this->lang}::a.Code"), '12345'));

        /*$mobileDetect = new \Mobile_Detect();
        dump($mobileDetect->isMobile());
        dump($mobileDetect->isTablet());*/


        $title = __('s.' . config('add.title_main'));

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->get();

        $this->setMeta($title, __('s.You_are_on_home'));
        return view("{$this->c}.index", compact('breadcrumbs'));
    }


    public function show($slug)
    {
        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $values = $this->model::where('slug', $slug)->firstOrFail();

        } else {
            $values = $this->model::where('slug', $slug)->where('status', $this->statusActive)->firstOrFail();
        }


        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /app/Modules/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $values->body = Main::inc($values->body, $values);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $values->body = Main::getDownScript($values->body);


        // Передаём в контейнер id элемента
        Main::set('id', $values->id);

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->dynamic($values->id)
            ->get();

        $this->setMeta($values->title ?? null, $values->description ?? null);
        return view("{$this->c}.show", compact('values', 'breadcrumbs'));
    }


    public function contactUs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Валидация
            $rules = [
                'name' => 'required|string|max:190',
                'tel' => 'required|string|max:190',
                'email' => 'required|string|email|max:190',
                'message' => 'required', 'string',
                'accept' => 'accepted',
                //'g-recaptcha-response' => 'required|recaptcha',
            ];
            $request->validate($rules);

            //unset($data['g-recaptcha-response']);

            /*if (Form::googleReCaptcha($data)) {

                // code form...
            }*/

            /*$data['accept'] = $data['accept'] ? '1' : '0'; // В чекбокс запишем 1
            $data['ip'] = $request->ip();

            // Сохраним пользователя отправителя формы. Если есть пользователь, то обновим его данные, если нет, то создадим. Также если пользователь Админ или Редактор, то не будем обновлять его данные.
            $userId = Form::userFormSave($data);
            if (!$userId) {
                Main::getError($this->class, __METHOD__);
            }

            // Данные form
            $dataForm['user_id'] = $userId;
            $dataForm['message'] = s($data['message']);
            $dataForm['ip'] = $request->ip();

            $form = new Form();
            $form->fill($dataForm);

            //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
            if ($form->save()) {
                $format = config('admin.date_format') ?? 'd.m.Y H:i';
                $data['date'] = date($format);

                // Письмо пользователю
                try {
                    $title = __("{$this->lang}::s.You_have_filled_out_form") . config('add.domain');
                    $body = __("{$this->lang}::s.Your_form_has_been_received");

                    Mail::to($data['email'])
                        ->send(new SendMail($title, $body));

                } catch (\Exception $e) {
                    Main::getError("Error sending email User: {$e}", __METHOD__, false);
                }

                // Письмо администратору
                try {
                    $formName = Str::snake(__FUNCTION__); // Из contactUs будет contact_us
                    $template = 'table_form'; // Все данные в таблице
                    $title = __("{$this->lang}::s.Completed_form", ['name' => $formName]) . config('add.domain');
                    $email_admin = HelpersStr::strToArr(Main::site('admin_email'));

                    if ($email_admin) {
                        Mail::to($email_admin)
                            ->send(new SendMail($title, null, $data, $template));
                    }

                } catch (\Exception $e) {
                    Main::getError("Error sending email Admin: {$e}", __METHOD__, false);
                }

                // Сообщение об успехе
                return redirect()->route('index')->with('success', __("{$this->lang}::s.Your_form_successfully"));
            }*/
        }

        $title = __('s.contact_us');

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['contact_us' => $title])
            ->get();

        $this->setMeta($title);
        return view("{$this->c}.contact_us", compact('breadcrumbs'));
    }


    // Записать куку через Ajax, после получения ответа перезагрузите страницу
    public function setCookie(Request $request)
    {
        if ($request->ajax()) {
            $name = $request->name ?? null;
            $value = $request->value ?? null;

            if ($name && $value) {

                // Ставим на очередь создание куки
                cookie()->queue($name, $value);
                return 1;
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
