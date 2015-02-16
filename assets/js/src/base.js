// Common functions
var Kanboard = (function() {

    jQuery(document).ready(function() {
        Kanboard.Init();
    });

    return {

        // Return true if the element#id exists
        Exists: function(id) {
            if (document.getElementById(id)) {
                return true;
            }

            return false;
        },

        // Open a popup on a link click
        Popover: function(e, callback) {
            e.preventDefault();
            e.stopPropagation();

            var link = e.target.getAttribute("href");

            if (! link) {
                link = e.target.getAttribute("data-href");
            }

            if (link) {
                Kanboard.OpenPopover(link, callback);
            }
        },

        // Display a popup
        OpenPopover: function(link, callback) {

            $.get(link, function(content) {

                $("body").append('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');

                $("#popover-container").click(function() {
                    $(this).remove();
                });

                $("#popover-content").click(function(e) {
                    e.stopPropagation();
                });

                $(".close-popover").click(function(e) {
                    e.preventDefault();
                    $('#popover-container').remove();
                });

                Mousetrap.bind("esc", function() {
                    $('#popover-container').remove();
                });

                if (callback) {
                    callback();
                }
            });
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

        // Save preferences in local storage
        SetStorageItem: function(key, value) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem(key, value);
            }
        },

        GetStorageItem: function(key) {

            if (typeof(Storage) !== "undefined") {
                return localStorage.getItem(key);
            }

            return '';
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
                })
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
                        401: function() {
                            window.location = $("body").data("login-url");
                        }
                    }
                });
            }
        },

        Init: function() {

            // Project select box
            $("#board-selector").chosen({
                width: 180
            });

            $("#board-selector").change(function() {
                window.location = $(this).attr("data-board-url").replace(/PROJECT_ID/g, $(this).val());
            });

            // Check the session every 60s
            window.setInterval(Kanboard.CheckSession, 60000);

            // Keyboard shortcuts
            Mousetrap.bindGlobal("mod+enter", function() {
                $("form").submit();
            });

            Mousetrap.bind("b", function(e) {
                e.preventDefault();
                $('#board-selector').trigger('chosen:open');
            });

            // Tooltip for column description
            $(".column-tooltip").tooltip({
                content: function() {
                    return '<div class="markdown">' + $(this).attr("title") + '</div>';
                }
            });

            $.datepicker.setDefaults($.datepicker.regional[$("body").data("js-lang")]);

            Kanboard.InitAfterAjax();
        },

        InitAfterAjax: function() {

            // Popover
            $(document).on("click", ".popover", Kanboard.Popover);

            // Datepicker
            $(".form-date").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                constrainInput: false
            });

            // Markdown Preview for textareas
            $("#markdown-preview").click(Kanboard.MarkdownPreview);
            $("#markdown-write").click(Kanboard.MarkdownWriter);

            // Auto-select input fields
            $(".auto-select").focus(function() {
                $(this).select();
            });

            // Dropdown
            $(".dropit-submenu").hide();
            $('.dropdown').not(".dropit").dropit();
        }
    };

})();
