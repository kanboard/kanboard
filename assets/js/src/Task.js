function Task() {
}

Task.prototype.listen = function() {
    $(document).on("click", ".color-square", function() {
        $(".color-square-selected").removeClass("color-square-selected");
        $(this).addClass("color-square-selected");
        $("#form-color_id").val($(this).data("color-id"));
    });

    $(document).on("click", ".assign-me", function(e) {
        e.preventDefault();

        var currentId = $(this).data("current-id");
        var dropdownId = "#" + $(this).data("target-id");

        if ($(dropdownId + ' option[value=' + currentId + ']').length) {
            $(dropdownId).val(currentId);
        }
    });
};
