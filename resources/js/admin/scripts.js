
document.addEventListener('DOMContentLoaded', function() {

    /*
     * При выборе файла, подставим его имя в input file.
     * В класс .img_replace заменить картинку на загруженную.
     */
    $('input[type=file]').change(function (e) {
        var name = e.target.files[0] ? e.target.files[0].name : null
        if (name) {
            $(this).siblings('label').text(name)

            var path = URL.createObjectURL(e.target.files[0])
            if (path) {
                $('.img_replace').attr('src', path)
                //$(this).closest('.row').find('.this_img_replace').attr('src', path)
            }
        }
    })

}, false)
