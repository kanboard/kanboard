KB.component('text-editor', function (containerElement, options) {
    var textarea, viewModeElement, writeModeElement, previewElement, selectionStart, selectionEnd;

    this.render = function() {
        writeModeElement = buildWriteMode();
        viewModeElement = buildViewMode();

        containerElement.appendChild(KB.dom('div')
            .attr('class', 'text-editor')
            .add(viewModeElement)
            .add(writeModeElement)
            .build());

        if (options.autofocus) {
            textarea.focus();
        }
    };

    function buildViewMode() {
        var toolbarElement = KB.dom('div')
            .attr('class', 'text-editor-toolbar')
            .for('a', [
                {href: '#', html: '<i class="fa fa-pencil-square-o fa-fw"></i> ' + options.labelWrite, click: function() { toggleViewMode(); }}
            ])
            .build();

        previewElement = KB.dom('div')
            .attr('class', 'text-editor-preview-area markdown')
            .build();

        return KB.dom('div')
            .attr('class', 'text-editor-view-mode')
            .add(toolbarElement)
            .add(previewElement)
            .hide()
            .build();
    }

    function buildWriteMode() {
        var toolbarElement = KB.dom('div')
            .attr('class', 'text-editor-toolbar')
            .for('a', [
                {href: '#', html: '<i class="fa fa-eye fa-fw"></i> ' + options.labelPreview, click: function() { toggleViewMode(); }},
                {href: '#', html: '<i class="fa fa-bold fa-fw"></i>', click: function() { insertEnclosedTag('**'); }},
                {href: '#', html: '<i class="fa fa-italic fa-fw"></i>', click: function() { insertEnclosedTag('_'); }},
                {href: '#', html: '<i class="fa fa-strikethrough fa-fw"></i>', click: function() { insertEnclosedTag('~~'); }},
                {href: '#', html: '<i class="fa fa-link fa-fw"></i>', click: function() { insertLinkTag(); }},
                {href: '#', html: '<i class="fa fa-quote-right fa-fw"></i>', click: function() { insertPrependTag('> '); }},
                {href: '#', html: '<i class="fa fa-list-ul fa-fw"></i>', click: function() { insertPrependTag('* '); }},
                {href: '#', html: '<i class="fa fa-code fa-fw"></i>', click: function() { insertBlockTag('```'); }}
            ])
            .build();

        var textareaElement = KB.dom('textarea');
        textareaElement.attr('name', options.name);

        if (options.tabindex) {
            textareaElement.attr('tabindex', options.tabindex);
        }

        if (options.required) {
            textareaElement.attr('required', 'required');
        }

        // Order is important for IE11 (especially for the placeholder)
        var textWrapper = KB.dom(containerElement).find('script');
        textareaElement.html(textWrapper.innerHTML);

        if (options.placeholder) {
            textareaElement.attr('placeholder', options.placeholder);
        }

        textarea = textareaElement.build();

        if (options.suggestOptions) {
            KB.getComponent('suggest-menu', textarea, options.suggestOptions).render();
        }

        return KB.dom('div')
            .attr('class', 'text-editor-write-mode')
            .add(toolbarElement)
            .add(textarea)
            .build();
    }

    function toggleViewMode() {
        $.ajax({
            cache: false,
            type: 'POST',
            url: options.previewUrl,
            data: { 'text': textarea.value},
            success: function(data) {
                KB.dom(previewElement).html(data);
            }
        });

        KB.dom(viewModeElement).toggle();
        KB.dom(writeModeElement).toggle();
    }

    function getSelectedText() {
        return textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
    }

    function replaceTextRange(s, start, end, substitute) {
        return s.substring(0, start) + substitute + s.substring(end);
    }

    function insertEnclosedTag(tag) {
        var selectedText = getSelectedText();

        insertText(tag + selectedText + tag);
        setCursorBeforeClosingTag(tag);
    }

    function insertBlockTag(tag) {
        var selectedText = getSelectedText();

        insertText('\n' + tag + '\n' + selectedText + '\n' + tag);
        setCursorBeforeClosingTag(tag, 2);
    }

    function insertPrependTag(tag) {
        var selectedText = getSelectedText();

        if (selectedText.indexOf('\n') === -1) {
            insertText('\n' + tag + selectedText);
        } else {
            var lines = selectedText.split('\n');

            for (var i = 0; i < lines.length; i++) {
                if (lines[i].indexOf(tag) === -1) {
                    lines[i] = tag + lines[i];
                }
            }

            insertText(lines.join('\n'));
        }

        setCursorBeforeClosingTag(tag, 1);
    }

    function insertLinkTag() {
        var selectedText = getSelectedText();
        var linkLabel = options.labelTitle;
        var linkUrl = 'http://...';
        var selectionStartOffset = 0;
        var selectionEndOffset = 0;

        if (selectedText.startsWith('http')) {
            linkUrl = selectedText;
            selectionStartOffset = -1 * (linkUrl.length + 3 + linkLabel.length);
            selectionEndOffset = selectionStartOffset + linkLabel.length;
        } else if (selectedText.length > 0) {
            linkLabel = selectedText;
            selectionStartOffset = -1 * (linkUrl.length + 1);
            selectionEndOffset = selectionStartOffset + linkUrl.length;
        }
        insertText('[' + linkLabel + '](' + linkUrl + ')');

        var selectionPosition = KB.utils.getSelectionPosition(textarea);
        var currentSelectionStart = selectionPosition.selectionStart;

        textarea.setSelectionRange(currentSelectionStart + selectionStartOffset, currentSelectionStart + selectionEndOffset);
    }

    function insertText(replacedText) {
        textarea.focus();

        var result = false;
        var selectionPosition = KB.utils.getSelectionPosition(textarea);

        selectionStart = selectionPosition.selectionStart;
        selectionEnd = selectionPosition.selectionEnd;

        if (document.queryCommandSupported('insertText')) {
            result = document.execCommand('insertText', false, replacedText);
        }

        if (! result) {
            try {
                document.execCommand('ms-beginUndoUnit');
            } catch (error) {}

            textarea.value = replaceTextRange(textarea.value, selectionStart, selectionEnd, replacedText);

            try {
                document.execCommand('ms-endUndoUnit');
            } catch (error) {}
        }
    }

    function setCursorBeforeClosingTag(tag, offset) {
        offset = offset || 0;
        var position = selectionEnd + tag.length + offset;
        textarea.setSelectionRange(position, position);
    }
});
