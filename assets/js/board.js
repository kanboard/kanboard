(function () {

    function handleItemDragStart(e)
    {
        this.style.opacity = '0.4';

        dragSrcItem = this;
        dragSrcColumn = this.parentNode;

        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('text/plain', this.innerHTML);
    }

    function handleItemDragEnd(e)
    {
        // Restore styles
        removeOver();
        this.style.opacity = '1.0';

        dragSrcColumn = null;
        dragSrcItem = null;
    }

    function handleItemDragOver(e)
    {
        if (e.preventDefault) e.preventDefault();

        e.dataTransfer.dropEffect = 'copy';

        return false;
    }

    function handleItemDragEnter(e)
    {
        if (dragSrcItem != this) {
            removeOver();
            this.classList.add('over');
        }
    }

    function handleItemDrop(e)
    {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();

        // Drop the element if the item is not the same
        if (dragSrcItem != this) {

            var position = getItemPosition(this);
            var item = createItem(e.dataTransfer.getData('text/plain'));

            if (countColumnItems(this.parentNode) == position) {
                this.parentNode.appendChild(item);
            }
            else {
                this.parentNode.insertBefore(item, this);
            }

            dragSrcItem.parentNode.removeChild(dragSrcItem);

            saveBoard();
        }

        dragSrcColumn = null;
        dragSrcItem = null;

        return false;
    }


    function handleColumnDragOver(e)
    {
        if (e.preventDefault) e.preventDefault();

        e.dataTransfer.dropEffect = 'copy';

        return false;
    }

    function handleColumnDragEnter(e)
    {
        if (dragSrcColumn != this) {
            removeOver();
            this.classList.add('over');
        }
    }

    function handleColumnDrop(e)
    {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();

        // Drop the element if the column is not the same
        if (dragSrcColumn != this) {

            var item = createItem(e.dataTransfer.getData('text/plain'));
            this.appendChild(item);
            dragSrcColumn.removeChild(dragSrcItem);

            saveBoard();
        }

        return false;
    }

    function saveBoard()
    {
        var data = [];
        var projectId = document.getElementById("board").getAttribute("data-project-id");
        var cols = document.querySelectorAll('.column');

        [].forEach.call(cols, function(col) {

            var task_limit = col.getAttribute("data-task-limit");

            if (task_limit != "" && task_limit != "0") {

                task_limit = parseInt(task_limit);

                if (col.children.length > task_limit) {
                    col.classList.add("task-limit-warning");
                }
                else {
                    col.classList.remove("task-limit-warning");
                }

                var counter = document.getElementById("task-number-column-" + col.getAttribute("data-column-id"));
                if (counter) counter.innerHTML = col.children.length;
            }

            [].forEach.call(col.children, function(item) {

                data.push({
                    "task_id": item.firstElementChild.getAttribute("data-task-id"),
                    "position": getItemPosition(item),
                    "column_id": col.getAttribute("data-column-id")
                })
            });
        });

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "?controller=board&action=save&project_id=" + projectId, true);

        xhr.onreadystatechange = function(response) {

            if (this.readyState == this.DONE) {
                try {
                    var response = JSON.parse(this.responseText);

                    if (response.result == false) {
                        window.alert('Unable to update the board');
                    }
                    else if (response.refresh == true) {
                        window.location = "?controller=board&action=show&project_id=" + projectId;
                    }
                }
                catch (e) {}
            }
        };

        xhr.send(JSON.stringify(data));
    }

    function getItemPosition(element)
    {
        var i = 0;

        while ((element = element.previousSibling) != null) {

            if (element.nodeName == "DIV" && element.className == "draggable-item") {
                i++;
            }
        }

        return i + 1;
    }

    function countColumnItems(element)
    {
        return element.children.length;
    }

    function createItem(html)
    {
        var item = document.createElement("div");
        item.className = "draggable-item";
        item.draggable  = true;
        item.innerHTML = html;
        item.ondragstart = handleItemDragStart;
        item.ondragend = handleItemDragEnd;
        item.ondragenter = handleItemDragEnter;
        item.ondragover = handleItemDragOver;
        item.ondrop = handleItemDrop;

        return item;
    }

    function removeOver()
    {
        // Remove column over
        [].forEach.call(document.querySelectorAll('.column'), function (col) {
            col.classList.remove('over');
        });

        // Remove item over
        [].forEach.call(document.querySelectorAll('.draggable-item'), function (item) {
            item.classList.remove('over');
        });
    }

    // Drag and drop events

    var dragSrcItem = null;
    var dragSrcColumn = null;

    var items = document.querySelectorAll('.draggable-item');

    [].forEach.call(items, function(item) {
        item.addEventListener('dragstart', handleItemDragStart, false);
        item.addEventListener('dragend', handleItemDragEnd, false);
        item.addEventListener('dragenter', handleItemDragEnter, false);
        item.addEventListener('dragover', handleItemDragOver, false);
        item.addEventListener('drop', handleItemDrop, false);
    });

    var cols = document.querySelectorAll('.column');

    [].forEach.call(cols, function(col) {
        col.addEventListener('dragenter', handleColumnDragEnter, false);
        col.addEventListener('dragover', handleColumnDragOver, false);
        col.addEventListener('drop', handleColumnDrop, false);
    });

    [].forEach.call(document.querySelectorAll('[data-task-id]'), function (item) {
        item.addEventListener('click', function() {
            window.location.href = '?controller=task&action=show&task_id=' + item.getAttribute('data-task-id');
        });
    });

    // Filtering

    function getSelectedUserFilter()
    {
        var select = document.getElementById("form-user_id");
        return select.options[select.selectedIndex].value;
    }

    function hasDueDateFilter()
    {
        var dateFilter = document.getElementById("filter-due-date");
        return dateFilter.classList.contains("filter-on");
    }

    function applyFilter(selectedUserId, filterDueDate)
    {
        [].forEach.call(document.querySelectorAll('[data-task-id]'), function (item) {

            var ownerId = item.getAttribute("data-owner-id");
            var dueDate = item.getAttribute("data-due-date");

            if (ownerId != selectedUserId && selectedUserId != -1) {
                item.style.opacity = "0.2";
            }
            else {
                item.style.opacity = "1.0";
            }

            if (filterDueDate && (dueDate == "" || dueDate == "0")) {
                item.style.opacity = "0.2";
            }
        });
    }

    var userFilter = document.getElementById("form-user_id");
    var dateFilter = document.getElementById("filter-due-date");

    if (userFilter) {
        userFilter.onchange = function() {
            applyFilter(getSelectedUserFilter(), hasDueDateFilter());
        };
    }

    if (dateFilter) {

        dateFilter.onclick = function(e) {
            dateFilter.classList.toggle("filter-on");
            applyFilter(getSelectedUserFilter(), hasDueDateFilter());
            e.preventDefault();
        };
    }

}());
