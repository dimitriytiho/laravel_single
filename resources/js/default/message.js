const timeout = 4000

function alert(text, ms = timeout, alertClass = 'danger') {
    const getAlert = document.querySelector('#get-alert'),
        html = `
        <div class="row alert-js">
            <div class="col-md-10 offset-md-1 anime-from-center">
                <div class="alert alert-${alertClass} py-4 px-5" role="alert">
                    <span>${text}</span>
                </div>
            </div>
        </div>`

    getAlert.innerHTML = html
    setTimeout(function () {
        getAlert.innerHTML = ''
    }, ms)
}

function hideAlert(ms = timeout - 500) {
    const alert = document.querySelector('.alert-js > div')
    if (alert) {
        setTimeout(function () {
            alert.classList.remove('anime-from-center')
            alert.classList.add('anime-to-center')
        }, ms)
    }
}

export default {
    error: function (text, ms = null, reload = false) {
        ms = !ms ? timeout : ms

        alert(text, ms)
        hideAlert()

        if (reload) {
            setTimeout(function () {
                location.reload()
            }, ms)
        }
    },
    success: function (text, ms = null, reload = false) {
        ms = !ms ? timeout : ms

        alert(text, ms, 'success')
        hideAlert()

        if (reload) {
            setTimeout(function () {
                location.reload()
            }, ms)
        }
    },
    warning: function (text, ms = null, reload = false) {
        ms = !ms ? timeout : ms

        alert(text, ms, 'warning')
        hideAlert()

        if (reload) {
            setTimeout(function () {
                location.reload()
            }, ms)
        }
    },
    info: function (text, ms = null, reload = false) {
        ms = !ms ? timeout : ms

        alert(text, ms, 'info')
        hideAlert()

        if (reload) {
            setTimeout(function () {
                location.reload()
            }, ms)
        }
    }
}
