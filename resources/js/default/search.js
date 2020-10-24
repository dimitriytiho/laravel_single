document.addEventListener('DOMContentLoaded', function() {

    var url = '/search-js',
        searchClass = '.search_js',
        searchInput = searchClass + '__input',
        searchChild = searchClass + '__child'

    $(document).on('keyup', searchInput, function () {
        var self = $(this),
            query = self.val(),
            length = query.length,
            child = self.closest(searchClass).find(searchChild)

        if (length > 0) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token,
                    query
                },
                success: function(res) {
                    if (res) {
                        res = JSON.parse(res)
                        let html = ''

                        res.forEach(function (el) {
                            html += `<a href="${el.slug}" class="search_js__child--link">${el.title}</a>`
                        })

                        child.html(html)
                    }
                }/*,
                error: function () {
                    console.log('error')
                }*/
            })
        }
    })

}, false)
