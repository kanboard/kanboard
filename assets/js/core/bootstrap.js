
document.addEventListener('DOMContentLoaded', function () {
    KB.render();
    KB.listen();
    KB.keyboardShortcuts();
    KB.tooltip();
    KB.trigger('dom.ready');
});
