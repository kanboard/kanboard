function Search(app) {
    this.app = app;
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
    var self = this;

    // Filter helper for search
    $(document).on("click", ".filter-helper", function (e) {
        e.preventDefault();

        var filter = $(this).data("filter");
        var appendFilter = $(this).data("append-filter");

        if (appendFilter) {
            filter = $("#form-search").val() + " " + appendFilter;
        }

        $("#form-search").val(filter);

        if ($('#board').length) {
            self.app.board.reloadFilters(filter);
        }
        else {
            $("form.search").submit();
        }
    });
};

Search.prototype.keyboardShortcuts = function() {
    var self = this;

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

    // Switch view mode for projects: go to the gantt chart
    Mousetrap.bind("v g", function(e) {
        var link = $(".view-gantt");

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

    // Reset to the search field
    Mousetrap.bind("r", function(e) {
        e.preventDefault();
        var reset = $(".filter-reset").data("filter");

        $("#form-search").val(reset);

        if ($('#board').length) {
            self.app.board.reloadFilters(reset);
        }
        else {
            $("form.search").submit();
        }
    });
};
