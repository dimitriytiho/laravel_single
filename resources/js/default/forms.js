
// Скрипты для Форм

/*
 * При клике на ссылку или кнопку с классом .one_click
 * Отключается кнопка (добавляется класс disabled).
 */
$(document).on('click', '.one_click', function(e) {
    $(this)
        .attr('disabled', true)
        .addClass('disabled')
})


/*
 * При клике на ссылку или кнопку с классом .spinner_click
 * Отключается кнопка (добавляется класс disabled) и включается спинер.
 */
$(document).on('click', '.spinner_click', function(e) {
    $(this)
        .attr('disabled', true)
        .addClass('disabled')
        .prepend(spinnerBtn)
})


/*
 * При отправке формы с классом .spinner_submit
 * Отключается кнопка и включается спинер в кнопке отправки.
 * Внимание, спинер будет крутиться до перезагрузки страницы.
 */
$('.spinner_submit').click(function () {
    $(this)
        .find('[type=submit]')
        .attr('disabled', true)
        .addClass('disabled')
        .prepend(spinnerBtn)
})
