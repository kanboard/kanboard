// Task related functions
Kanboard.Task = (function() {

    return {
        Init: function() {
            // Image preview for attachments
            $(".file-popover").click(Kanboard.Popover);
        }
    };

})();