// Common functions
var Kanboard = (function() {

    return {

        // Return true if the element#id exists
        Exists: function(id) {
            if (document.getElementById(id)) {
                return true;
            }

            return false;
        },

        // Display a popup
        Popover: function(e, callback) {
            e.preventDefault();
            e.stopPropagation();

            var link = e.target.getAttribute("href");

            if (! link) {
                link = e.target.getAttribute("data-href");
            }

            if (link) {
                $.get(link, function(content) {

                    $("body").append('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');

                    $("#popover-container").click(function() {
                        $(this).remove();
                    });

                    $("#popover-content").click(function(e) {
                        e.stopPropagation();
                    });

                    if (callback) {
                        callback();
                    }
                });
            }
        },

        // Return true if the page is visible
        IsVisible: function() {

            var property = "";

            if (typeof document.hidden !== "undefined") {
                property = "visibilityState";
            } else if (typeof document.mozHidden !== "undefined") {
                property = "mozVisibilityState";
            } else if (typeof document.msHidden !== "undefined") {
                property = "msVisibilityState";
            } else if (typeof document.webkitHidden !== "undefined") {
                property = "webkitVisibilityState";
            }

            if (property != "") {
                return document[property] == "visible";
            }

            return true;
        },

        // Common init
        Init: function() {

            // Datepicker
            $(".form-date").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                constrainInput: false
            });

            // Project select box
            $("#board-selector").chosen({
                width: 180
            });

            $("#board-selector").change(function() {
                window.location = $(this).attr("data-board-url").replace(/%d/g, $(this).val());
            });
        }
    };

})();