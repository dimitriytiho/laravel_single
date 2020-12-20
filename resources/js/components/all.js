document.addEventListener('DOMContentLoaded', function() {

    // Lazy
    $('.lazy').lazy()
    $('.lazy_delay').lazy({ // Появление картинки с задержкой
        delay: 100
    })


    // fancybox
    /*var fancybox = $('.fancybox')
    //var fancybox = $('[data-fancybox]')
    if (fancybox.length) {

        fancybox.fancybox()
    }*/

}, false)
