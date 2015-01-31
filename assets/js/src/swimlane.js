Kanboard.Swimlane = (function() {

    // Reload the swimlane states (shown/hidden) after an ajax call
    jQuery(document).ajaxComplete(function() {
	var hiddenlist = getAllHidden();
	hiddenlist.map(function(swimlaneid) {
	    collapse(swimlaneid);
	    ;
	})
    });

    // Reload the swimlane states (shown/hidden) after page refresh
    jQuery(document).ready(function() {
	if (!localStorage.hiddenSwimlaneIds) {
	    localStorage.hiddenSwimlaneIds = JSON.stringify([]);
	} else {
	    var hiddenlist = getAllHidden();
	    hiddenlist.map(function(swimlaneid) {
		collapse(swimlaneid);
	    })
	}
    });

    // Clicking on Show/Hide icon fires this.
    jQuery(document).on('click', ".row_toggler", function(e) {
	e.preventDefault();
	var swimlaneid = $(this).attr('row_id');

	if (isHidden(swimlaneid) > -1) {
	    unhide(swimlaneid);
	    expand(swimlaneid);
	} else {
	    hide(swimlaneid);
	    collapse(swimlaneid);
	}
    });

    // Expand a Swimlane via display attributes
    function expand(swimlaneid) {
	$('.cell_' + swimlaneid).css('display', 'table-row');
	$('.show_' + swimlaneid).css('display', 'none');
	$('.hide_' + swimlaneid).css('display', 'inline');
    }

    // Collapse a Swimlane via display attributes
    function collapse(swimlaneid) {
	$('.cell_' + swimlaneid).css('display', 'none');
	$('.show_' + swimlaneid).css('display', 'inline');
	$('.hide_' + swimlaneid).css('display', 'none');
    }

    // Add swimlane Id to the hidden list and stores the list to localStorage
    function hide(id) {
	var hiddenSwimlaneIds = JSON.parse(localStorage.hiddenSwimlaneIds);
	hiddenSwimlaneIds.push(id);
	localStorage.hiddenSwimlaneIds = JSON.stringify(hiddenSwimlaneIds);
    }

    // Remove swimlane Id from the hidden list and stores the list to
    // localStorage
    function unhide(id) {
	var hiddenSwimlaneIds = JSON.parse(localStorage.hiddenSwimlaneIds);
	var index = hiddenSwimlaneIds.indexOf(id);
	if (index > -1) {
	    hiddenSwimlaneIds.splice(index, 1);
	}
	localStorage.hiddenSwimlaneIds = JSON.stringify(hiddenSwimlaneIds);
    }

    // Check if swimlane Id is hidden. Anything > -1 means hidden.
    function isHidden(id) {
	var hiddenSwimlaneIds = JSON.parse(localStorage.hiddenSwimlaneIds);
	return hiddenSwimlaneIds.indexOf(id);
    }

    // Gets all swimlane Ids that are hidden
    function getAllHidden() {
	return JSON.parse(localStorage.hiddenSwimlaneIds);
    }

})();
