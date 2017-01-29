
document.addEventListener('DOMContentLoaded', function () {
    KB.render();
    KB.listen();
    KB.keyboardShortcuts();
    KB.trigger('dom.ready');
});
