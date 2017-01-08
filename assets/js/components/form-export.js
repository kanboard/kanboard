KB.onClick('.js-form-export', function(e) {
    var formElement = document.querySelector('#modal-content form');
    var fromElement = formElement.querySelector('#form-from');
    var toElement = formElement.querySelector('#form-to');

    if (fromElement.value !== '' && toElement.value !== '') {
        formElement.submit();
    }
});
