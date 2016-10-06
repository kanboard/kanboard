Vue.component('task-move-position', {
    props: ['board', 'saveUrl'],
    template: '#template-task-move-position',
    data: function () {
        return {
            swimlaneId: 0,
            columnId: 0,
            position: 1,
            columns: [],
            tasks: [],
            positionChoice: 'before'
        }
    },
    ready: function () {
        this.columns = this.board[0].columns;
        this.columnId = this.columns[0].id;
        this.tasks = this.columns[0].tasks;
    },
    methods: {
        onChangeSwimlane: function () {
            var self = this;
            this.columnId = 0;
            this.position = 1;
            this.columns = [];
            this.tasks = [];
            this.positionChoice = 'before';

            this.board.forEach(function(swimlane) {
                if (swimlane.id === self.swimlaneId) {
                    self.columns = swimlane.columns;
                    self.tasks = self.columns[0].tasks;
                    self.columnId = self.columns[0].id;
                }
            });
        },
        onChangeColumn: function () {
            var self = this;
            this.position = 1;
            this.tasks = [];
            this.positionChoice = 'before';

            this.columns.forEach(function(column) {
                if (column.id == self.columnId) {
                    self.tasks = column.tasks;

                    if (self.tasks.length > 0) {
                        self.position = parseInt(self.tasks[0]['position']);
                    }
                }
            });
        },
        onSubmit: function () {
            if (this.positionChoice == 'after') {
                this.position++;
            }

            $.ajax({
                cache: false,
                url: this.saveUrl,
                contentType: "application/json",
                type: "POST",
                processData: false,
                data: JSON.stringify({
                    "column_id": this.columnId,
                    "swimlane_id": this.swimlaneId,
                    "position": this.position
                }),
                complete: function() {
                    window.location.reload(true);
                }
            });
        }
    }
});
