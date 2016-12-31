Kanboard.BoardHorizontalScrolling = function(app) {
    this.app = app;
};

Kanboard.BoardHorizontalScrolling.prototype.execute = function() {
    if (this.app.hasId("board")) {
        this.render();
    }
};

Kanboard.BoardHorizontalScrolling.prototype.listen = function() {
    var self = this;

    $(document).on('click', ".filter-toggle-scrolling", function(e) {
        e.preventDefault();
        self.toggle();
    });
};

Kanboard.BoardHorizontalScrolling.prototype.onBoardRendered = function() {
    this.render();
};

Kanboard.BoardHorizontalScrolling.prototype.toggle = function() {
    var scrolling = localStorage.getItem("horizontal_scroll") || 1;
    localStorage.setItem("horizontal_scroll", scrolling == 0 ? 1 : 0);
    this.render();
};

Kanboard.BoardHorizontalScrolling.prototype.render = function() {
    if (localStorage.getItem("horizontal_scroll") == 0) {
        $(".filter-wide").show();
        $(".filter-compact").hide();

        $("#board-container").addClass("board-container-compact");
        $("#board th:not(.board-column-header-collapsed)").addClass("board-column-compact");
    }
    else {
        $(".filter-wide").hide();
        $(".filter-compact").show();

        $("#board-container").removeClass("board-container-compact");
        $("#board th").removeClass("board-column-compact");
    }
};
