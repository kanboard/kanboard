KB.onClick('.js-reply-to-comment, .fa-reply', function (e) {
    var commentId = parseInt($(e.target).parents('li[data-comment-id]').attr('data-comment-id'));

    var commentReplyTemplate = document.querySelector('#comment-reply-content-' + commentId);
    if (! commentReplyTemplate) {
        return false;
    }
    var commentTextContent = commentReplyTemplate.content.querySelector("textarea").textContent;

    var textarea = document.querySelector(".text-editor textarea[name=comment]");
    textarea.value += commentTextContent + '\n\n';

    var $editorContainer = $(textarea).parents('.text-editor');

    // The text editor gives us no way to refresh the preview mode. We have to simulate it by triggering a click
    // on the edit button and then on the preview button to do so.

    // We are in edit mode, so we are fine
    if ($editorContainer.find('.text-editor-view-mode').is(':hidden')) {
        textarea.focus();
        return false;
    }

    var $editButton = $editorContainer.find('.text-editor-toolbar a:has(> i.fa-pencil-square-o)');
    if ($editButton.length === 0) {
        console.error('Could not find the edit button');
        return false;
    }
    $editButton[0].click();

    var $previewButton = $editorContainer.find('.text-editor-toolbar a:has(> i.fa-eye)');
    if ($previewButton.length === 0) {
        console.error('Could not find the preview button');
        return false;
    }
    $previewButton[0].click();

    return false;
});
