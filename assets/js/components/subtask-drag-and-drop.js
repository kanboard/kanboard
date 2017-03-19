KB.on('dom.ready', function() {

    function savePosition(subtaskId, position) {
        var url = $(".subtasks-table").data("save-position-url");

        $.ajax({
            cache: false,
            url: url,
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "subtask_id": subtaskId,
                "position": position
            })
        });
    }

    $(".draggable-row-handle").mouseenter(function() {
        $(this).parent().parent().addClass("draggable-item-hover");
    }).mouseleave(function() {
        $(this).parent().parent().removeClass("draggable-item-hover");
    });

    $(".subtasks-table tbody").sortable({
        forcePlaceholderSize: true,
        handle: "td:first i",
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });

            return ui;
        },
        stop: function(event, ui) {
            var subtask = ui.item;
            subtask.removeClass("draggable-item-selected");
            savePosition(subtask.data("subtask-id"), subtask.index() + 1);
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
        }
    }).disableSelection();
});
