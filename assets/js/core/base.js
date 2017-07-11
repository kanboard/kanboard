var KB = {
    components: {},
    utils: {},
    html: {},
    http: {},
    listeners: {
        clicks: {},
        changes: {},
        keys: [],
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
            this.listeners.internals[eventType][i](eventData);
        }
    }
};

KB.removeListener = function (eventType, callback) {
    if (this.listeners.internals.hasOwnProperty(eventType)) {
        for (var i = 0; i < this.listeners.internals[eventType].length; i++) {
            if (this.listeners.internals[eventType][i] === callback) {
                this.listeners.internals[eventType].splice(i, 1);
            }
        }
    }
};

KB.onClick = function (selector, callback, noPreventDefault) {
    this.listeners.clicks[selector] = {
        callback: callback,
        noPreventDefault: noPreventDefault === true
    };
};

KB.onChange = function (selector, callback) {
    this.listeners.changes[selector] = callback;
};

KB.onKey = function (combination, callback, ignoreInputField, ctrlKey, metaKey) {
    this.listeners.keys.push({
        'combination': combination,
        'callback': callback,
        'ignoreInputField': ignoreInputField || false,
        'ctrlKey': ctrlKey || false,
        'metaKey': metaKey || false
    });
};

KB.listen = function () {
    var self = this;
    var keysQueue = [];

    function onClick(e) {
        for (var selector in self.listeners.clicks) {
            if (self.listeners.clicks.hasOwnProperty(selector) && e.target.matches(selector)) {
                if (! self.listeners.clicks[selector].noPreventDefault) {
                    e.preventDefault();
                }

                self.listeners.clicks[selector].callback(e);
            }
        }
    }

    function onChange(e) {
        for (var selector in self.listeners.changes) {
            if (self.listeners.changes.hasOwnProperty(selector) && e.target.matches(selector)) {
                self.listeners.changes[selector](e.target);
            }
        }
    }

    function onKeyPressed(e) {
        var key = KB.utils.getKey(e);
        var isInputField = KB.utils.isInputField(e);

        if (! isInputField || ['Escape', 'Enter'].indexOf(key) !== -1) {
            keysQueue.push(key);
        }

        if (keysQueue.length > 0) {
            var reset = true;

            for (var i = 0; i < self.listeners.keys.length; i++) {
                var params = self.listeners.keys[i];
                var combination = params.combination;
                var sequence = combination.split('+');

                if (KB.utils.arraysIdentical(keysQueue, sequence) &&
                    e.ctrlKey === params.ctrlKey && e.metaKey === params.metaKey) {
                    if (isInputField && !params.ignoreInputField) {
                        keysQueue = [];
                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();
                    keysQueue = [];
                    params.callback(e);
                    break;
                } else if (KB.utils.arraysStartsWith(keysQueue, sequence) && keysQueue.length < sequence.length) {
                    reset = false;
                }
            }

            if (reset) {
                keysQueue = [];
            }
        }
    }

    document.addEventListener('click', onClick, false);
    document.addEventListener('change', onChange, false);
    window.addEventListener('keydown', onKeyPressed, false);
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

KB.interval = function (seconds, callback) {
    setInterval(callback, seconds * 1000);
};
