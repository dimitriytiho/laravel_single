import settingsObj from './settings'
import validator from './validator'

document.addEventListener('DOMContentLoaded', function() {

    // К форме добавить класс .form_post
    const forms = document.querySelectorAll('form.form_post')
    if (forms[0]) {
        forms.forEach(function (form) {
            let name = form.getAttribute('name'), // Имя в теге form
                settings = settingsObj[name]
            validator(form, settings)
        })
    }

}, false)
