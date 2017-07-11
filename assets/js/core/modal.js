(function () {
    var isOpen = false;

    function onOverlayClick(e) {
        if (e.target.matches('#modal-overlay')) {
            e.stopPropagation();
            e.preventDefault();
            destroy();
        }
    }

    function onCloseButtonClick() {
        KB.trigger('modal.close');
    }

    function onFormSubmit() {
        KB.trigger('modal.loading');
        submitForm();
    }

    function getForm() {
        return document.querySelector('#modal-content form:not(.js-modal-ignore-form)');
    }

    function submitForm() {
        var form = getForm();

        if (form) {
            var url = form.getAttribute('action');

            if (url) {
                KB.http.postForm(url, form).success(function (response) {
                    KB.trigger('modal.stop');

                    if (response) {
                        replace(response);
                    } else {
                        destroy();
                    }
                }).error(function (response) {
                    KB.trigger('modal.stop');

                    if (response.hasOwnProperty('message')) {
                        window.alert(response.message);
                    }
                });
            }
        }
    }

    function afterRendering() {
        var formElement = KB.find('#modal-content form');
        if (formElement) {
            formElement.on('submit', onFormSubmit, false);
        }

        var autoFocusElement = document.querySelector('#modal-content input[autofocus]');
        if (autoFocusElement) {
            autoFocusElement.focus();
        }

        KB.render();
        _KB.datePicker();
        _KB.autoComplete();
        _KB.tagAutoComplete();
        _KB.get('Task').onPopoverOpened();

        KB.trigger('modal.afterRender');
    }

    function replace(html) {
        var contentElement = KB.find('#modal-content');

        if (contentElement) {
            contentElement.replace(KB.dom('div')
                .attr('id', 'modal-content')
                .html(html)
                .build()
            );

            afterRendering();
        }
    }

    function create(html, width, overlayClickDestroy) {
        var closeButtonElement = KB.dom('a')
            .attr('href', '#')
            .attr('id', 'modal-close-button')
            .html('<i class="fa fa-times"></i>')
            .click(onCloseButtonClick)
            .build();

        var headerElement = KB.dom('div')
            .attr('id', 'modal-header')
            .add(closeButtonElement)
            .build();

        var contentElement = KB.dom('div')
            .attr('id', 'modal-content')
            .html(html)
            .build();

        var boxElement = KB.dom('div')
            .attr('id', 'modal-box')
            .style('width', width)
            .add(headerElement)
            .add(contentElement)
            .build();

        var overlayElement = KB.dom('div')
            .attr('id', 'modal-overlay')
            .add(boxElement)
            .build();

        if (overlayClickDestroy) {
            overlayElement.addEventListener('click', onOverlayClick, false);
        }

        document.body.appendChild(overlayElement);
        afterRendering();
    }

    function destroy() {
        isOpen = false;
        var overlayElement = KB.find('#modal-overlay');

        if (overlayElement) {
            KB.trigger('modal.beforeDestroy');

            overlayElement.remove();
        }
    }

    function getWidth(size) {
        var viewport = KB.utils.getViewportSize();

        if (viewport.width < 700) {
            return '99%';
        }

        switch (size) {
            case 'large':
                return viewport.width < 1350 ? '98%' : '1350px';
            case 'medium':
                return viewport.width < 1024 ? '70%' : '1024px';
        }

        return viewport.width < 800 ? '75%' : '800px';
    }

    KB.on('modal.close', function () {
        destroy();
    });

    KB.on('modal.submit', function () {
        submitForm();
    });

    KB.modal = {
        open: function (url, size, overlayClickDestroy) {
            KB.trigger('modal.open');

            _KB.get('Dropdown').close();
            destroy();

            if (typeof overlayClickDestroy === 'undefined') {
                overlayClickDestroy = true;
            }

            KB.http.get(url).success(function (response) {
                isOpen = true;
                create(response, getWidth(size), overlayClickDestroy);
            });
        },
        close: function () {
            destroy();
        },
        isOpen: function () {
            return isOpen;
        },
        replace: function (url) {
            KB.http.get(url).success(function (response) {
                replace(response);
            });
        },
        getForm: getForm,
        submitForm: submitForm
    };
}());
