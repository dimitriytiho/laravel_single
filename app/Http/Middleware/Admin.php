<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
    /**
     *
     * Проверяет доступ для Админов и Редакторов.
     *
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //return $next($request); // Чтобы отключить раскомментируйте

        if (auth()->check() && auth()->user()->Admin()) {

            // Продолжим
            return $next($request);
        }

        // Вернём ошибку 404
        abort(404);

        // Запишем в логи и показажем страницу 404
        //Main::getError('Request Admin', __METHOD__, false);

        //return redirect()->route('not_found');
    }
}
