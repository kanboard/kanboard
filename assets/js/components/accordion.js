KB.onClick('.accordion-toggle', function (e) {
    var sectionElement = KB.dom(e.target).parent('.accordion-section');

    if (sectionElement) {
        KB.dom(sectionElement).toggleClass('accordion-collapsed');
    }
});
