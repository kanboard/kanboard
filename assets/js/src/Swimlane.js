function Swimlane(app) {
    this.app = app;
}

Swimlane.prototype.getStorageKey = function() {
    return "hidden_swimlanes_" + $("#board").data("project-id");
};

Swimlane.prototype.expand = function(swimlaneId) {
    var swimlaneIds = this.getAllCollapsed();
    var index = swimlaneIds.indexOf(swimlaneId);

    if (index > -1) {
        swimlaneIds.splice(index, 1);
    }

    localStorage.setItem(this.getStorageKey(), JSON.stringify(swimlaneIds));

    $('.board-swimlane-columns-' + swimlaneId).css('display', 'table-row');
    $('.board-swimlane-tasks-' + swimlaneId).css('display', 'table-row');
    $('.hide-icon-swimlane-' + swimlaneId).css('display', 'inline');
    $('.show-icon-swimlane-' + swimlaneId).css('display', 'none');
};

Swimlane.prototype.collapse = function(swimlaneId) {
    var swimlaneIds = this.getAllCollapsed();

    if (swimlaneIds.indexOf(swimlaneId) < 0) {
        swimlaneIds.push(swimlaneId);
        localStorage.setItem(this.getStorageKey(), JSON.stringify(swimlaneIds));
    }

    $('.board-swimlane-columns-' + swimlaneId + ':not(:first-child)').css('display', 'none');
    $('.board-swimlane-tasks-' + swimlaneId).css('display', 'none');
    $('.hide-icon-swimlane-' + swimlaneId).css('display', 'none');
    $('.show-icon-swimlane-' + swimlaneId).css('display', 'inline');
};

Swimlane.prototype.isCollapsed = function(swimlaneId) {
    return this.getAllCollapsed().indexOf(swimlaneId) > -1;
};

Swimlane.prototype.getAllCollapsed = function() {
    return JSON.parse(localStorage.getItem(this.getStorageKey())) || [];
};

Swimlane.prototype.refresh = function() {
    var swimlaneIds = this.getAllCollapsed();

    for (var i = 0; i < swimlaneIds.length; i++) {
        this.collapse(swimlaneIds[i]);
    }
};

Swimlane.prototype.listen = function() {
    var self = this;
    self.dragAndDrop();

    $(document).on('click', ".board-swimlane-toggle", function(e) {
        e.preventDefault();

        var swimlaneId = $(this).data('swimlane-id');

        if (self.isCollapsed(swimlaneId)) {
            self.expand(swimlaneId);
        }
        else {
            self.collapse(swimlaneId);
        }
    });
};

Swimlane.prototype.dragAndDrop = function() {
    var self = this;

    $(".draggable-row-handle").mouseenter(function() {
        $(this).parent().parent().addClass("draggable-item-hover");
    }).mouseleave(function() {
        $(this).parent().parent().removeClass("draggable-item-hover");
    });

    $(".swimlanes-table tbody").sortable({
        forcePlaceholderSize: true,
        handle: "td:first i",
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });

            return ui;
        },
        stop: function(event, ui) {
            var swimlane = ui.item;
            swimlane.removeClass("draggable-item-selected");
            self.savePosition(swimlane.data("swimlane-id"), swimlane.index() + 1);
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
        }
    }).disableSelection();
};

Swimlane.prototype.savePosition = function(swimlaneId, position) {
    var url = $(".swimlanes-table").data("save-position-url");
    var self = this;

    this.app.showLoadingIcon();

    $.ajax({
        cache: false,
        url: url,
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "swimlane_id": swimlaneId,
            "position": position
        }),
        complete: function() {
            self.app.hideLoadingIcon();
        }
    });
};
