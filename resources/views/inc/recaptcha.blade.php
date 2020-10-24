{{--


Google ReCaptcha, если есть в настройках указан секретный ключ --}}
@if (config('add.recaptcha_public_key'))
    <script src="//www.google.com/recaptcha/api.js?render={{ config('add.recaptcha_public_key') }}"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('add.recaptcha_public_key') }}', {action: 'homepage'}).then(function(token) {
                grecaptchaIds = document.querySelectorAll('input[type=hidden][data-id=g-recaptcha-response]')
                grecaptchaIds.forEach(function (el) {
                    el.value = token
                })
            })
        })
    </script>
@endif
