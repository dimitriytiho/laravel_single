import alert from '../default/alert'

document.addEventListener('DOMContentLoaded', function() {

    var filterClass = 'filter',
        productsClass = 'products_js',
        documentLocation = document.location,
        url = documentLocation.pathname,
        getParamsStr = documentLocation.search.replace('?', ''),
        filterParams = []


    // Ползунок цены ionRangeSlider отлеживаем изменение
    $('.js_range_slider').on('change', function () {
        var self = $(this),
            priceFrom = self.data('from'),
            priceTo = self.data('to')


        if (priceFrom && priceTo) {

            // Добавляем цены от и до в Url
            pushNewUrl('from', priceFrom)
            pushNewUrl('to', priceTo)


            // Ajax запрос
            if (url) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {_token: _token, priceFrom: priceFrom, priceTo: priceTo},

                    // Перед запросом
                    beforeSend: function () {
                        beforeSendProduct()
                    },

                    // При успешном ответе
                    success: function(res) {
                        successProduct(res)
                    },

                    // При ошибке
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            }
        }
    })


    // Отслеживаем изменение фильтров
    $(document).on('change', '.' + filterClass + ' .filter_form input, .' + filterClass + ' .filter_change_js', function () {
        var checked = $('.' + filterClass + ' .filter_form input:checked, .' + filterClass + ' .filter_change_js option:selected'),
            data = ''

        // Получаем отмеченные input
        checked.each(function () {
            if (this.value) {
                data += this.value + '%2C'
            }
        })

        // Добавляем фильтры в Url
        pushNewUrl('filter', data)

        $.ajax({
            type: 'POST',
            url: url,
            data: {_token: _token, filters: data},

            // Перед запросом
            beforeSend: function () {
                beforeSendProduct()
            },

            // При успешном ответе
            success: function(res) {
                successProduct(res)
            },

            // При ошибке
            error: function () {
                alert.get(translations['something_went_wrong'])
            }
        })
    })


    // Кнопка сброса Get параметров
    $(document).on('click', '.' + filterClass + ' .reset', function () {
        if (url) {
            url = urlEndSlash(url)
            window.location = url
        }
    })



    // ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ

    // Перед запросом
    function beforeSendProduct() {

        // Скрываем все товары
        spinner.fadeIn(300, function () {
            $('.' + productsClass).hide()
        })
    }


    // При успешном ответе
    function successProduct(res) {
        if (res) {

            // Показываем полученные товары
            spinner.delay(300).fadeOut(300, function () {
                $('.' + productsClass).html(res).fadeIn()
            })
        }
    }


    // Возвращает Get параметры в массиве
    function getFilterParams() {
        var params = document.location.search.replace('?', '').split('&')
        for (var key in params) {
            filterParams[params[key].split('=')[0]] = params[key].split('=')[1]
        }
        return filterParams
    }


    /*
     * Отправляем параметры в Url
     * addUrlKey - к примеру 'from'
     * addUrlValue - к примеру 100
     */
    function pushNewUrl(addUrlKey, addUrlValue) {
        var location = document.location,
            urlFull = location.href
        if (urlFull && addUrlKey) {
            getParamsStr = location.search.replace('?', '')

            // Если есть get параметры
            if (getParamsStr) {

                // Если передаваемый параметр есть в Url
                if (getParamsStr.includes(addUrlKey)) {

                    // Пересобрать Get параметры
                    var getFilters = getFilterParams(),
                        i = 0,
                        newGetParamsStr = ''
                    for (var key in getFilters) {

                        if (key === addUrlKey) {
                            if (addUrlValue && addUrlValue !== '%2C') {

                                // В передаваемый ключ записываем новое передаваемое значение
                                getFilters[key] = addUrlValue

                            } else {

                                // Если пусто передаваемое значение, удалим из Get
                                continue
                            }
                        }
                        newGetParamsStr += (i ? '&' : '') + key + '=' + getFilters[key]
                        i++
                    }

                    // Старое значение передаваемого ключа
                    //var urlParamValOld = getFilterParams()[addUrlKey] || ''
                    // Заменить старое значение
                    //var newGetParamsStr = getParamsStr.replace(urlParamValOld, addUrlValue)

                    // Оправить в Url
                    history.pushState({}, '', url + (newGetParamsStr ? '?' : '') + newGetParamsStr)

                    // Если пусты параметры фильтра, то перезагрузим страницу
                    if (!newGetParamsStr) {
                        document.location.href = url
                    }


                // Если передаваемого параметра нет в Url
                } else {

                    // Оправить в Url
                    history.pushState({}, '', urlFull + '&' + addUrlKey + '=' + addUrlValue)
                }


            // Если get параметров нет
            } else {
                urlFull = urlEndSlash(urlFull)

                // Оправить в Url
                history.pushState({}, '', urlFull + '?' + addUrlKey + '=' + addUrlValue)
            }

            // Показываем кнопку сброса
            $('.' + filterClass + ' .reset').show()
        }
    }


    // Проверяем url, если на конце нет слэша, то добавим его
    function urlEndSlash(url) {
        if (url.charAt(url.length - 1) !== '/') {
            return url + '/'
        }
        return url
    }

}, false)

