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

KB.onClick('.js-autocomplete-subject', function (e) {
    var subject = KB.dom(e.target).data('subject');

    if (subject) {
        var subjectField = KB.find('.js-task-mail-form input[name="subject"]');

        if (subjectField) {
            subjectField.attr('value', subject);
            _KB.controllers['Dropdown'].close();
        }
    }
});
