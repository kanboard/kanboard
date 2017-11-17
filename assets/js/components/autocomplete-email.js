KB.onClick('.js-autocomplete-email', function (e) {
    var email = e.target.dataset.email;
    var emailField = document.querySelector('.js-mail-form input[name="emails"]');

    if (!email || !emailField) {
        return;
    }

    if (emailField.value !== '') {
        emailField.value += ', ' + email;
    } else {
        emailField.value = email;
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
