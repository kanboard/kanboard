KB.http.request = function (method, url, headers, body) {
    var successCallback = function() {};
    var errorCallback = function() {};

    function parseResponse(request) {
        try {
            return JSON.parse(request.responseText);
        } catch (e) {
            return request.responseText;
        }
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
