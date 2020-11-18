<?php

namespace App\Models;

use App\Mail\SendMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Upload extends App
{


    /**
     *
     * @return string
     *
     * Возвращает ключ для входа в admin.
     */
    public static function getKeyAdmin()
    {
        // Взязь из кэша
        if (cache()->has('key_for_site')) {
            return cache()->get('key_for_site');

        } else {

            // Запрос в БД
            $key = self::select('key')->orderBy('id', 'desc')->first();

            if (isset($key->key)) {

                // Кэшируется запрос
                cache()->forever('key_for_site', $key->key);

                return $key->key;
            }
        }
        return '';
    }


    /*
     * Сохраниться новый ключ для входа в admin, отправить письма админам.
     * $newKey - передать новый ключ, необязательный параметр, по-умолчанию сформируется ромдомный.
     * $mailAdmins - если не нужно отправлять письма администраторам и редакторам, то передать false, необязательный параметр.
     */
    public static function getNewKey($newKey = null, $mailAdmins = true)
    {
        $upload = new self();
        $key = $upload->key = $newKey ?: Str::lower(Str::random(18));
        $upload->save();


        // Удалить все кэши
        cache()->flush();


        // Отправить письмо всем admins
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
}
