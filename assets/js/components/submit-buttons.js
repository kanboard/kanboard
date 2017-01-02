KB.component('submit-buttons', function (containerElement, options) {
    var isLoading = false;
    var isDisabled = options.disabled || false;
    var submitLabel = options.submitLabel;
    var formActionElement = null;

    function onSubmit() {
        isLoading = true;
        KB.find('#modal-submit-button').replace(buildButton());
        KB.trigger('modal.submit');
    }

    function onCancel() {
        KB.trigger('modal.close');
    }

    function onStop() {
        isLoading = false;
        KB.find('#modal-submit-button').replace(buildButton());
    }

    function onDisable() {
        isLoading = false;
        isDisabled = true;
        KB.find('#modal-submit-button').replace(buildButton());
    }

    function onEnable() {
        isLoading = false;
        isDisabled = false;
        KB.find('#modal-submit-button').replace(buildButton());
    }

    function onHide() {
        KB.dom(formActionElement).hide();
    }

    function onUpdateSubmitLabel(eventData) {
        submitLabel = eventData.submitLabel;
        KB.find('#modal-submit-button').replace(buildButton());
    }

    function buildButton() {
        var button = KB.dom('button')
            .attr('id', 'modal-submit-button')
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

    this.render = function () {
        KB.on('modal.stop', onStop);
        KB.on('modal.disable', onDisable);
        KB.on('modal.enable', onEnable);
        KB.on('modal.hide', onHide);
        KB.on('modal.submit.label', onUpdateSubmitLabel);

        var formActionElementBuilder = KB.dom('div')
            .attr('class', 'form-actions')
            .add(buildButton());

        if (KB.modal.isOpen()) {
            formActionElementBuilder
                .text(' ' + options.orLabel + ' ')
                .add(KB.dom('a').attr('href', '#').click(onCancel).text(options.cancelLabel).build())
        }

        formActionElement = formActionElementBuilder.build();
        containerElement.appendChild(formActionElement);
    };
});
