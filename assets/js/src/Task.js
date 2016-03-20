Kanboard.Task = function(app) {
    this.app = app;
};

Kanboard.Task.prototype.keyboardShortcuts = function() {
    var taskView = $("#task-view");
    var self = this;

    if (this.app.hasId("task-view")) {
        Mousetrap.bind("e", function() {
            self.app.get("Popover").open(taskView.data("edit-url"));
        });

        Mousetrap.bind("d", function() {
            self.app.get("Popover").open(taskView.data("description-url"));
        });

        Mousetrap.bind("c", function() {
            self.app.get("Popover").open(taskView.data("comment-url"));
        });

        Mousetrap.bind("s", function() {
            self.app.get("Popover").open(taskView.data("subtask-url"));
        });

        Mousetrap.bind("l", function() {
            self.app.get("Popover").open(taskView.data("internal-link-url"));
        });
    }
};

Kanboard.Task.prototype.onPopoverOpened = function() {
    var self = this;
    var reloadingProjectId = 0;

    // Change color
    $(document).on("click", ".color-square", function() {
        $(".color-square-selected").removeClass("color-square-selected");
        $(this).addClass("color-square-selected");
        $("#form-color_id").val($(this).data("color-id"));
    });

    // Assign to me
    $(document).on("click", ".assign-me", function(e) {
        var currentId = $(this).data("current-id");
        var dropdownId = "#" + $(this).data("target-id");

        e.preventDefault();

        if ($(dropdownId + ' option[value=' + currentId + ']').length) {
            $(dropdownId).val(currentId);
        }
    });

    // Reload page when a destination project is changed
    $(document).on("change", "select.task-reload-project-destination", function() {
        if (reloadingProjectId > 0) {
            $(this).val(reloadingProjectId);
        }
        else {
            reloadingProjectId = $(this).val();
            var url = $(this).data("redirect").replace(/PROJECT_ID/g, reloadingProjectId);

            $(".loading-icon").show();

            $.ajax({
                type: "GET",
                url: url,
                success: function(data, textStatus, request) {
                    reloadingProjectId = 0;
                    $(".loading-icon").hide();
                    self.app.get("Popover").ajaxReload(data, request, self.app.get("Popover"));
                }
            });
        }
    });
};
