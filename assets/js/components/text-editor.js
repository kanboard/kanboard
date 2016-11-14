Vue.component('texteditor', {
    props: ['text', 'name', 'labelPreview', 'labelWrite', 'placeholder', 'css', 'tabindex', 'required', 'autofocus'],
    template:
        '<div class="text-editor">' +
        '<div class="text-editor-toolbar">' +
        '<button v-if="!preview" v-on:click.prevent="togglePreview"><i class="fa fa-fw fa-eye"></i>{{ labelPreview }}</button>' +
        '<button v-if="preview" v-on:click.prevent="toggleEditor"><i class="fa fa-fw fa-pencil-square-o"></i>{{ labelWrite }}</button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertBoldTag"><i class="fa fa-bold fa-fw"></i></button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertItalicTag"><i class="fa fa-italic fa-fw"></i></button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertStrikethroughTag"><i class="fa fa-strikethrough fa-fw"></i></button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertQuoteTag"><i class="fa fa-quote-right fa-fw"></i></button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertBulletListTag"><i class="fa fa-list-ul fa-fw"></i></button>' +
        '<button :disabled="isPreview" v-on:click.prevent="insertCodeTag"><i class="fa fa-code fa-fw"></i></button>' +
        '</div>' +
        '<div v-show="!preview" class="text-editor-write-area">' +
        '<textarea ' +
            'v-model="text" ' +
            'name="{{ name }}" ' +
            'id="{{ getId }}" ' +
            'class="{{ css }}" ' +
            'tabindex="{{ tabindex }}" ' +
            ':autofocus="hasAutofocus" ' +
            'placeholder="{{ placeholder }}" ' +
        '></textarea>' +
        '</div>' +
        '<div v-show="preview" class="text-editor-preview-area markdown">{{{ renderedText }}}</div>' +
        '</div>'
    ,
    data: function() {
        return {
            id: null,
            preview: false,
            renderedText: '',
            textarea: null,
            selectionStart: 0,
            selectionEnd: 0
        };
    },
    ready: function() {
        this.textarea = document.getElementById(this.id);
    },
    computed: {
        hasAutofocus: function() {
            return this.autofocus === '1';
        },
        isPreview: function() {
            return this.preview;
        },
        getId: function() {
            if (! this.id) {
                var i = 0;
                var uniqueId;

                while (true) {
                    i++;
                    uniqueId = 'text-editor-textarea-' + i;

                    if (! document.getElementById(uniqueId)) {
                        break;
                    }
                }

                this.id = uniqueId;
            }

            return this.id;
        }
    },
    methods: {
        toggleEditor: function() {
            this.preview = false;
        },
        togglePreview: function() {
            this.preview = true;
            this.renderedText = marked(this.text, {sanitize: true});
        },
        insertBoldTag: function() {
            this.insertEnclosedTag('**');
        },
        insertItalicTag: function() {
            this.insertEnclosedTag('_');
        },
        insertStrikethroughTag: function() {
            this.insertEnclosedTag('~~');
        },
        insertQuoteTag: function() {
            this.insertPrependTag('> ');
        },
        insertBulletListTag: function() {
            this.insertPrependTag('* ');
        },
        insertCodeTag: function() {
            this.insertBlockTag('```');
        },
        replaceTextRange: function(s, start, end, substitute) {
            return s.substring(0, start) + substitute + s.substring(end);
        },
        getSelectedText: function() {
            return this.text.substring(this.textarea.selectionStart, this.textarea.selectionEnd);
        },
        insertEnclosedTag: function(tag) {
            var selectedText = this.getSelectedText();

            this.insertText(tag + selectedText + tag);
            this.setCursorBeforeClosingTag(tag);
        },
        insertPrependTag: function(tag) {
            var selectedText = this.getSelectedText();

            if (selectedText.indexOf('\n') === -1) {
                this.insertText('\n' + tag + selectedText);
            } else {
                var lines = selectedText.split('\n');

                for (var i = 0; i < lines.length; i++) {
                    if (lines[i].indexOf(tag) === -1) {
                        lines[i] = tag + lines[i];
                    }
                }

                this.insertText(lines.join('\n'));
            }
        },
        insertBlockTag: function(tag) {
            var selectedText = this.getSelectedText();

            this.insertText('\n' + tag + '\n' + selectedText + '\n' + tag);
            this.setCursorBeforeClosingTag(tag, 2);
        },
        insertText: function(replacedText) {
            var result = false;

            this.selectionStart = this.textarea.selectionStart;
            this.selectionEnd = this.textarea.selectionEnd;
            this.textarea.focus();

            if (document.queryCommandSupported('insertText')) {
                result = document.execCommand('insertText', false, replacedText);
            }

            if (! result) {
                try {
                    document.execCommand("ms-beginUndoUnit");
                } catch (error) {}

                this.textarea.value = this.replaceTextRange(this.text, this.textarea.selectionStart, this.textarea.selectionEnd, replacedText);

                try {
                    document.execCommand("ms-endUndoUnit");
                } catch (error) {}
            }
        },
        setCursorBeforeClosingTag: function(tag, offset) {
            var position = this.selectionEnd + tag.length + offset;
            this.textarea.setSelectionRange(position, position);
        }
    }
});
