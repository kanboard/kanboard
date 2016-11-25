KB.component('external-task-view', function (containerElement, options) {

    this.render = function () {
        $.ajax({
            cache: false,
            url: options.url,
            success: function(data) {
                KB.dom(containerElement).html('<div id="external-task-view">' + data + '</div>');
            }
        });
    };
});
