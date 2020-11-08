import func from './functions'

// При клике на кнопку Вверх, движение вверх
$('#btn-up').click(function() {
    func.scrollUp()
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

document.addEventListener('DOMContentLoaded', function() {



}, false)
