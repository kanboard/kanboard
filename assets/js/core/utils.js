KB.utils.formatDuration = function (d) {
    if (d >= 86400) {
        return Math.round(d/86400) + "d";
    }
    else if (d >= 3600) {
        return Math.round(d/3600) + "h";
    }
    else if (d >= 60) {
        return Math.round(d/60) + "m";
    }

    return d + "s";
};

KB.utils.getSelectionPosition = function (element) {
    var selectionStart, selectionEnd;

    if (element.value.length < element.selectionStart) {
        selectionStart = element.value.length;
    } else {
        selectionStart = element.selectionStart;
    }

    if (element.selectionStart === element.selectionEnd) {
        selectionEnd = selectionStart;
    } else {
        selectionEnd = element.selectionEnd;
    }

    return {
        selectionStart: selectionStart,
        selectionEnd: selectionEnd
    };
};

KB.utils.arraysIdentical = function (a, b) {
    var i = a.length;

    if (i !== b.length) {
        return false;
    }

    while (i--) {
        if (a[i] !== b[i]) {
            return false;
        }
    }

    return true;
};

KB.utils.arraysStartsWith = function (array1, array2) {
    var length = Math.min(array1.length, array2.length);

    for (var i = 0; i < length; i++) {
        if (array1[i] !== array2[i]) {
            return false;
        }
    }

    return true;
};

KB.utils.isInputField = function (event) {
    var element = event.target;

    return !!(element.tagName === 'INPUT' ||
    element.tagName === 'SELECT' ||
    element.tagName === 'TEXTAREA' ||
    element.isContentEditable);
};

KB.utils.getKey = function (e) {
    var mapping = {
        'Esc': 'Escape',
        'Up': 'ArrowUp',
        'Down': 'ArrowDown',
        'Left': 'ArrowLeft',
        'Right': 'ArrowRight'
    };

    for (var key in mapping) {
        if (mapping.hasOwnProperty(key) && key === e.key) {
            return mapping[key];
        }
    }

    return e.key;
};

KB.utils.getViewportSize = function () {
    return {
        width: Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
        height: Math.max(document.documentElement.clientHeight, window.innerHeight || 0)
    };
};

KB.utils.isVisible = function() {
    var property = '';

    if (typeof document.hidden !== 'undefined') {
        property = 'visibilityState';
    } else if (typeof document.mozHidden !== 'undefined') {
        property = 'mozVisibilityState';
    } else if (typeof document.msHidden !== 'undefined') {
        property = 'msVisibilityState';
    } else if (typeof document.webkitHidden !== 'undefined') {
        property = 'webkitVisibilityState';
    }

    if (property !== '') {
        return document[property] === 'visible';
    }

    return true;
};
