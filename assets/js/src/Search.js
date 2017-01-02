Kanboard.Search = function(app) {
    this.app = app;
};

Kanboard.Search.prototype.focus = function() {

    // Place cursor at the end when focusing on the search box
    $(document).on("focus", "#form-search", function() {
        var input = $("#form-search");

        if (input[0].setSelectionRange) {
            var len = input.val().length * 2;
            input[0].setSelectionRange(len, len);
        }
    });
};

// TODO: rewrite this code
Kanboard.Search.prototype.listen = function() {
    $(document).on("click", ".filter-helper", function (e) {
        e.preventDefault();

        var filter = $(this).data("filter");
        var appendFilter = $(this).data("append-filter");
        var uniqueFilter = $(this).data("unique-filter");
        var input = $("#form-search");

        if (uniqueFilter) {
            var attribute = uniqueFilter.substr(0, uniqueFilter.indexOf(':'));
            filter = input.val().replace(new RegExp('(' + attribute + ':[#a-z0-9]+)', 'g'), '');
            filter = filter.replace(new RegExp('(' + attribute + ':"(.+)")', 'g'), '');
            filter = filter.trim();
            filter += ' ' + uniqueFilter;
        } else if (appendFilter) {
            filter = input.val() + " " + appendFilter;
        }

        input.val(filter);
        $("form.search").submit();
    });
};
