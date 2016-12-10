KB.onChange('.js-project-creation-select-options', function (element) {
    var projectId = element.value;

    if (projectId === '0') {
        KB.find('.js-project-creation-options').hide();
    } else {
        KB.find('.js-project-creation-options').show();
    }
});
