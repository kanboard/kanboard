KB.interval(60, function () {
    var statusUrl = KB.find('body').data('statusUrl');
    var loginUrl = KB.find('body').data('loginUrl');

    if (KB.find('.form-login') === null) {
        KB.http.get(statusUrl).authError(function () {
            window.location = loginUrl;
        });
    }
});
