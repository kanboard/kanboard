KB.component('select-dropdown-autocomplete', function(containerElement, options) {
    var componentElement, inputElement, inputHiddenElement, chevronIconElement, loadingIconElement;

    function onLoadingStart() {
        KB.dom(loadingIconElement).show();
        KB.dom(chevronIconElement).hide();
    }

    function onLoadingStop() {
        KB.dom(loadingIconElement).hide();
        KB.dom(chevronIconElement).show();
    }

    function onScroll() {
        var menuElement = KB.find('#select-dropdown-menu');

        if (menuElement) {
            var componentPosition = componentElement.getBoundingClientRect();
            menuElement.style('top', (document.body.scrollTop + componentPosition.bottom) + 'px');
        }
    }

    function onKeyDown(e) {
        switch (KB.utils.getKey(e)) {
            case 'Escape':
                inputElement.value = '';
                destroyDropdownMenu();
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

    function onInputChanged() {
        destroyDropdownMenu();
        renderDropdownMenu();
    }

    function onItemMouseOver(element) {
        if (KB.dom(element).hasClass('select-dropdown-menu-item')) {
            KB.find('.select-dropdown-menu-item.active').removeClass('active');
            KB.dom(element).addClass('active');
        }
    }

    function onItemClick() {
        insertSelectedItem();
    }

    function onDocumentClick(e) {
        if (! containerElement.contains(e.target)) {
            inputElement.value = '';
            destroyDropdownMenu();
        }
    }

    function toggleDropdownMenu() {
        var menuElement = KB.find('#select-dropdown-menu');

        if (menuElement === null) {
            renderDropdownMenu();
        } else {
            destroyDropdownMenu();
        }
    }

    function insertSelectedItem() {
        var element = KB.find('.select-dropdown-menu-item.active');
        var value = element.data('value');
        inputHiddenElement.value = value;
        inputElement.value = options.items[value];
        destroyDropdownMenu();

        if (options.redirect) {
            window.location = options.redirect.url.replace(new RegExp(options.redirect.regex, 'g'), value);
        } else if (options.replace) {
            onLoadingStart();
            KB.modal.replace(options.replace.url.replace(new RegExp(options.replace.regex, 'g'), value));
        }
    }

    function resetSelection() {
        var elements = document.querySelectorAll('.select-dropdown-menu-item');

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

    function buildItems(items) {
        var elements = [];

        for (var key in items) {
            if (items.hasOwnProperty(key)) {
                elements.push({
                    'class': 'select-dropdown-menu-item',
                    'text': items[key],
                    'data-label': items[key],
                    'data-value': key
                });
            }
        }

        if (options.sortByKeys) {
            elements.sort(function (a, b) {
                var value1 = a['data-value'].toLowerCase();
                var value2 = b['data-value'].toLowerCase();
                return value1 < value2 ? -1 : (value1 > value2 ? 1 : 0);
            });
        } else {
            elements.sort(function (a, b) {
                var value1 = a['data-label'].toLowerCase();
                var value2 = b['data-label'].toLowerCase();
                return value1 < value2 ? -1 : (value1 > value2 ? 1 : 0);
            });
        }

        return elements;
    }

    function filterItems(text, items) {
        var filteredItems = [];
        var hasActiveItem = false;

        for (var i = 0; i < items.length; i++) {
            if (text.length === 0 || items[i]['data-label'].toLowerCase().indexOf(text.toLowerCase()) > -1) {
                var item = items[i];

                if (typeof options.defaultValue !== 'undefined' && String(options.defaultValue) === item['data-value']) {
                    item.class += ' active';
                    hasActiveItem = true;
                }

                filteredItems.push(item);
            }
        }

        if (! hasActiveItem && filteredItems.length > 0) {
            filteredItems[0].class += ' active';
        }

        return filteredItems;
    }

    function buildDropdownMenu() {
        var itemElements = filterItems(inputElement.value, buildItems(options.items));
        var componentPosition = componentElement.getBoundingClientRect();
        var windowPosition = document.body.scrollTop || document.documentElement.scrollTop;

        if (itemElements.length === 0) {
            return null;
        }

        return KB.dom('ul')
            .attr('id', 'select-dropdown-menu')
            .style('top', (windowPosition + componentPosition.bottom) + 'px')
            .style('left', componentPosition.left + 'px')
            .style('width', componentPosition.width + 'px')
            .style('maxHeight', (window.innerHeight - componentPosition.bottom - 20) + 'px')
            .mouseover(onItemMouseOver)
            .click(onItemClick)
            .for('li', itemElements)
            .build();
    }

    function destroyDropdownMenu() {
        var menuElement = KB.find('#select-dropdown-menu');

        if (menuElement !== null) {
            menuElement.remove();
        }

        document.removeEventListener('keydown', onKeyDown, false);
        document.removeEventListener('click', onDocumentClick, false);
    }

    function renderDropdownMenu() {
        var element = buildDropdownMenu();

        if (element !== null) {
            document.body.appendChild(element);
        }

        document.addEventListener('keydown', onKeyDown, false);
        document.addEventListener('click', onDocumentClick, false);
    }

    function getPlaceholderValue() {
        if (options.defaultValue && options.defaultValue in options.items) {
            return options.items[options.defaultValue];
        }

        if (options.placeholder) {
            return options.placeholder;
        }

        return '';
    }

    this.render = function () {
        KB.on('select.dropdown.loading.start', onLoadingStart);
        KB.on('select.dropdown.loading.stop', onLoadingStop);

        KB.on('modal.close', function () {
            KB.removeListener('select.dropdown.loading.start', onLoadingStart);
            KB.removeListener('select.dropdown.loading.stop', onLoadingStop);
        });

        chevronIconElement = KB.dom('i')
            .attr('class', 'fa fa-chevron-down select-dropdown-chevron')
            .click(toggleDropdownMenu)
            .build();

        loadingIconElement = KB.dom('span')
            .hide()
            .addClass('select-loading-icon')
            .add(KB.dom('i').attr('class', 'fa fa-spinner fa-pulse').build())
            .build();

        inputHiddenElement = KB.dom('input')
            .attr('type', 'hidden')
            .attr('name', options.name)
            .attr('value', options.defaultValue || '')
            .build();

        inputElement = KB.dom('input')
            .attr('type', 'text')
            .attr('placeholder', getPlaceholderValue())
            .addClass('select-dropdown-input')
            .on('focus', toggleDropdownMenu)
            .on('input', onInputChanged, true)
            .build();

        componentElement = KB.dom('div')
            .addClass('select-dropdown-input-container')
            .add(inputHiddenElement)
            .add(inputElement)
            .add(chevronIconElement)
            .add(loadingIconElement)
            .build();

        containerElement.appendChild(componentElement);

        if (options.onFocus) {
            options.onFocus.forEach(function (eventName) {
                KB.on(eventName, function() { inputElement.focus(); });
            });
        }

        window.addEventListener('scroll', onScroll, false);
    };
});
