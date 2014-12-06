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

        // Generate Markdown preview
        MarkdownPreview: function(e) {

            e.preventDefault();

            var link = $(this);
            var nav = $(this).closest("ul");
            var write = $(".write-area");
            var preview = $(".preview-area");
            var textarea = $("textarea");

            var request = $.ajax({
                url: "?controller=app&action=preview",
                contentType: "application/json",
                type: "POST",
                processData: false,
                dataType: "html",
                data: JSON.stringify({
                    "text": textarea.val()
                }),
            });

            request.done(function(data) {

                nav.find("li").removeClass("form-tab-selected");
                link.parent().addClass("form-tab-selected");

                preview.find(".markdown").html(data)
                preview.css("height", textarea.css("height"));
                preview.css("width", textarea.css("width"));

                write.hide();
                preview.show();
            });
        },

        // Show the Markdown textarea
        MarkdownWriter: function(e) {

            e.preventDefault();

            $(this).closest("ul").find("li").removeClass("form-tab-selected")
            $(this).parent().addClass("form-tab-selected");

            $(".write-area").show();
            $(".preview-area").hide();
        },

        // Check session and redirect to the login page if not logged
        CheckSession: function() {

            if (! $(".form-login").length) {
                $.ajax({
                    cache: false,
                    url: $("body").data("status-url"),
                    statusCode: {
                        401: function(data) {
                            window.location = $("body").data("login-url");
                        }
                    }
                });
            }
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

            // Markdown Preview for textareas
            $("#markdown-preview").click(Kanboard.MarkdownPreview);
            $("#markdown-write").click(Kanboard.MarkdownWriter);

            // Check the session every 60s
            window.setInterval(Kanboard.CheckSession, 60000);

            // Auto-select input fields
            $(".auto-select").focus(function() {
                $(this).select();
            });
        }
    };

})();
