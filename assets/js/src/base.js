var Kanboard = (function() {

    jQuery(document).ready(function() {
        Kanboard.Init();
    });

    return {

        ShowLoadingIcon: function() {
            $("body").append('<span id="app-loading-icon">&nbsp;<i class="fa fa-spinner fa-spin"></i></span>');
        },

        HideLoadingIcon: function() {
            $("#app-loading-icon").remove();
        },

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
                    Kanboard.ClosePopover();
                });

                $("#popover-content").click(function(e) {
                    e.stopPropagation();
                });

                $(".close-popover").click(function(e) {
                    e.preventDefault();
                    Kanboard.ClosePopover();
                });

                Mousetrap.bindGlobal("esc", function() {
                    Kanboard.ClosePopover();
                });

                if (callback) {
                    callback();
                }
            });
        },

        ClosePopover: function() {
            $('#popover-container').remove();
            Kanboard.Screenshot.Destroy();
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

            // Chosen select
            $(".chosen-select").chosen({
                width: "200px",
                no_results_text: $(".chosen-select").data("notfound"),
                disable_search_threshold: 10
            });

            // Project select box
            $("#board-selector").chosen({
                width: 180,
                no_results_text: $("#board-selector").data("notfound")
            });

            $("#board-selector").change(function() {
                window.location = $(this).attr("data-board-url").replace(/PROJECT_ID/g, $(this).val());
            });

            // Check the session every 60s
            window.setInterval(Kanboard.CheckSession, 60000);

            // Submit form
            Mousetrap.bindGlobal("mod+enter", function() {
                $("form").submit();
            });

            // Open board selector
            Mousetrap.bind("b", function(e) {
                e.preventDefault();
                $('#board-selector').trigger('chosen:open');
            });

            // Focus to the search box
            Mousetrap.bind("f", function(e) {
                e.preventDefault();

                var input = document.getElementById("form-search");

                if (input) {
                    input.focus();
                }
            });

            // Switch view mode for projects: go to the board
            Mousetrap.bind("v b", function(e) {
                var link = $(".view-board");

                if (link.length) {
                    window.location = link.attr('href');
                }
            });

            // Switch view mode for projects: go to the calendar
            Mousetrap.bind("v c", function(e) {
                var link = $(".view-calendar");

                if (link.length) {
                    window.location = link.attr('href');
                }
            });

            // Switch view mode for projects: go to the listing
            Mousetrap.bind("v l", function(e) {
                var link = $(".view-listing");

                if (link.length) {
                    window.location = link.attr('href');
                }
            });

            // Place cursor at the end when focusing on the search box
            $(document).on("focus", "#form-search", function() {
                if ($("#form-search")[0].setSelectionRange) {
                   $('#form-search')[0].setSelectionRange($('#form-search').val().length, $('#form-search').val().length);
                }
            });

            // Filter helper for search
            $(document).on("click", ".filter-helper", function (e) {
               e.preventDefault();
               $("#form-search").val($(this).data("filter"));
               $("form.search").submit();
            });

            // Collapse sidebar
            $(document).on("click", ".sidebar-collapse", function (e) {
               e.preventDefault();
               $(".sidebar-container").addClass("sidebar-collapsed");
               $(".sidebar-expand").show();
               $(".sidebar h2").hide();
               $(".sidebar ul").hide();
               $(".sidebar-collapse").hide();
            });

            // Expand sidebar
            $(document).on("click", ".sidebar-expand", function (e) {
               e.preventDefault();
               $(".sidebar-container").removeClass("sidebar-collapsed");
               $(".sidebar-collapse").show();
               $(".sidebar h2").show();
               $(".sidebar ul").show();
               $(".sidebar-expand").hide();
            });

            // Reload page when a destination project is changed
            var reloading_project = false;
            $("select.task-reload-project-destination").change(function() {
                if (! reloading_project) {
                    $(".loading-icon").show();
                    reloading_project = true;
                    window.location = $(this).data("redirect").replace(/PROJECT_ID/g, $(this).val());
                }
            });

            // Datepicker translation
            $.datepicker.setDefaults($.datepicker.regional[$("body").data("js-lang")]);

            // Alert box fadeout
            $(".alert-fade-out").delay(4000).fadeOut(800, function() {
                $(this).remove();
            });

            Kanboard.InitAfterAjax();
        },

        InitAfterAjax: function() {

            // Popover
            $(document).on("click", ".popover", Kanboard.Popover);

            // Autofocus fields (html5 autofocus works only with page onload)
            $("[autofocus]").each(function(index, element) {
                $(this).focus();
            })

            // Datepicker
            $(".form-date").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                constrainInput: false
            });

            // Datetime picker
            $(".form-datetime").datetimepicker({
                controlType: 'select',
                oneLine: true,
                dateFormat: 'yy-mm-dd',
                // timeFormat: 'h:mm tt',
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
            $('.dropdown').not(".dropit").dropit({ triggerParentEl : "span" });

            // Task auto-completion
            if ($(".task-autocomplete").length) {

                if ($('.opposite_task_id').val() == '') {
                    $(".task-autocomplete").parent().find("input[type=submit]").attr('disabled','disabled');
                }

                $(".task-autocomplete").autocomplete({
                    source: $(".task-autocomplete").data("search-url"),
                    minLength: 1,
                    select: function(event, ui) {
                        var field = $(".task-autocomplete").data("dst-field");
                        $("input[name=" + field + "]").val(ui.item.id);

                        $(".task-autocomplete").parent().find("input[type=submit]").removeAttr('disabled');
                    }
                });
            }

            // Tooltip for column description
            $(".tooltip").tooltip({
                content: function() {
                    return '<div class="markdown">' + $(this).attr("title") + '</div>';
                },
                position: {
                    my: 'left-20 top',
                    at: 'center bottom+9',
                    using: function(position, feedback) {

                        $(this).css(position);

                        var arrow_pos = feedback.target.left + feedback.target.width / 2 - feedback.element.left - 20;

                        $("<div>")
                            .addClass("tooltip-arrow")
                            .addClass(feedback.vertical)
                            .addClass(arrow_pos < 1 ? "align-left" : "align-right")
                            .appendTo(this);
                    }
                }
            });

            // Screenshot
            if (Kanboard.Exists("screenshot-zone")) {
                Kanboard.Screenshot.Init();
            }
        }
    };

})();
