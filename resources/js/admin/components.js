
document.addEventListener('DOMContentLoaded', function() {

    // Select2
    $('.select2').select2({
        language: 'ru'
        //theme: 'bootstrap4'
    })
    $('.select2_one').select2({
        language: 'ru',
        maximumSelectionLength: 1
    })
    

    // Bootstrap Switch
    if (document.querySelector('[data-toggle=switch]')) {
        $('[data-toggle=switch]').bootstrapSwitch()
    }
    

    // Codemirror
    var codemirror = document.querySelector('.codemirror')
    if (codemirror) {
        editor = CodeMirror.fromTextArea(codemirror, {
            tabMode: 'indent',
            lineNumbers: true,
            lineWrapping: true,
            matchBrackets: true,
            indentUnit: 4
        })
        editor.setSize('auto', 'auto')
    }


    // Ckeditor
    var ckeditor = document.querySelector('.ckeditor')
    if (ckeditor) {
        CKEDITOR.config.height = '600px'
        //CKEDITOR.config.filebrowserImageBrowseUrl = '/file-manager/ckeditor'
    }

}, false)
