KB.component('confirm-buttons', function (containerElement, options) {
    var isLoading = false;

    function onSubmit() {
        isLoading = true;
        KB.find('#modal-confirm-button').replace(buildButton());
        KB.http.get(options.url);
    }

    function onCancel() {
        KB.trigger('modal.close');
    }

    function onStop() {
        isLoading = false;
        KB.find('#modal-confirm-button').replace(buildButton());
    }

    function buildButton() {
        var button = KB.dom('button')
            .click(onSubmit)
            .attr('id', 'modal-confirm-button')
            .attr('type', 'button')
            .attr('class', 'btn btn-red');

        if (isLoading) {
            button
                .disable()
                .add(KB.dom('i').attr('class', 'fa fa-spinner fa-pulse').build())
                .text(' ')
            ;
        }

        if (options.tabindex) {
            button.attr('tabindex', options.tabindex);
        }

        return button
            .text(options.submitLabel)
            .build();
    }

    this.render = function () {
        KB.on('modal.stop', onStop);
        KB.on('modal.close', function () {
            KB.removeListener('modal.stop', onStop);
        });

        var element = KB.dom('div')
            .attr('class', 'form-actions')
            .add(buildButton())
            .text(' ' + options.orLabel + ' ')
            .add(KB.dom('a').attr('href', '#').click(onCancel).text(options.cancelLabel).build())
            .build();

        containerElement.appendChild(element);
    };
});
