KB.dom = function (tag) {

    function DomManipulation(tag) {
        var element = typeof tag === 'string' ? document.createElement(tag) : tag;

        this.attr = function (attribute, value) {
            if (value !== null && typeof value !== 'undefined') {
                element.setAttribute(attribute, value);
                return this;
            } else {
                return element.getAttribute(attribute);
            }
        };

        this.data = function (attribute, value) {
            if (arguments.length === 1) {
                return element.dataset[attribute];
            }
            element.dataset[attribute] = value;
            return this;
        };

        this.hide = function () {
            element.style.display = 'none';
            return this;
        };

        this.show = function () {
            element.style.display = 'block';
            return this;
        };

        this.toggle = function () {
            if (element.style.display === 'none') {
                this.show();
            } else{
                this.hide();
            }

            return this;
        };

        this.style = function(attribute, value) {
            element.style[attribute] = value;
            return this;
        };

        this.on = function (eventName, callback, ignorePrevent) {
            element.addEventListener(eventName, function (e) {
                if (! ignorePrevent) {
                    e.preventDefault();
                }
                callback(e.target);
            });

            return this;
        };

        this.click = function (callback) {
            return this.on('click', callback);
        };

        this.mouseover = function (callback) {
            return this.on('mouseover', callback);
        };

        this.change = function (callback) {
            return this.on('change', callback);
        };

        this.add = function (node) {
            element.appendChild(node);
            return this;
        };

        this.replace = function (node) {
            element.parentNode.replaceChild(node, element);
            return this;
        };

        this.html = function (html) {
            element.innerHTML = html;
            return this;
        };

        this.text = function (text) {
            element.appendChild(document.createTextNode(text));
            return this;
        };

        this.replaceText = function (text) {
            element.textContent = text;
            return this;
        };

        this.addClass = function (className) {
            element.classList.add(className);
            return this;
        };

        this.removeClass = function (className) {
            element.classList.remove(className);
            return this;
        };

        this.toggleClass = function (className) {
            element.classList.toggle(className);
            return this;
        };

        this.hasClass = function (className) {
            return element.classList.contains(className);
        };

        this.disable = function () {
            element.disabled = true;
            return this;
        };

        this.enable = function () {
            element.disabled = false;
            return this;
        };

        this.remove = function () {
            element.parentNode.removeChild(element);
            return this;
        };

        this.empty = function () {
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }
            return this;
        };

        this.parent = function (selector) {
            for (; element && element !== document; element = element.parentNode) {
                if (element.matches(selector)) {
                    return element;
                }
            }

            return null;
        };

        this.find = function (selector) {
            return element.querySelector(selector);
        };

        this.for = function (tag, list) {
            for (var i = 0; i < list.length; i++) {
                var dict = list[i];

                if (typeof dict !== 'object') {
                    element.appendChild(KB.dom(tag).text(dict).build());
                } else {
                    var node = KB.dom(tag);

                    for (var attribute in dict) {
                        if (dict.hasOwnProperty(attribute) && attribute in this && typeof this[attribute] === 'function') {
                            node[attribute](dict[attribute]);
                        } else {
                            node.attr(attribute, dict[attribute]);
                        }
                    }

                    element.appendChild(node.build());
                }
            }

            return this;
        };

        this.build = function () {
            return element;
        };
    }

    return new DomManipulation(tag);
};

KB.find = function (selector) {
    var element = document.querySelector(selector);

    if (element) {
        return KB.dom(element);
    }

    return null;
};

KB.exists = function (selector) {
    return !!document.querySelector(selector);
};

KB.focus = function (selector) {
    var element = document.querySelector(selector);

    if (element) {
        return element.focus();
    }
};
