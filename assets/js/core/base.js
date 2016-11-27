var KB = {
    components: {},
    utils: {},
    html: {},
    http: {},
    listeners: {
        clicks: {},
        internals: {}
    }
};

KB.on = function (eventType, callback) {
    if (! this.listeners.internals.hasOwnProperty(eventType)) {
        this.listeners.internals[eventType] = [];
    }

    this.listeners.internals[eventType].push(callback);
};

KB.trigger = function (eventType, eventData) {
    if (this.listeners.internals.hasOwnProperty(eventType)) {
        for (var i = 0; i < this.listeners.internals[eventType].length; i++) {
            if (! this.listeners.internals[eventType][i](eventData)) {
                break;
            }
        }
    }
};

KB.onClick = function (selector, callback) {
    this.listeners.clicks[selector] = callback;
};

KB.listen = function () {
    var self = this;

    function onClick(e) {
        for (var selector in self.listeners.clicks) {
            if (self.listeners.clicks.hasOwnProperty(selector) && e.target.matches(selector)) {
                e.preventDefault();
                self.listeners.clicks[selector](e);
            }
        }
    }

    document.addEventListener('click', onClick, false);
};

KB.component = function (name, object) {
    this.components[name] = object;
};

KB.getComponent = function (name, containerElement, options) {
    var object = this.components[name];
    return new object(containerElement, options);
};

KB.render = function () {
    for (var name in this.components) {
        var elementList = document.querySelectorAll('.js-' + name);

        for (var i = 0; i < elementList.length; i++) {
            if (this.components.hasOwnProperty(name)) {
                var options;

                if (elementList[i].dataset.params) {
                    options = JSON.parse(elementList[i].dataset.params);
                }

                var component = KB.getComponent(name, elementList[i], options);
                component.render();
                elementList[i].className = elementList[i].className + '-rendered';
            }
        }
    }
};
