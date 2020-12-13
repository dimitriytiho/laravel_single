<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Form;
use App\Models\Main;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helpers\Str as HelpersStr;

class FormController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $this->c = Str::lower($this->class);
        $this->model = "{$this->namespaceModels}\\{$class}";
        $this->table = with(new $this->model)->getTable();
    }


    public function contactUs(Request $request)
    {
        $f = Str::snake(__FUNCTION__);

        // Валидация
        $rules = [
            'message' => 'required', 'string',
        ];

        // Если пользователь авторизован, то добавляем его данные
        if (auth()->check()) {

            $request->merge([
                'name' => auth()->user()->name,
                'tel' => auth()->user()->tel,
                'email' => auth()->user()->email,
                'accept' => auth()->user()->accept,
            ]);

        } else {

            $rules = [
                'name' => 'required|string|max:250',
                'tel' => 'required|tel|max:250',
                'email' => 'required|string|email|max:250',
                'accept' => 'accepted',
            ];
        }

        // Если есть ключ Recaptcha и не локально запущен сайт
        if (config('add.env') !== 'local' && config('add.recaptcha_public_key')) {
            $rules += [
                'g-recaptcha-response' => 'required|recaptcha',
            ];
        }
        $request->validate($rules);

        $data = $request->all();

        // Сохраним пользователя отправителя формы. Если есть пользователь, то обновим его данные, если нет, то создадим.
        $user = UserAdmin::saveUser($request);
        $userId = $user->id ?? null;
        if (!$userId) {
            // Сообщение об ошибке
            return redirect()->back()->with('error', __('s.whoops'));
        }

        // Данные form
        $data['user_id'] = $userId;
        $data['ip'] = $request->ip();
        if (!empty($data['message'])) {
            $data['message'] = s($data['message']);
        }

        $form = new Form();
        $form->fill($data);

        //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
        if ($form->save()) {
            $data['date'] = d(time(), config('admin.date_format') ?: 'dd.MM.y HH:mm');

            // Письмо пользователю
            try {
                $title = __('s.You_have_filled_out_form') . config('add.domain');
                $body = __('s.Your_form_has_been_received');

                Mail::to($data['email'])
                    ->send(new SendMail($title, $body));

            } catch (\Exception $e) {
                Main::getError("Error sending email User: {$e}", __METHOD__, false);
            }

            // Письмо администратору
            try {
                $formName = l($f, 's'); // Из contactUs будет contact_us
                $template = 'table_form'; // Все данные в таблице
                $title = __('s.Completed_form', ['name' => $formName]) . config('add.domain');
                $email_admin = HelpersStr::strToArr(Main::site('admin_email'));

                if ($email_admin) {
                    Mail::to($email_admin)
                        ->send(new SendMail($title, null, $data, $template));
                }

            } catch (\Exception $e) {
                Main::getError("Error sending email Admin: {$e}", __METHOD__, false);
            }

            // Сообщение об успехе
            return redirect()->route('index')->with('success', __('s.Your_form_successfully'));
        }
    }
}
