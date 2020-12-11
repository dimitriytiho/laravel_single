import alert from "../default/alert";

document.addEventListener('DOMContentLoaded', function() {

    // Проверяем подключен ли jQuery
    if (window.jQuery) {


        // Если есть класс .no_js, то отключаем JS
        if (!$('div').hasClass('no_js')) {

            var urlCart = '/cart/'


            // Показать корзину по клику на .cart_show
            $('.cart_show').on('click', function (e) {
                e.preventDefault()

                $.ajax({
                    type: 'GET',
                    url: urlCart + 'show',
                    success: function (res) {
                        showCart(res)
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            })



            // Добавить в корзину товар по клику на .add_to_cart
            $(document).on('click', '.add_to_cart', function (e) {
                e.preventDefault()

                var self = $(this),
                    productId = self.data('product-id'),
                    qty = self.closest('.product').find('input[name=qty]').val() || self.closest('.product_show').find('input[name=qty]').val() || 1

                // Проверить есть ли в корзине товар
                if (productId) {
                    $.ajax({
                        type: 'GET',
                        url: urlCart + productId + '/add',
                        data: {qty},
                        success: function (res) {

                            if (res) {

                                // Показать модальное окно
                                showCart(res)

                            } else {

                                // Товар не найден
                                alert.get(translations['something_went_wrong'])
                            }
                        },
                        error: function () {
                            alert.get(translations['something_went_wrong'])
                        }
                    })
                }
            })



            // Плюсовать товар в корзине по клику на .cart_plus
            $(document).on('click', '.cart_plus', function (e) {
                e.preventDefault()

                var self = $(this),
                    cartKey = self.data('cart-key')

                $.ajax({
                    type: 'GET',
                    url: urlCart + cartKey + '/plus',
                    success: function (res) {

                        if (res) {

                            // Показать модальное окно
                            showCart(res)

                        } else {

                            // Товар не найден
                            alert.get(translations['something_went_wrong'])
                        }
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            })



            // Минусовать товар из корзины по клику на .cart_minus
            $(document).on('click', '.cart_minus', function (e) {
                e.preventDefault()

                var self = $(this),
                    cartKey = self.data('cart-key')

                $.ajax({
                    type: 'GET',
                    url: urlCart + cartKey + '/minus',
                    //data: {cartKey: cartKey},
                    success: function (res) {

                        if (res) {

                            // Показать модальное окно
                            showCart(res)

                        } else {

                            // Товар не найден
                            alert.get(translations['something_went_wrong'])
                        }
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            })



            // Удалить товар из корзину по клику на .cart_remove
            $(document).on('click', '.cart_remove', function (e) {
                e.preventDefault()

                var self = $(this),
                    cartKey = self.data('cart-key')

                $.ajax({
                    type: 'GET',
                    url: urlCart + cartKey + '/remove',
                    success: function (res) {

                        if (res) {

                            // Показать модальное окно
                            showCart(res)

                        } else {

                            // Товар не найден
                            alert.get(translations['something_went_wrong'])
                        }
                    },
                    error: function () {
                        alert.get(translations['something_went_wrong'])
                    }
                })
            })

        }



        // Функция показа корзины, принимает содержимое корзины, в ответе на ajax
        function showCart(cart, modalId = 'cart_modal') {

            // Вставим в модальное окно содержимое корзины
            $('#' + modalId + ' .modal-body').html(cart)

            // Открыть модальное окно
            $('#' + modalId).modal()

            var cartQty = $('#cart_modal_qty').text(),
                cartQtyClass = $('.cart_count_qty'),
                cartSum = $('#cart_modal_sum').html(),
                cartSumClass = $('.cart_count_sum')

            /*if (cartQty) {

                // Вставляем кол-во из корзины в кнопку вызова
                cartQtyClass.text(cartQty)
            } else {

                // Если нет кол-ва, то скрываем круг для кол-ва
                cartQtyClass.css('opacity', '0')
            }*/


            if (cartSum) {

                // Вставляем сумму из корзины в кнопку вызова
                cartSumClass.html(cartSum)

            } else {

                // Если нет сумму
                cartSumClass.text(translations['cart'])
            }
        }

    }


}, false)
