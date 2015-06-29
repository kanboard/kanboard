$(document).ready(function() {
    var project_id = GetURLParameter('project_id');
    var redirect = GetURLParameter('redirect');
    var urlVariables = GetURLStrings('&redirect=true');
    if (redirect == 'true') {
        var popoverUrl = '?controller=task&action=create&project_id=' + project_id + urlVariables[1];
        Kanboard.OpenPopover(popoverUrl, Kanboard.InitAfterAjax);
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

function GetURLStrings(parameter) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split(parameter);
    return sURLVariables;
}