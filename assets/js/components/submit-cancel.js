KB.component('submit-cancel', function (containerElement, options) {
    var isLoading = false;

    function onSubmit() {
        isLoading = true;
        KB.find('#modal-submit-button').replace(buildButton());
        KB.trigger('modal.submit');
    }

    function onCancel() {
        KB.trigger('modal.cancel');
        _KB.get('Popover').close();
    }

    function onStop() {
        isLoading = false;
        KB.find('#modal-submit-button').replace(buildButton());
    }

    function buildButton() {
        var button = KB.dom('button')
            .click(onSubmit)
            .attr('id', 'modal-submit-button')
            .attr('type', 'submit')
            .attr('class', 'btn btn-blue');

        if (isLoading) {
            button
                .disable()
                .add(KB.dom('i').attr('class', 'fa fa-spinner fa-pulse').build())
                .text(' ')
            ;
        }

        return button
            .text(options.submitLabel)
            .build();
    }

    this.render = function () {
        KB.on('modal.stop', onStop);

        var element = KB.dom('div')
            .attr('class', 'form-actions')
            .add(buildButton())
            .text(' ' + options.orLabel + ' ')
            .add(KB.dom('a').attr('href', '#').click(onCancel).text(options.cancelLabel).build())
            .build();

        containerElement.appendChild(element);
    };
});
