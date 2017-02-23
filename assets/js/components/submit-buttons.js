KB.component('submit-buttons', function (containerElement, options) {
    var isLoading = false;
    var isDisabled = options.disabled || false;
    var submitLabel = options.submitLabel;
    var formActionElement = null;
    var submitButtonElement = null;

    function onSubmit() {
        isLoading = true;
        replaceButton();
        KB.trigger('modal.submit');
    }

    function onCancel() {
        KB.trigger('modal.close');
    }

    function onStop() {
        isLoading = false;
        replaceButton();
    }

    function onDisable() {
        isLoading = false;
        isDisabled = true;
        replaceButton();
    }

    function onEnable() {
        isLoading = false;
        isDisabled = false;
        replaceButton();
    }

    function onHide() {
        KB.dom(formActionElement).hide();
    }

    function onUpdateSubmitLabel(eventData) {
        submitLabel = eventData.submitLabel;
        replaceButton();
    }

    function buildButton() {
        var button = KB.dom('button')
            .attr('type', 'submit')
            .attr('class', 'btn btn-' + (options.color || 'blue'));

        if (KB.modal.isOpen()) {
            button.click(onSubmit);
        }

        if (options.tabindex) {
            button.attr('tabindex', options.tabindex);
        }

        if (isLoading) {
            button
                .disable()
                .add(KB.dom('i').attr('class', 'fa fa-spinner fa-pulse').build())
                .text(' ')
            ;
        }

        if (isDisabled) {
            button.disable();
        }

        return button
            .text(submitLabel)
            .build();
    }

    function replaceButton() {
        var buttonElement = buildButton();
        KB.dom(submitButtonElement).replace(buttonElement);
        submitButtonElement = buttonElement;
    }

    this.render = function () {
        KB.on('modal.stop', onStop);
        KB.on('modal.disable', onDisable);
        KB.on('modal.enable', onEnable);
        KB.on('modal.hide', onHide);
        KB.on('modal.submit.label', onUpdateSubmitLabel);

        KB.on('modal.close', function () {
            KB.removeListener('modal.stop', onStop);
            KB.removeListener('modal.disable', onDisable);
            KB.removeListener('modal.enable', onEnable);
            KB.removeListener('modal.hide', onHide);
            KB.removeListener('modal.submit.label', onUpdateSubmitLabel);
        });

        submitButtonElement = buildButton();

        var formActionElementBuilder = KB.dom('div')
            .attr('class', 'form-actions')
            .add(submitButtonElement);

        if (KB.modal.isOpen()) {
            formActionElementBuilder
                .text(' ' + options.orLabel + ' ')
                .add(KB.dom('a').attr('href', '#').click(onCancel).text(options.cancelLabel).build());
        }

        formActionElement = formActionElementBuilder.build();
        containerElement.appendChild(formActionElement);
    };
});
