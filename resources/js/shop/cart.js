import alert from "../default/alert";

document.addEventListener('DOMContentLoaded', function() {

    // id модального окна
    var modalId = 'cart_modal'

    // Проверяем подключен ли jQuery
    if (window.jQuery) {


        // Если есть класс .no_js, то отключаем JS
        if (!$('div').hasClass('no_js')) {

            // Показать корзину по клику на .cart_show
            $('.cart_show').on('click', function (e) {
                e.preventDefault()

                $.ajax({
                    type: 'GET',
                    url: '/cart/show',
                    success: function (res) {
                        showCart(res, modalId)
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            })



            // Добавить товар в корзину по клику на .cart_plus
            $(document).on('click', '.cart_plus', function (e) {
                e.preventDefault()

                var $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/plus',
                        //data: {id: id},
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert.get(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert.get(translations['something_went_wrong'])
                        }
                    })
                }
            })



            // Отминусовать товар из корзины по клику на .cart_minus
            $(document).on('click', '.cart_minus', function (e) {
                e.preventDefault()

                var $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/minus',
                        //data: {id: id},
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert.get(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert.get(translations['something_went_wrong'])
                        }
                    })
                }
            })



            // Удалить товар из корзину по клику на .cart_destroy
            $(document).on('click', '.cart_destroy', function (e) {
                e.preventDefault()

                var $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/destroy',
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert.get(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert.get(translations['something_went_wrong'])
                        }
                    })
                }
            })

        }



        // Функция показа корзины, принимает содержимое корзины, в ответе на ajax
        function showCart(cart, modalId) {
            //var modalInstance = new BSN.Modal('#' + modalId)

            // Вставим в модальное окно содержимое корзины
            $('#' + modalId + ' .modal-body').html(cart)

            // Открыть модальное окно
            $('#' + modalId).modal()
            //modalInstance.show()

            var cartQty = $('#cart_modal_qty').text(),
                cartQtyClass = $('.cart_count_qty'),
                cartSum = $('#cart_modal_sum').text()

            // Вставляем кол-во из корзины в кнопку вызова
            cartQtyClass.text(cartQty)

            // Если нет кол-ва, то скрываем круг для кол-ва
            if (!cartQty) {
                cartQtyClass.css('opacity', '0')
            }

            // Вставляем сумму из корзины в кнопку вызова
            //$('.cart_count_sum').text(cartSum ? cartSum + ' ₽' : '')
        }



        var addToCart = 'add_to_cart'

        // Добавить в корзину товар
        $(document).on('click', '.' + addToCart, function (e) {
            e.preventDefault()

            var modal = $('#' + addToCart),
                self = $(this),
                url = self.data('url'),
                id = self.data('id'),
                title = self.data('title')

            // Проверить есть ли в корзине товар
            if (url && id) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {_token: _token},
                    success: function (res) {

                        // Товар не найден
                        if (!res) {
                            alert.get(translations['something_went_wrong'])
                        }

                        // Открыть модальное окно
                        //modal.find('.modal-title').text(title)
                        modal.find('.modal-body').html(res)
                        modal.modal()
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            }
        })
    }


}, false)
