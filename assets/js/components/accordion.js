KB.onClick('.accordion-toggle', function(e) {
    var section = KB.dom(e.target).parent('.accordion-section');

    if (section) {
        KB.dom(section).toggleClass('accordion-collapsed');
        KB.dom(KB.dom(section).find('.accordion-content')).toggle();
    }
});
