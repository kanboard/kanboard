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
