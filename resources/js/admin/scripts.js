
document.addEventListener('DOMContentLoaded', function() {

    // При выборе файла, подставим его имя в input file
    $('input[type=file]').change(function (e) {
        var name = e.target.files[0] ? e.target.files[0].name : null
        if (name) {
            $(this).siblings('label').text(name)
        }
    })

}, false)
