Kanboard.Task = (function() {

    function keyboard_shortcuts()
    {
        Mousetrap.bind("i", function() {
            var url = window.location.origin + window.location.pathname + '?controller=board&action=assignToMe&task_id=' + task_id + '&project_id=' + project_id;
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

        Mousetrap.bind("b", function() {
            var url = window.location.origin + window.location.pathname +  '?controller=board&action=show&project_id=' + project_id + '#swimlane-0';
            window.location.replace(url);
        });
    }

    jQuery(document).ready(function() {
        console.log('Kanboard.Task controle');
        if (Kanboard.Exists("task")) {
            console.log('Kanboard.Task exists');
            var task_id = GetURLParameter('task_id');
            var project_id = GetURLParameter('project_id');
            keyboard_shortcuts();
        }
    });

    function GetURLParameter(parameter) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == parameter) {
                return sParameterName[1];
            }
        }
    }

})();
