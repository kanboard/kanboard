function Task(app) {
    this.app = app;
}

Task.prototype.listen = function() {
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
        e.preventDefault();

        var currentId = $(this).data("current-id");
        var dropdownId = "#" + $(this).data("target-id");

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

                    self.app.popover.afterSubmit(data, request, self.app.popover);
                }
            });
        }
    });
};
