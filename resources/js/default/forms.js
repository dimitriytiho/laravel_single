
// Скрипты для Форм

/*
 * При клике на ссылку или кнопку с классом .one_click
 * Отключается кнопка (добавляется класс disabled).
 */
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('one_click')) {
        e.target.classList.add('disabled')
        //e.target.setAttribute('disabled', 'true')
    }
})


/*
 * При клике на ссылку или кнопку с классом .spinner_click
 * Отключается кнопка (добавляется класс disabled) и включается спинер.
 */
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('spinner_click')) {
        e.preventDefault()
        e.target.classList.add('disabled')
        e.target.innerHTML = spinnerBtn + e.target.innerText
    }
})


/*
 * При отправке формы с классом .spinner_submit
 * Отключается кнопка и включается спинер в кнопке отправки.
 * Внимание, спинер будет крутиться до перезагрузки страницы.
 */
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('spinner_submit')) {
        var btn = e.target.querySelector('[type=submit]')
        if (btn) {
            btn.classList.add('disabled')
            btn.innerHTML = spinnerBtn + btn.innerText
        }
    }
})
