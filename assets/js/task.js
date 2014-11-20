// Task related functions
Kanboard.Task = (function() {

    return {
        // Generate Markdown AJAX Request and show preview
        Preview: function(event) {
            // remember the clicked <a>
            var a = $(this);

            // now lets look for the closest div (which is the nav)
            // and then take the siblings being our write- and preview-area
            var nav = $(this).closest("div");
            var write = nav.siblings().filter(".write-area");
            var preview = nav.siblings().filter(".preview-area");

            // inside the <div> id=write-area is the textarea we need
            var textarea = write.find(":input").val();

            // do AJAX
            var request = $.ajax({
                type: "POST",
                url:  "?controller=app&action=preview",
                data: { text: textarea },
                dataType: "html",
            });

            // work on AJAX positive return
            request.done(function(msg){
                // change the tabs
                nav.find("a").removeClass("form-tab-selected")
                // change the layout of the <a> just clicked
                a.addClass("form-tab-selected");

            // give the preview area the CSS min-height of the height of the write area
            preview.css("min-height", write.css("height"));

            // hide the textarea, show the preview
            write.hide();
            preview.show();

            // insert data into previews lowest element (there may be some divs inside...)
            preview.find("div:last").html(msg);
            });

            // work on AJAX negative return
            request.fail(function(jqXHR, textStatus){
                alert( "Request failed: " + textStatus );
            });
        },

        // Return to Textarea
        Textarea: function(event) {
            // now lets look for the closest div (which is the nav)
            // and then take the siblings being our write- and preview-area
            var nav = $(this).closest("div");

            // change the tabs
            nav.find("a").removeClass("form-tab-selected")
                // change the layout of the <a> just clicked
                $(this).addClass("form-tab-selected");

            // show the textarea, hide the preview
            nav.siblings().filter(".write-area").show();
            nav.siblings().filter(".preview-area").hide();
        },

        Init: function() {
            // Image preview for attachments
            $(".file-popover").click(Kanboard.Popover);
        }
    };

})();
