Kanboard.BoardColumnScrolling = function(app) {
    this.app = app;
};

Kanboard.BoardColumnScrolling.prototype.execute = function() {
    if (this.app.hasId("board")) {
        this.render();

        $(window).on("load", this.render);
        $(window).resize(this.render);
    }
};

Kanboard.BoardColumnScrolling.prototype.listen = function() {
    var self = this;

    $(document).on('click', ".filter-toggle-height", function(e) {
        e.preventDefault();
        self.toggle();
    });
};

Kanboard.BoardColumnScrolling.prototype.onBoardRendered = function() {
    this.render();
};

Kanboard.BoardColumnScrolling.prototype.toggle = function() {
    var scrolling = localStorage.getItem("column_scroll");

    if (scrolling == undefined) {
        scrolling = 1;
    }

    localStorage.setItem("column_scroll", scrolling == 0 ? 1 : 0);
    this.render();
};

Kanboard.BoardColumnScrolling.prototype.render = function() {
    var taskList = $(".board-task-list");
    var rotationWrapper = $(".board-rotation-wrapper");
    var filterMax = $(".filter-max-height");
    var filterMin = $(".filter-min-height");

    if (localStorage.getItem("column_scroll") == 0) {
        var height = 80;

        filterMax.show();
        filterMin.hide();
        rotationWrapper.css("min-height", '');

        taskList.each(function() {
            var columnHeight = $(this).height();

            if (columnHeight > height) {
                height = columnHeight;
            }
        });

        taskList.css("min-height", height);
        taskList.css("height", '');
    }
    else {

        filterMax.hide();
        filterMin.show();

        if ($(".board-swimlane").length > 1) {
            taskList.each(function() {
                if ($(this).height() > 500) {
                    $(this).css("height", 500);
                }
                else {
                    $(this).css("min-height", 320); // Height of the dropdown menu
                    rotationWrapper.css("min-height", 320);
                }
            });
        }
        else {
            var height = $(window).height() - 170;

            taskList.css("height", height);
            rotationWrapper.css("min-height", height);
        }
    }
};
