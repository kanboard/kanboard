document.addEventListener("DOMContentLoaded", function () {

    function selectAllItems(event) {
        event.preventDefault();

        var items = document.querySelectorAll("input[data-list-item=selectable]");
        for (var i = 0; i < items.length; i++) {
            items[i].checked = true;
        }

        showActionMenu();
    }

    function unselectAllItems(event) {
        event.preventDefault();

        var items = document.querySelectorAll("input[data-list-item=selectable]");
        for (var i = 0; i < items.length; i++) {
            items[i].checked = false;
        }

        hideActionMenu();
    }

    function onItemChange(event) {
        var selectedItems = document.querySelectorAll("input[data-list-item=selectable]:checked");

        if (selectedItems.length > 0) {
            showActionMenu();
        }
    }

    function showActionMenu() {
        var element = document.querySelector(".list-item-actions");
        if (element) {
            element.classList.remove("list-item-action-hidden");
        }
    }

    function hideActionMenu() {
        var element = document.querySelector(".list-item-actions");
        if (element && ! element.classList.contains("list-item-action-hidden")) {
            element.classList.add("list-item-action-hidden");
        }
    }

    function onActionClick(event) {
        event.preventDefault();
        var selectedItems = document.querySelectorAll("input[data-list-item=selectable]:checked");
        var taskIDs = [];

        for (var i = 0; i < selectedItems.length; i++) {
            taskIDs.push(selectedItems[i].value);
        }

        var link = event.target.href + "&task_ids=" + taskIDs.join(",");
        KB.modal.open(link, "medium", true);
    }

    var selectAllLink = document.querySelector("a[data-list-item-selection=all]");
    if (selectAllLink) {
        selectAllLink.addEventListener("click", selectAllItems);
    }

    var unselectLink = document.querySelector("a[data-list-item-selection=none]");
    if (unselectLink) {
        unselectLink.addEventListener("click", unselectAllItems);
    }

    var items = document.querySelectorAll("input[data-list-item=selectable]");
    for (var i = 0; i < items.length; i++) {
        items[i].addEventListener("change", onItemChange);
    }

    KB.on('dropdown.afterRender', function () {
        var actionLinks = document.querySelectorAll("a[data-list-item-action=modal]");

        for (var i = 0; i < actionLinks.length; i++) {
            actionLinks[i].addEventListener("click", onActionClick, false);
        }
    });
});
