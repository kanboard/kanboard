KB.component('suggest-menu', function(containerElement, options) {

    function onKeyDown(e) {
        switch (KB.utils.getKey(e)) {
            case 'Escape':
                destroy();
                break;
            case 'ArrowUp':
                e.preventDefault();
                e.stopImmediatePropagation();
                moveUp();
                break;
            case 'ArrowDown':
                e.preventDefault();
                e.stopImmediatePropagation();
                moveDown();
                break;
            case 'Enter':
                e.preventDefault();
                e.stopImmediatePropagation();
                insertSelectedItem();
                break;
        }
    }

    function onClick() {
        insertSelectedItem();
    }

    function onMouseOver(element) {
        if (KB.dom(element).hasClass('suggest-menu-item')) {
            KB.find('.suggest-menu-item.active').removeClass('active');
            KB.dom(element).addClass('active');
        }
    }

    function insertSelectedItem() {
        containerElement.focus();

        var element = KB.find('.suggest-menu-item.active');
        var value = element.data('value');
        var trigger = element.data('trigger');
        var content = containerElement.value;
        var text = getLastWord(containerElement);
        var substitute = trigger + value + ' ';
        var selectionPosition = KB.utils.getSelectionPosition(containerElement);
        var before = content.substring(0, selectionPosition.selectionStart - text.length);
        var after = content.substring(selectionPosition.selectionEnd);
        var position = before.length + substitute.length;

        containerElement.value = before + substitute + after;
        containerElement.setSelectionRange(position, position);

        destroy();
    }

    function getLastWord(element) {
        var lines = element.value.substring(0, element.selectionEnd).split("\n");
        var lastLine = lines[lines.length - 1];
        var words = lastLine.split(' ');
        return words[words.length - 1];
    }

    function getParentElement() {
        var selectors = ['#modal-content form', '#modal-content', 'body'];

        for (var i = 0; i < selectors.length; i++) {
            var element = document.querySelector(selectors[i]);

            if (element !== null) {
                return element;
            }
        }

        return null;
    }

    function resetSelection() {
        var elements = document.querySelectorAll('.suggest-menu-item');

        for (var i = 0; i < elements.length; i++) {
            if (KB.dom(elements[i]).hasClass('active')) {
                KB.dom(elements[i]).removeClass('active');
                break;
            }
        }

        return {items: elements, index: i};
    }

    function moveUp() {
        var result = resetSelection();

        if (result.index > 0) {
            result.index = result.index - 1;
        }

        KB.dom(result.items[result.index]).addClass('active');
    }

    function moveDown() {
        var result = resetSelection();

        if (result.index < result.items.length - 1) {
            result.index++;
        }

        KB.dom(result.items[result.index]).addClass('active');
    }

    function destroy() {
        var element = KB.find('#suggest-menu');

        if (element !== null) {
            element.remove();
        }

        document.removeEventListener('keydown', onKeyDown, false);
    }

    function search(element) {
        var text = getLastWord(element);
        var trigger = getTrigger(text, options.triggers);

        destroy();

        if (trigger !== null) {
            fetchItems(trigger, text.substring(trigger.length), options.triggers[trigger]);
        }
    }

    function getTrigger(text, triggers) {
        for (var trigger in triggers) {
            if (triggers.hasOwnProperty(trigger) && text.indexOf(trigger) === 0) {
                return trigger;
            }
        }

        return null;
    }

    function fetchItems(trigger, text, params) {
        if (typeof params === 'string') {
            var regex = new RegExp('SEARCH_TERM', 'g');
            var url = params.replace(regex, text);

            KB.http.get(url).success(function (response) {
                onItemFetched(trigger, text, response);
            });
        } else {
            onItemFetched(trigger, text, params);
        }
    }

    function onItemFetched(trigger, text, items) {
        items = filterItems(text, items);

        if (items.length > 0) {
            renderMenu(buildItems(trigger, items));
        }
    }

    function filterItems(text, items) {
        var filteredItems = [];

        if (text.length === 0) {
            return items;
        }

        for (var i = 0; i < items.length; i++) {
            if (items[i].value.toLowerCase().indexOf(text.toLowerCase()) === 0) {
                filteredItems.push(items[i]);
            }
        }

        return filteredItems;
    }

    function buildItems(trigger, items) {
        var elements = [];

        for (var i = 0; i < items.length; i++) {
            var className = 'suggest-menu-item';

            if (i === 0) {
                className += ' active';
            }

            elements.push({
                class: className,
                html: items[i].html,
                'data-value': items[i].value,
                'data-trigger': trigger
            });
        }

        return elements;
    }

    function renderMenu(items) {
        var parentElement = getParentElement();
        var caretPosition = getCaretCoordinates(containerElement, containerElement.selectionEnd);
        var left = caretPosition.left + containerElement.offsetLeft - containerElement.scrollLeft;
        var top = caretPosition.top + containerElement.offsetTop - containerElement.scrollTop + 16;

        document.addEventListener('keydown', onKeyDown, false);

        var menu = KB.dom('ul')
            .attr('id', 'suggest-menu')
            .click(onClick)
            .mouseover(onMouseOver)
            .style('left', left + 'px')
            .style('top', top + 'px')
            .for('li', items)
            .build();

        parentElement.appendChild(menu);
    }

    this.render = function () {
        containerElement.addEventListener('input', function () {
            search(this);
        });
    };
});
