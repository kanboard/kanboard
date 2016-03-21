Kanboard.Markdown = function(app) {
    this.app = app;
    this.editor = null;
};

Kanboard.Markdown.prototype.onPopoverOpened = function() {
    this.listen();
};

Kanboard.Markdown.prototype.onPopoverClosed = function() {
    this.listen();
};

Kanboard.Markdown.prototype.listen = function() {
    var editors = $(".markdown-editor");

    if (this.editor) {
        this.destroy();
    }

    if (editors.length > 0) {
        this.show(editors[0]);
    }
};

Kanboard.Markdown.prototype.destroy = function() {
    var cm = this.editor.codemirror;
    var wrapper = cm.getWrapperElement();

    for (var item in ["toolbar", "statusbar", "sideBySide"]) {
        if (this.editor.gui[item]) {
            wrapper.parentNode.removeChild(this.editor.gui[item]);
        }
    }

    cm.toTextArea();
    this.editor = null;
};

Kanboard.Markdown.prototype.show = function(textarea) {
    var toolbar = ["bold", "italic", "strikethrough", "heading", "|", "unordered-list", "ordered-list", "link", "|", "code", "table"];

    this.editor = new SimpleMDE({
        element: textarea,
        status: false,
        toolbarTips: false,
        autoDownloadFontAwesome: false,
        spellChecker: false,
        autosave: {
            enabled: false
        },
        forceSync: true,
        blockStyles: {
            italic: "_"
        },
        toolbar: textarea.hasAttribute("data-markdown-editor-disable-toolbar") ? false : toolbar,
        placeholder: textarea.getAttribute("placeholder")
    });
};
