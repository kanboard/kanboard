Kanboard.Task = function(app) {
    this.app = app;
};

// TODO: rewrite this code
Kanboard.Task.prototype.onPopoverOpened = function() {
    var self = this;

    self.renderColorPicker();

    // Assign to me
    $(document).on("click", ".assign-me", function(e) {
        var currentId = $(this).data("current-id");
        var dropdownId = "#" + $(this).data("target-id");

        e.preventDefault();

        if ($(dropdownId + ' option[value=' + currentId + ']').length) {
            $(dropdownId).val(currentId);
        }
    });
};

Kanboard.Task.prototype.renderColorPicker = function() {
    function renderColorOption(color) {
        return $(
            '<div class="color-picker-option">' +
            '<div class="color-picker-square color-' + color.id + '"></div>' +
            '<div class="color-picker-label">' + color.text + '</div>' +
            '</div>'
        );
    }

    $(".color-picker").select2({
        minimumResultsForSearch: Infinity,
        templateResult: renderColorOption,
        templateSelection: renderColorOption
    });
};
