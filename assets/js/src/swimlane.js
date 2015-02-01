Kanboard.Swimlane = (function() {

    // Expand a Swimlane via display attributes
    function expand(swimlaneId)
    {
        $('.swimlane-row-' + swimlaneId).css('display', 'table-row');
        $('.show-icon-swimlane-' + swimlaneId).css('display', 'none');
        $('.hide-icon-swimlane-' + swimlaneId).css('display', 'inline');
    }

    // Collapse a Swimlane via display attributes
    function collapse(swimlaneId)
    {
        $('.swimlane-row-' + swimlaneId).css('display', 'none');
        $('.show-icon-swimlane-' + swimlaneId).css('display', 'inline');
        $('.hide-icon-swimlane-' + swimlaneId).css('display', 'none');
    }

    // Add swimlane Id to the hidden list and stores the list to localStorage
    function hide(id)
    {
        var storageKey = "hidden_swimlanes_" + $("#board").data("project-id");
        var hiddenSwimlaneIds = JSON.parse(Kanboard.GetStorageItem(storageKey)) || [];

        hiddenSwimlaneIds.push(id);

        Kanboard.SetStorageItem(storageKey, JSON.stringify(hiddenSwimlaneIds));
    }

    // Remove swimlane Id from the hidden list and stores the list to
    // localStorage
    function unhide(id)
    {
        var storageKey = "hidden_swimlanes_" + $("#board").data("project-id");
        var hiddenSwimlaneIds = JSON.parse(Kanboard.GetStorageItem(storageKey)) || [];
        var index = hiddenSwimlaneIds.indexOf(id);

        if (index > -1) {
            hiddenSwimlaneIds.splice(index, 1);
        }

        Kanboard.SetStorageItem(storageKey, JSON.stringify(hiddenSwimlaneIds));
    }

    // Check if swimlane Id is hidden. Anything > -1 means hidden.
    function isHidden(id)
    {
        return getAllHidden().indexOf(id) > -1;
    }

    // Gets all swimlane Ids that are hidden
    function getAllHidden()
    {
        var storageKey = "hidden_swimlanes_" + $("#board").data("project-id");
        return JSON.parse(Kanboard.GetStorageItem(storageKey)) || [];
    }

    // Reload the swimlane states (shown/hidden) after an ajax call
    jQuery(document).ajaxComplete(function() {

        getAllHidden().map(function(swimlaneId) {
            collapse(swimlaneId);
        });
    });

    // Reload the swimlane states (shown/hidden) after page refresh
    jQuery(document).ready(function() {

        getAllHidden().map(function(swimlaneId) {
            collapse(swimlaneId);
        });
    });

    // Clicking on Show/Hide icon fires this.
    jQuery(document).on('click', ".board-swimlane-toggle", function(e) {
        e.preventDefault();

        var swimlaneId = $(this).data('swimlane-id');

        if (isHidden(swimlaneId)) {
            unhide(swimlaneId);
            expand(swimlaneId);
        }
        else {
            hide(swimlaneId);
            collapse(swimlaneId);
        }
    });

})();
