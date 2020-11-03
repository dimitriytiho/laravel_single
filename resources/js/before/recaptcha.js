
if (recaptchaKey && recaptchaV === 3) {
    grecaptcha.execute(recaptchaKey, {action: 'homepage'}).then(function(token) {
        grecaptchaIds = document.querySelectorAll('input[type=hidden][name="g-recaptcha-response"]')
        grecaptchaIds.forEach(function (el) {
            el.value = token
        })
    })
}
