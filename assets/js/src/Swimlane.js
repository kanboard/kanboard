function Swimlane() {
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
