import func from './functions'


// Скрываем элементы с классами .js-hide
$('.js-hide').hide()

// Делаем видимыми элементы с классами .js-none-visible
$('.js-none-visible').show()

// Делаем видимыми элементы с классами .js-none-visible-flex
$('.js-none-visible-flex').css('display', 'flex')


// Отменяем обычное поведение ссылки при клике по класс .prevent-default
$('.prevent-default').click(function() {
    return false
})


/*
 * Открыть модальное окно по клику на класс .modal_show, при этом нужно указать здесь же атрибут data-modal-id="" и в него вписать id модального окна.
 * Можно задать data-modal-title="" и в него вписать заголовок модального окна.
 */
document.addEventListener('click', function(e) {

    var modalShowClass = 'modal_show',
        block = e.target.classList.contains(modalShowClass) || e.target.closest('.' + modalShowClass) && e.target.closest('.' + modalShowClass).classList.contains(modalShowClass)

    if (block) {
        var modalId = e.target.dataset.modalId || e.target.closest('.' + modalShowClass).dataset.modalId,
            modalTitle = e.target.dataset.modalTitle || e.target.closest('.' + modalShowClass).dataset.modalTitle

        if (modalId) {
            e.preventDefault()
            if (modalTitle) {
                $('#' + modalId + ' .modal-title').text(modalTitle)
            }
            $('#' + modalId).modal('show')
        }
    }
})



// При клике добавляем класс .active к родителю
$('.click_add_active').click(function() {
    $(this).parent().addClass('active')
})

// При клике в любом месте убираем класс .active у блока с классом .click_remove_active
document.body.onclick = function(e) {
    const blockClass = 'remove_active',
        blocks = document.querySelectorAll('.' + blockClass),
        blockClick = e.target.classList.contains(blockClass) || e.target.closest('.' + blockClass)

    if (!blockClick) {
        blocks.forEach(function (el) {
            if (el.classList.contains('active')) {
                el.classList.remove('active')
            }
        })
    }
}


document.addEventListener('DOMContentLoaded', function() {

    // Одинаковая высота блоков, задать класс у элементов .height-math
    func.getHeight('height-math')

}, false)

