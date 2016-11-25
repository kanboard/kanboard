var KB = {
    components: {},
    utils: {},
    clickEvents: {}
};

KB.onClick = function (selector, callback) {
    this.clickEvents[selector] = callback;
};

KB.listen = function () {
    var self = this;

    function onClick(e) {
        for (var selector in self.clickEvents) {
            if (self.clickEvents.hasOwnProperty(selector) && e.target.matches(selector)) {
                e.preventDefault();
                self.clickEvents[selector](e);
            }
        }
    }

    document.addEventListener('click', onClick, false);
};

KB.component = function (name, object) {
    this.components[name] = object;
};

KB.render = function () {
    for (var name in this.components) {
        var elementList = document.querySelectorAll('.js-' + name);

        for (var i = 0; i < elementList.length; i++) {
            if (this.components.hasOwnProperty(name)) {
                var object = this.components[name];
                var component = new object(elementList[i], JSON.parse(elementList[i].dataset.params));

                component.render();
                elementList[i].className = elementList[i].className + '-rendered';
            }
        }
    }
};

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
