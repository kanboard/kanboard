KB.on('dom.ready', function () {
    function highlightComment() {
        if (window.location.hash.indexOf('#comment-') === 0) {
            var commentElement = KB.find(window.location.hash);

            if (commentElement) {
                var commentsElement = document.querySelectorAll('.comment');

                commentsElement.forEach(function (element) {
                    KB.dom(element).removeClass('comment-highlighted');
                });

                commentElement.addClass('comment-highlighted');
            }
        }
    }

    window.addEventListener('hashchange', highlightComment);

    highlightComment();
});
