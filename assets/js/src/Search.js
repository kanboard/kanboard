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

Kanboard.Search.prototype.listen = function() {
    // Filter helper for search
    $(document).on("click", ".filter-helper", function (e) {
        e.preventDefault();

        var filter = $(this).data("filter");
        var appendFilter = $(this).data("append-filter");
        var input = $("#form-search");

        if (appendFilter) {
            filter = input.val() + " " + appendFilter;
        }

        input.val(filter);
        $("form.search").submit();
    });
};

Kanboard.Search.prototype.goToView = function(label) {
    var link = $(label);

    if (link.length) {
        window.location = link.attr('href');
    }
};

Kanboard.Search.prototype.keyboardShortcuts = function() {
    var self = this;

    // Switch view mode for projects: go to the overview page
    Mousetrap.bind("v o", function() {
        self.goToView(".view-overview");
    });

    // Switch view mode for projects: go to the board
    Mousetrap.bind("v b", function() {
        self.goToView(".view-board");
    });

    // Switch view mode for projects: go to the calendar
    Mousetrap.bind("v c", function() {
        self.goToView(".view-calendar");
    });

    // Switch view mode for projects: go to the listing
    Mousetrap.bind("v l", function() {
        self.goToView(".view-listing");
    });

    // Switch view mode for projects: go to the gantt chart
    Mousetrap.bind("v g", function() {
        self.goToView(".view-gantt");
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
        var input = $("#form-search");

        input.val(reset);
        $("form.search").submit();
    });
};
