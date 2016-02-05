function Subtask() {
}

Subtask.prototype.listen = function() {
    $(document).on("click", ".subtask-toggle-status", function(e) {
        e.preventDefault();
        var el = $(this);

        $.ajax({
            cache: false,
            url: el.attr("href"),
            success: function(data) {
                if (el.hasClass("subtask-refresh-table")) {
                    $(".subtasks-table").replaceWith(data);
                } else {
                    el.replaceWith(data);
                }
            }
        });
    });

    $(document).on("click", ".subtask-toggle-timer", function(e) {
        e.preventDefault();
        var el = $(this);

        $.ajax({
            cache: false,
            url: el.attr("href"),
            success: function(data) {
                $(".subtasks-table").replaceWith(data);
            }
        });
    });
};
