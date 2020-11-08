<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Form;
use App\Models\Main;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FormController extends Controller
{


    public function contactUs(Request $request)
    {
        $data = $request->all();

        // Валидация
        $rules = [
            'name' => 'required|string|max:250',
            'tel' => "required|tel|max:250",
            'email' => 'required|string|email|max:250',
            'message' => 'required', 'string',
            'accept' => 'accepted',
            //'g-recaptcha-response' => 'required|recaptcha',
        ];
        $request->validate($rules);

        // Сохраним пользователя отправителя формы. Если есть пользователь, то обновим его данные, если нет, то создадим.
        $user = UserAdmin::saveUser($request);
        $userId = $user->id ?? null;
        if (!$userId) {
            // Сообщение об ошибке
            return redirect()->back()->with('error', __('s.whoops'));
        }

        // Данные form
        $data['user_id'] = $userId;
        $data['message'] = s($data['message']);
        $data['ip'] = $request->ip();

        $form = new Form();
        $form->fill($data);

        //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
        if ($form->save()) {
            $format = config('admin.date_format') ?? 'd.m.Y H:i';
            $data['date'] = date($format);

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
                $formName = Str::snake(__FUNCTION__); // Из contactUs будет contact_us
                $template = 'table_form'; // Все данные в таблице
                $title = __('s.Completed_form', ['name' => $formName]) . config('add.domain');
                $email_admin = \App\Helpers\Str::strToArr(Main::site('admin_email'));

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
