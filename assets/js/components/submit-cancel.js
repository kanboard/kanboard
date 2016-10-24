Vue.component('submit-cancel', {
    props: ['labelButton', 'labelOr', 'labelCancel', 'callback'],
    template: '<div class="form-actions">' +
              '<button type="button" class="btn btn-blue" @click="onSubmit" :disabled="isLoading">' +
              '<span v-show="isLoading"><i class="fa fa-spinner fa-pulse"></i> </span>' +
              '{{ labelButton }}' +
              '</button> ' +
              '{{ labelOr }} <a href="#" v-on:click.prevent="onCancel">{{ labelCancel }}</a>' +
              '</div>'
    ,
    data: function () {
        return {
            loading: false
        };
    },
    computed: {
        isLoading: function () {
            return this.loading;
        }
    },
    methods: {
        onSubmit: function () {
            this.loading = true;
            this.callback();
        },
        onCancel: function () {
            _KB.get('Popover').close();
        }
    },
    events: {
        'submitCancelled': function() {
            this.loading = false;
        }
    }
});
