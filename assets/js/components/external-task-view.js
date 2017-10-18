KB.component('external-task-view', function (containerElement, options) {

    this.render = function () {
        KB.dom(containerElement).html('<div id="external-task-view"><i class="fa fa-spinner fa-2x fa-pulse"></div>');

        $.ajax({
            cache: false,
            url: options.url,
            success: function(data) {
                KB.dom(containerElement).html('<div id="external-task-view">' + data + '</div>');
            }
        });
    };
});
