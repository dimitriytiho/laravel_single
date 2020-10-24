import func from './functions'
import message from './message';


// Необходимые данные
const offset = 20,
    heightScreen = window.innerHeight,
    widthScreen = window.innerWidth || document.body.clientWidth


// Селекторы в коде
const btnUp = document.getElementById('btn-up')



// Код со скролом
$(window).on('scroll', function () {
    const scrollTop = $(window).scrollTop() || document.documentElement.scrollTop || window.pageYOffset, // Верхняя позиция скрола
        heightWindow = window.innerHeight // Высота окна браузера

    // pageYOffset Динамическая позиция скрола у верхней кромки


    // Кнопка вверх
    if (btnUp) {
        if (scrollTop > 300 && !btnUp.classList.contains('scale-in')) {
            btnUp.classList.remove('scale-out')
            btnUp.classList.add('scale-in')

        } else if (scrollTop < 300 && !btnUp.classList.contains('scale-out')) {
            btnUp.classList.remove('scale-in')
            btnUp.classList.add('scale-out')
        }
    }


    // Прилипающее меню
    /*const stickyMenu = document.getElementById('sticky_menu')
    if (stickyMenu) {

        const stickyMenuHeight = stickyMenu.offsetHeight
        if (stickyMenuHeight && document.body.clientWidth > 992 && pageYOffset > stickyMenuHeight) {
            stickyMenu.classList.add('sticky_menu')
        } else {
            stickyMenu.classList.remove('sticky_menu')
        }
    }*/


    // Добавление класса анимации для lg дисплеев (или других, можно выбрать)
    /*const animateBottom = document.querySelectorAll('.animate-bottom-js'),
        animateRight = document.querySelectorAll('.animate-right-js'),
        animateLeft = document.querySelectorAll('.animate-left-js')

    addAnimate(animateBottom)
    addAnimate(animateRight, 'animate-right')
    addAnimate(animateLeft, 'animate-left')*/


    // Вызовите функцию и передайте нужный селектор, который получите выше чем window.onscroll
    function addAnimate(selectorAll, addClassName = 'animate-bottom', widthScreenAfter = 992) {

        if (selectorAll[0] && widthScreen > widthScreenAfter) {

            selectorAll.forEach(function (el) {
                let elTop = el.offsetTop
                const elHeight = el.offsetHeight,
                    container = el.closest('.container.animate-add-parent') && el.closest('.container.animate-add-parent').closest('.container')


                // Если есть вложенность, то надо прибавить расстояние от родителя до верха экрана браузера
                if (container) {
                    //elTop = elTop + container.offsetTop
                }

                if (heightScreen + scrollTop - offset > elTop && scrollTop + offset < elTop + elHeight) {

                    el.classList.add(addClassName)
                }
            })
        }
    }
})