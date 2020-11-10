
document.addEventListener('DOMContentLoaded', function() {

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
