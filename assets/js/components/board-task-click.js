(function () {
    function redirectToTaskView(e) {
        var ignoreParentElement = KB.dom(e.target).parent('a, .task-board-change-assignee');
        if (ignoreParentElement) {
            return;
        }

        var taskElement = KB.dom(e.target).parent('.task-board');
        if (taskElement) {
            var taskUrl = KB.dom(taskElement).data('taskUrl');

            if (taskUrl) {
                window.location = taskUrl;
            }
        }
    }

    function openEditTask(e) {
        var baseElement = KB.dom(e.target).parent('.task-board-change-assignee');
        var url = KB.dom(baseElement).data('url');

        if (url) {
            KB.modal.open(url, 'medium', false);
        }
    }

    KB.onClick('.task-board *', redirectToTaskView, true);
    KB.onClick('.task-board-change-assignee *', openEditTask, true);
}());
