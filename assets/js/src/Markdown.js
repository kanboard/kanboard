function Markdown() {
}

Markdown.prototype.showPreview = function(e) {
    e.preventDefault();

    var link = $(this);
    var nav = $(this).closest("ul");
    var write = $(".write-area");
    var preview = $(".preview-area");
    var textarea = $("textarea");

    var request = $.ajax({
        url: "?controller=app&action=preview", // TODO: remoe harcoded url
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
};

Markdown.prototype.showWriter = function(e) {
    e.preventDefault();

    $(this).closest("ul").find("li").removeClass("form-tab-selected")
    $(this).parent().addClass("form-tab-selected");

    $(".write-area").show();
    $(".preview-area").hide();
};

Markdown.prototype.listen = function() {
    $(document).on("click", "#markdown-preview", this.showPreview.bind(this));
    $(document).on("click", "#markdown-write", this.showWriter.bind(this));
};
