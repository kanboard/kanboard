function Markdown() {
}

Markdown.prototype.showPreview = function(e) {
    e.preventDefault();

    var write = $(".write-area");
    var preview = $(".preview-area");
    var textarea = $("textarea");

    $("#markdown-write").parent().removeClass("form-tab-selected");
    $("#markdown-preview").parent().addClass("form-tab-selected");

    var request = $.ajax({
        url: $("body").data("markdown-preview-url"),
        contentType: "application/json",
        type: "POST",
        processData: false,
        dataType: "html",
        data: JSON.stringify({
            "text": textarea.val()
        })
    });

    request.done(function(data) {
        preview.find(".markdown").html(data)
        preview.css("height", textarea.css("height"));
        preview.css("width", textarea.css("width"));

        write.hide();
        preview.show();
    });
};

Markdown.prototype.showWriter = function(e) {
    e.preventDefault();

    $("#markdown-write").parent().addClass("form-tab-selected");
    $("#markdown-preview").parent().removeClass("form-tab-selected");

    $(".write-area").show();
    $(".preview-area").hide();
};

Markdown.prototype.listen = function() {
    $(document).on("click", "#markdown-preview", this.showPreview.bind(this));
    $(document).on("click", "#markdown-write", this.showWriter.bind(this));
};
