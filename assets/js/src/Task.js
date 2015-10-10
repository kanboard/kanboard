function Task() {
}

Task.prototype.listen = function() {
    $(document).on("click", ".color-square", function() {
        $(".color-square-selected").removeClass("color-square-selected");
        $(this).addClass("color-square-selected");
        $("#form-color_id").val($(this).data("color-id"));
    });
};
