/*
* При кликена на .btn-pulse эффект пульса.
* Если есть вложенный тег, то установить .btn-pulse-child.
*/
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-pulse')) {
        const div = document.createElement('div'),
            style = div.style,
            max = Math.max(e.target.offsetWidth, e.target.offsetHeight),
            rect = e.target.getBoundingClientRect(),
            px = 'px',
            color = window.getComputedStyle(e.target).backgroundColor,
            textBtn = e.target.textContent,
            spanBtn = e.target.querySelector('span'),
            timeDeleteDiv = 300

        div.classList.add('pulse_js')
        style.width = style.height = max + px
        style.left = e.clientX - rect.left - (max / 2) + px
        style.top = e.clientY - rect.top - (max / 2) + px
        style.backgroundColor = color
        style.opacity = .4
        e.target.appendChild(div)

        if (!spanBtn) {
            setTimeout(function () {
                e.target.textContent = textBtn
            }, timeDeleteDiv)
        }

    } else if (e.target.classList.contains('btn-pulse-child')) {
        const parent = e.target.closest('.btn-pulse'),
            div = document.createElement('div'),
            style = div.style,
            max = Math.max(parent.offsetWidth, parent.offsetHeight),
            rect = parent.getBoundingClientRect(),
            px = 'px',
            color = window.getComputedStyle(e.target).backgroundColor,
            textBtn = e.target.textContent,
            timeDeleteDiv = 300


        div.classList.add('pulse_js')
        style.width = style.height = max + px
        style.left = e.clientX - rect.left - (max / 2) + px
        style.top = e.clientY - rect.top - (max / 2) + px
        style.backgroundColor = color
        style.opacity = .4
        parent.appendChild(div)

        setTimeout(function () {
            parent.textContent = textBtn
        }, timeDeleteDiv)
    }
})
