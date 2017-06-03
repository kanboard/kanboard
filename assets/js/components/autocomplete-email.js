KB.onClick('.js-autocomplete-email', function (e) {
    var email = KB.dom(e.target).data('email');

    if (email) {
        var emailField = KB.find('.js-task-mail-form input[type="email"]');

        if (emailField) {
            emailField.attr('value', email);
            _KB.controllers['Dropdown'].close();
        }
    }
});
