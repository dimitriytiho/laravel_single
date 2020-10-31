
document.addEventListener('DOMContentLoaded', function() {

    // Маска для телефона
    $('input[type=tel]').inputmask('+7(999)999-99-99')


    // Валидация
    $.validator.setDefaults({

        /*beforeSubmit: function () {

        }*/

        // Действия после успешного ввода
        submitHandler: function () {

            // Заблокируем кнопку, включим спиннер
            $('form.needs-validation button[type=submit]')
                .attr('disabled', true)
                .prepend(spinnerBtn)

            return true
        }
    });


    // Добаляем валидатор для номера телефона
    $.validator.methods.checkTel = function(value, element) {
        return this.optional(element) || /^[\+\(\)\- 0-9]+$/.test( value)
    }


    // Правила валидации
    $('.needs-validation').validate({
        rules: {
            title: {
                required: true
            },
            slug: {
                required: true
            },
            /*value: {
                required: true
            },*/
            email: {
                required: true,
                email: true
            },
            tel: {
                //required: true,
                checkTel: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password_confirmation : {
                required: true,
                minlength : 6,
                equalTo : "#password"
            },
            accept: {
                required: true
            },
        },
        messages: {
            title: {
                required: translations['required']
            },
            slug: {
                required: translations['required']
            },
            value: {
                required: translations['required']
            },
            email: {
                required: translations['required'],
                email: translations['email']
            },
            tel: {
                required: translations['required'],
                checkTel: translations['tel']
            },
            password: {
                required: translations['required'],
                minlength: translations['min6']
            },
            password_confirmation: {
                required: translations['required'],
                minlength: translations['min6'],
                equalTo: translations['password_confirm_must_match']
            },
            accept: translations['accepted']
        },
        onfocusout: function(element) {
            this.element(element);
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
            element.closest('.input-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    })

}, false)
