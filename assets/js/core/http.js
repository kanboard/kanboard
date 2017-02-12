KB.http.request = function (method, url, headers, body) {
    var successCallback = function() {};
    var errorCallback = function() {};

    function parseResponse(request) {
        var redirect = request.getResponseHeader('X-Ajax-Redirect');
        var location = request.getResponseHeader('Location');

        if (redirect === 'self') {
            window.location.reload();
        } else if (redirect && redirect.indexOf('#') > -1) {
            window.location = redirect.split('#')[0];
        } else if (redirect) {
            window.location = redirect;
        } else if (location) {
            window.location = location;
        } else if (request.getResponseHeader('Content-Type') === 'application/json') {
            try {
                return JSON.parse(request.responseText);
            } catch (e) {}
        }

        return request.responseText;
    }

    this.execute = function () {
        var request = new XMLHttpRequest();
        request.open(method, url, true);
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        for (var header in headers) {
            if (headers.hasOwnProperty(header)) {
                request.setRequestHeader(header, headers[header]);
            }
        }

        request.onerror = function() {
            errorCallback();
        };

        request.onreadystatechange = function() {
            if (request.readyState === XMLHttpRequest.DONE) {
                var response = parseResponse(request);

                if (request.status === 200) {
                    successCallback(response);
                } else {
                    errorCallback(response);
                }
            }
        };

        request.send(body);
        return this;
    };

    this.success = function (callback) {
        successCallback = callback;
        return this;
    };

    this.error = function (callback) {
        errorCallback = callback;
        return this;
    };
};

KB.http.get = function (url) {
    return (new KB.http.request('GET', url)).execute();
};

KB.http.postJson = function (url, body) {
    var headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    return (new KB.http.request('POST', url, headers, JSON.stringify(body))).execute();
};

KB.http.postForm = function (url, formElement) {
    var formData = new FormData(formElement);
    return (new KB.http.request('POST', url, {}, formData)).execute();
};

KB.http.uploadFile = function (url, file, onProgress, onComplete, onError, onServerError) {
    var fd = new FormData();
    fd.append('files[]', file);

    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', onProgress);
    xhr.upload.addEventListener('error', onError);
    xhr.open('POST', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                onComplete();
            } else if (typeof onServerError !== 'undefined') {
                onServerError(JSON.parse(xhr.responseText));
            }
        }
    };

    xhr.send(fd);
};
