KB.onClick('.js-autocomplete-email', function (e) {
    var email = KB.dom(e.target).data('email');
    var emailField = KB.find('.js-mail-form input[type="email"]');

    if (email && emailField) {
        emailField.build().value = email;
    }

    _KB.controllers.Dropdown.close();
});

KB.onClick('.js-autocomplete-subject', function (e) {
    var subject = KB.dom(e.target).data('subject');
    var subjectField = KB.find('.js-mail-form input[name="subject"]');

    if (subject && subjectField) {
        subjectField.build().value = subject;
    }

    _KB.controllers.Dropdown.close();
});
