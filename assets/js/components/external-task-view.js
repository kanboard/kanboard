Vue.component('external-task-view', {
    props: ['url'],
    template: '<div id="external-task-view" v-show="content">{{{ content }}}</div>',
    data: function () {
        return {
            content: ''
        };
    },
    ready: function () {
        var self = this;
        $.ajax({
            cache: false,
            url: this.url,
            success: function(data) {
                self.content = data;
            }
        });
    }
});
