Kanboard.Column = function(app) {
    this.app = app;
};

Kanboard.Column.prototype.listen = function() {
    this.dragAndDrop();
};

Kanboard.Column.prototype.dragAndDrop = function() {
    var self = this;

    $(".draggable-row-handle").mouseenter(function() {
        $(this).parent().parent().addClass("draggable-item-hover");
    }).mouseleave(function() {
        $(this).parent().parent().removeClass("draggable-item-hover");
    });

    $(".columns-table tbody").sortable({
        forcePlaceholderSize: true,
        handle: "td:first i",
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });

            return ui;
        },
        stop: function(event, ui) {
            var column = ui.item;
            column.removeClass("draggable-item-selected");
            self.savePosition(column.data("column-id"), column.index() + 1);
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
        }
    }).disableSelection();
};

Kanboard.Column.prototype.savePosition = function(columnId, position) {
    var url = $(".columns-table").data("save-position-url");
    var self = this;

    this.app.showLoadingIcon();

    $.ajax({
        cache: false,
        url: url,
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "column_id": columnId,
            "position": position
        }),
        complete: function() {
            self.app.hideLoadingIcon();
        }
    });
};
