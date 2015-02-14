Kanboard.Task = (function() {

    jQuery(document).ready(function() {

        if ($(".task-autocomplete").length) {

            $("input[type=submit]").attr('disabled','disabled');

            $(".task-autocomplete").autocomplete({
                source: $(".task-autocomplete").data("search-url"),
                minLength: 2,
                select: function(event, ui) {
                    var field = $(".task-autocomplete").data("dst-field");
                    $("input[name=" + field + "]").val(ui.item.id);

                    $("input[type=submit]").removeAttr('disabled');
                }
            });
        }
    });

})();
