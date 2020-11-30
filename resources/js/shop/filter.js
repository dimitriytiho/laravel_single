import alert from '../default/alert'


document.addEventListener('DOMContentLoaded', function() {

    const filterClass = 'filter',
        productsClass = 'products'


    body.on('change', '.' + filterClass + ' input', function () {
        const checked = $('.' + filterClass + ' input:checked')
        let data = ''

        // Получаем отмеченные input
        checked.each(function () {
            data += this.value + ','
        })

        if (data) {

            $.ajax({
                url: location.href,
                type: 'GET',
                data: {filter: data},
                beforeSend: function () {
                    spinner.fadeIn(300, function () {
                        $('.' + productsClass).hide()
                    })
                },
                success: function (res) {

                    spinner.delay(500).fadeOut(300, function () {
                        $('.' + productsClass).html(res).fadeIn()

                        // Работаем с адресной строкой объектом history
                        let url = location.search.replace(/filter(.+?)(&|$)/g, ''),
                            newUrl = location.pathname + url + (location.search ? '&' : '?') + 'filter=' + data

                        // Если будет дубль, то заменяем его
                        newUrl = newUrl.replace('&&', '&')
                        newUrl = newUrl.replace('?&', '?')

                        // Отправляем в url
                        history.pushState({}, '', newUrl)
                    })
                },
                error: function () {
                    alert.get(translations['something_went_wrong'])
                }
            })

        } else {

            // Перезагрузим страницу
            window.location = location.pathname
        }
    })


    body.on('click', '.' + filterClass + ' .reset', function () {
        window.location = location.pathname
    })

}, false)

