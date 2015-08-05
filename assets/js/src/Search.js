function Search() {
    this.keyboardShortcuts();
}

Search.prototype.focus = function() {
    // Place cursor at the end when focusing on the search box
    $(document).on("focus", "#form-search", function() {
        if ($("#form-search")[0].setSelectionRange) {
           $('#form-search')[0].setSelectionRange($('#form-search').val().length, $('#form-search').val().length);
        }
    });
};

Search.prototype.listen = function() {
    // Filter helper for search
    $(document).on("click", ".filter-helper", function (e) {
        e.preventDefault();
        $("#form-search").val($(this).data("filter"));
        $("form.search").submit();
    });
};

Search.prototype.keyboardShortcuts = function() {
    // Switch view mode for projects: go to the board
    Mousetrap.bind("v b", function(e) {
        var link = $(".view-board");

        if (link.length) {
            window.location = link.attr('href');
        }
    });

    // Switch view mode for projects: go to the calendar
    Mousetrap.bind("v c", function(e) {
        var link = $(".view-calendar");

        if (link.length) {
            window.location = link.attr('href');
        }
    });

    // Switch view mode for projects: go to the listing
    Mousetrap.bind("v l", function(e) {
        var link = $(".view-listing");

        if (link.length) {
            window.location = link.attr('href');
        }
    });

    // Focus to the search field
    Mousetrap.bind("f", function(e) {
        e.preventDefault();
        var input = document.getElementById("form-search");

        if (input) {
            input.focus();
        }
    });
};

