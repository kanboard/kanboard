'use strict';

var Kanboard = {};

var KB = {
    components: {}
};

KB.component = function (name, object) {
    this.components[name] = object;
};

KB.render = function () {
    for (var name in this.components) {
        var elementList = document.querySelectorAll('.js-' + name);

        for (var i = 0; i < elementList.length; i++) {
            var object = this.components[name];
            var component = new object(elementList[i], JSON.parse(elementList[i].dataset.params));
            component.render();
            elementList[i].className = elementList[i].className + '-rendered';
        }
    }
};

KB.el = function (tag) {

    function DOMBuilder(tag) {
        var element = typeof tag === 'string' ? document.createElement(tag) : tag;

        this.attr = function (attribute, value) {
            if (value !== null) {
                element.setAttribute(attribute, value);
            }
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

        this.click = function (callback) {
            element.onclick = function (e) {
                e.preventDefault();
                callback();
            };
            return this;
        };

        this.add = function (node) {
            element.appendChild(node);
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

        this.for = function (tag, list) {
            for (var i = 0; i < list.length; i++) {
                var dict = list[i];

                if (typeof dict !== 'object') {
                    element.appendChild(KB.el(tag).text(dict).build());
                } else {
                    var node = KB.el(tag);

                    for (var attribute in dict) {
                        if (attribute in this && typeof this[attribute] === 'function') {
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

    return new DOMBuilder(tag);
};
