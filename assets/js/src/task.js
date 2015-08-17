Kanboard.Task = (function() {

    var task_id = GetURLParameter('task');
    var project_id = GetURLParameter('project');

    function keyboard_shortcuts()
    {
        Mousetrap.bind("i", function() {
            var url = '/?controller=board&action=assignToMe&task_id=' + task_id + '&project_id=' + project_id;
            console.log(url);
            $.ajax({
                url: url,
                type: "POST",
                success: function(data, textStatus, jqXHR) {
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            });
        });
    }

    jQuery(document).ready(function() {
        if (Kanboard.Exists("task-section")) {
            keyboard_shortcuts();
        }
    });

    function GetURLParameter(parameter) {
        var sPageURLVariables = window.location.pathname.split('/');
        for (var i = 0; i < sPageURLVariables.length-1; i++) {
            if (sPageURLVariables[i] == parameter) {
                return sPageURLVariables[i+1];
            }
        }
    }

})();
