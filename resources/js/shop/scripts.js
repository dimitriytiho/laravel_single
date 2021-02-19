
document.addEventListener('DOMContentLoaded', function() {

    /*
     * При клике на Plus или Minus меняем кол-во в Input.
     * Необходимо по центру сделать input type="number" step="1" min="1" value="1", слева - span.minus, справа - span.plus.
     */
    $(document).on('click', '.input_qty .minus', function() {
        var input = $(this).next('input'),
            val = input.val()

        val = parseInt(val) >= 2 ? parseInt(val) - 1 : 1
        input.val(val)
    })
    $(document).on('click', '.input_qty .plus', function() {
        var input = $(this).prev('input'),
            val = input.val()

        val = parseInt(val) >= 1 ? parseInt(val) + 1 : 2
        input.val(val)
    })


    // При наведении на класс .fancybox_hover показываем соответствующую блок с классом .fancybox_thumbs
    $('.fancybox_hover').hover(function () {
        $('.fancybox_thumbs').hide()
        $('.fancybox_thumbs[rel=' + $(this).data('gallery') + ']').show()
    })

}, false)
