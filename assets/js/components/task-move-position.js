KB.component('task-move-position', function (containerElement, options) {

    function getSelectedValue(id) {
        var element = KB.dom(document).find('#' + id);

        if (element) {
            return parseInt(element.options[element.selectedIndex].value);
        }

        return null;
    }

    function getSwimlaneId() {
        var swimlaneId = getSelectedValue('form-swimlanes');
        return swimlaneId === null ? options.board[0].id : swimlaneId;
    }

    function getColumnId() {
        var columnId = getSelectedValue('form-columns');
        return columnId === null ? options.board[0].columns[0].id : columnId;
    }

    function getPosition() {
        var position = getSelectedValue('form-position');
        return position === null ? 1 : position;
    }

    function getPositionChoice() {
        var element = KB.find('input[name=positionChoice]:checked');

        if (element) {
            return element.value;
        }

        return 'before';
    }

    function onSwimlaneChanged() {
        var columnSelect = KB.dom(document).find('#form-columns');
        KB.dom(columnSelect).replace(buildColumnSelect());

        var taskSection = KB.dom(document).find('#form-tasks');
        KB.dom(taskSection).replace(buildTasks());
    }

    function onColumnChanged() {
        var taskSection = KB.dom(document).find('#form-tasks');
        KB.dom(taskSection).replace(buildTasks());
    }

    function onError(message) {
        KB.trigger('modal.stop');

        KB.find('#message-container')
            .replace(KB.dom('div')
                .attr('id', 'message-container')
                .attr('class', 'alert alert-error')
                .text(message)
                .build()
            );
    }

    function onSubmit() {
        var position = getPosition();
        var positionChoice = getPositionChoice();

        if (positionChoice === 'after') {
            position++;
        }

        KB.find('#message-container').replace(KB.dom('div').attr('id', 'message-container').build());

        KB.http.postJson(options.saveUrl, {
            "column_id": getColumnId(),
            "swimlane_id": getSwimlaneId(),
            "position": position
        }).error(function (response) {
            if (response) {
                onError(response.message);
            }
        });
    }

    function buildSwimlaneSelect() {
        var swimlanes = [];

        options.board.forEach(function(swimlane) {
            var option = {'value': swimlane.id, 'text': swimlane.name};
            if(swimlane.id == options.task.swimlane_id) {
                option.selected = "";
            }
            swimlanes.push(option);
        });

        return KB.dom('select')
            .attr('id', 'form-swimlanes')
            .change(onSwimlaneChanged)
            .for('option', swimlanes)
            .build();
    }

    function buildColumnSelect() {
        var columns = [];
        var swimlaneId = getSwimlaneId();

        options.board.forEach(function(swimlane) {
            if (swimlaneId === swimlane.id) {
                swimlane.columns.forEach(function(column) {
                    var option = {'value': column.id, 'text': column.title};
                    if(column.id == options.task.column_id) {
                        option.selected = "";
                    }
                    columns.push(option);
                });
            }
        });

        return KB.dom('select')
            .attr('id', 'form-columns')
            .change(onColumnChanged)
            .for('option', columns)
            .build();
    }

    function buildTasks() {
        var tasks = [];
        var swimlaneId = getSwimlaneId();
        var columnId = getColumnId();
        var container = KB.dom('div').attr('id', 'form-tasks');

        options.board.forEach(function (swimlane) {
            if (swimlaneId === swimlane.id) {
                swimlane.columns.forEach(function (column) {
                    if (columnId === column.id) {
                        column.tasks.forEach(function (task) {
                            tasks.push({'value': task.position, 'text': '#' + task.id + ' - ' + task.title});
                        });
                    }
                });
            }
        });

        if (tasks.length > 0) {
            container
                .add(KB.html.label(options.positionLabel, 'form-position'))
                .add(KB.dom('select').attr('id', 'form-position').for('option', tasks).build())
                .add(KB.html.radio(options.beforeLabel, 'positionChoice', 'before'))
                .add(KB.html.radio(options.afterLabel, 'positionChoice', 'after'))
            ;
        }

        return container.build();
    }

    this.render = function () {
        KB.on('modal.submit', onSubmit);
        KB.on('modal.close', function () {
            KB.removeListener('modal.submit', onSubmit);
        });

        var form = KB.dom('div')
            .add(KB.dom('div').attr('id', 'message-container').build())
            .add(KB.html.label(options.swimlaneLabel, 'form-swimlanes'))
            .add(buildSwimlaneSelect())
            .add(KB.html.label(options.columnLabel, 'form-columns'))
            .add(buildColumnSelect())
            .add(buildTasks())
            .build();

        containerElement.appendChild(form);
    };
});
