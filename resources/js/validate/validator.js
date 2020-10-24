import validate from 'validate.js'

export default function validator(form, settings) {
    const btn = form.querySelector('button[type=submit]')
    // btn.disabled = true

    // При убирании фокуса с input
    const inputs = form.querySelectorAll('input[type=text], input[type=tel], input[type=email], input[type=password], textarea')
    if (inputs[0]) {
        inputs.forEach(function (el) {
            el.addEventListener('blur', function() {
                handleForm(this)
            })
        })
    }


    // При клике на checkbox
    const inputsClick = form.querySelectorAll('input[type=checkbox], input[type=radio]')
    if (inputsClick[0]) {
        inputsClick.forEach(function (el) {
            el.addEventListener('click', function() {
                handleForm(this)
            })
        })
    }


    function handleForm(input) {
        const values = validate.collectFormValues(form),
            errors = validate(values, settings)

        if (errors) {
            const error = errors[input.getAttribute('name')]
            showErrorsForInput(input, error)
        } else {
            // btn.disabled = false
            showErrorsForInput(input)
        }
    }


    // При клике на отправить
    form.addEventListener('submit', function(ev) {
        ev.preventDefault()
        handleFormSubmit(this, ev)
    })


    function handleFormSubmit(form) {
        const values = validate.collectFormValues(form),
            errors = validate(values, settings)

        // Если нет ошибок
        if (!errors) {

            // Блокируется кнопка и отправка формы
            btn.disabled = true
            
            // Включаем спинер
            btn.innerHTML = spinnerBtn + btn.innerText
            /*if (btn.querySelector('.js-none')) {
                btn.querySelector('.js-none').style.display = 'inline-block'
            }*/

            // Отправил форму
            form.submit()
        }
        showErrors(form, errors || {})
    }

    function showErrors(form, errors) {
        const inputs = form.querySelectorAll('input, textarea')
        inputs.forEach(function (el) {
            showErrorsForInput(el, errors && errors[el.name])
        })
    }

    function showErrorsForInput(input, error) {
        const formGroup = input.closest('.form-group')
        if (formGroup) {
            const errorTag = formGroup.querySelector('.invalid-feedback')
            if (errorTag) {
                errorTag.textContent = error
            }
        }

        if (error) {
            //input.classList.remove('is-valid')
            input.classList.add('is-invalid')
        } else {
            input.classList.remove('is-invalid')
            //input.classList.add('is-valid')
        }
    }
}
