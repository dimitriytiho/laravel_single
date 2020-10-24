
// Настройки составлять для каждой разной формы отдельно, при это в теге form в name="enter_email" - написать имя формы, если много одинаковых форм на странице (Если форма другая, то написать другой name

//var translations = {'accepted': '^Вы должны принять','email': '^Должно быть действительным электронным адресом','required': '^Обязательно для заполнения','min': '^Количество символов должно быть не менее '}

const validateSettings = {
    enter: {
        email: {
            presence: {
                message: translations['required'] // '^characters' ^ без названия поля
            },
            email: {
                message: translations['email']
            }
        },
        password: {
            presence: {
                message: translations['required']
            }
        }
    }/*,
    enter_email: {
        email: {
            presence: {
                message: translations['required'] // '^characters' ^ без названия поля
            },
            email: {
                message: translations['email']
            }
        }
    },
    enter_code: {
        confirm: {
            presence: {
                message: translations['required']
            }
        }
    },
    enter_pass: {
        password: {
            presence: {
                message: translations['required']
            }
        }
    }*/
}

export default validateSettings
