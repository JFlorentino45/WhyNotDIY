$(document).ready(function () {
    var offset = 7;
    var loading = false;

    $(window).scroll(function () {
        var scrollPercentage = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;

        if (scrollPercentage > 80 && !loading) {
            loading = true;
            loadMoreBlogs();
        }
    });

    function loadMoreBlogs() {
        $('#pagination-loader').html('Loading...');

        var url = userRole === 'admin' ? '/admin/load-more-blogs' : '/load-more-blogs';

        $.get(url, { offset: offset }, function (response) {
            if (response.html.trim() != '') {
                var sanitizedHtml = DOMPurify.sanitize(response.html, {
                    ALLOWED_TAGS: ['iframe', 'p', 'a'],
                });
                $('#blog-container').append(sanitizedHtml);
                offset += 5;
                loading = false;
            } else {
                $('#pagination-loader').html('No more blogs to load.');
            }
        });
    }
});