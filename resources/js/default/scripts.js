
document.addEventListener('DOMContentLoaded', function() {

    // При клике на кнопку Вверх, движение вверх
    $('#btn_up').click(function() {
        $('html, body').animate({scrollTop: 0}, '400')
    })


    /*
     * Плавная прокрутка страницы до якоря.
     * Добавить класс anchor и в href="#name_anchor" написать название якоря.
     */
    $(document).on('click', '.anchor', function(e) {
        e.preventDefault()
        var anchor = $(this).attr('href'),
            margin = 70,
            offset = $(anchor).offset()

        if (offset) {
            $('html, body').stop().animate({
                scrollTop: offset.top - margin
            }, 400)
        }
    })

}, false)
