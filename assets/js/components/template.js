KB.onClick('.js-template', function (e) {
    var template = KB.dom(e.target).data('template');
    var target = KB.dom(e.target).data('templateTarget');
    var targetField = KB.find(target);

    if (targetField) {
        targetField.build().value = template;
    }

    _KB.controllers.Dropdown.close();
});