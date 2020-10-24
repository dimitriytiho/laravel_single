document.addEventListener('DOMContentLoaded', function() {

    $('.lazy').lazy()

    // Появление картинки с задержкой
    $('.lazy_delay').lazy({
        delay: 100
    })

}, false)
