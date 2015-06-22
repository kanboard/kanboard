/**
 * Created by wmeyer on 6/15/2015.
 */

$( document ).ready(function() {
    var project_id = GetURLParameter('project_id');
    var redirect = GetURLParameter('redirect');
    var urlVariables = GetURLStrings('&redirect=true');
    console.log(redirect);
    console.log(project_id);
    console.log(urlVariables[0]);
    console.log(urlVariables[1]);
    if (redirect == 'true') {
        var popoverUrl = '?controller=task&action=create&project_id=' + project_id + urlVariables[1];
        console.log(popoverUrl);
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