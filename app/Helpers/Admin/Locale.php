<?php


namespace App\Helpers\Admin;

use App\Models\Main;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class Locale
{
    private $currentLocale;
    private $locales;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->locales = config('admin.locales') ?: [];
    }


    public static function excludeCurrentLocale()
    {
        $self = new self();
        $currentLocale = $self->currentLocale;
        $locales = $self->locales;
        if (in_array($currentLocale, $locales)) {
            unset($locales[array_search($currentLocale, $locales)]);
        }
        return array_values($locales);
    }


    public static function setLocaleFromCookie($request)
    {
        $self = new self();
        $currentLocale = $self->currentLocale;
        $locales = $self->locales;
        $locale = $request->cookie(config('add.name') . '_loc');
        if ($locale) {

            try {
                $locale = Crypt::decryptString($locale);
            } catch (DecryptException $e) {
                Main::getError('Error Crypt::decryptString', __METHOD__, false);
            }

            if ($locale !== $currentLocale && in_array($locale, $locales)) {
                app()->setLocale($locale);
            }
        }
    }
}
