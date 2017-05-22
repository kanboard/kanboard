Kanboard.TaskAssigneeDropdown = function(app) {
    this.app = app;
};

Kanboard.TaskAssigneeDropdown.prototype.listen = function() {
    var self = this;

    $(document).on("click", ".task-assignee-dropdown-item", function (e) {
        e.preventDefault();

        var $this = $(this);
        self.task_id = $this.data('task_id');
        self.owner_id = $this.data('owner_id');

        $('#task-assignee-form-task-' + self.task_id + ' #form-owner_id').val(self.owner_id);

        self.submitForm();
    });
}

Kanboard.TaskAssigneeDropdown.prototype.getForm = function() {
    var self = this;

    return document.querySelector('#task-assignee-form-task-' + self.task_id);
}

Kanboard.TaskAssigneeDropdown.prototype.submitForm = function() {
    var self = this;

    var form = self.getForm();

    if (form) {
        var url = form.getAttribute('action');

        if (url) {
            KB.http.postForm(url, form).success(function (response) {
                if (response.hasOwnProperty('message')) {
                    window.alert(response.message);
                }
            }).error(function (response) {
                if (response.hasOwnProperty('message')) {
                    window.alert(response.message);
                }
            }); 
        }
    }
} 
