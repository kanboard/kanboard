function Accordion(app) {
    this.app = app;
}

Accordion.prototype.listen = function() {
    $(document).on("click", ".accordion-toggle", function(e) {
        e.preventDefault();
        var section = $(this).parents(".accordion-section");

        if (section.hasClass("accordion-collapsed")) {
            section.find(".accordion-content").show();
            section.removeClass("accordion-collapsed");
        } else {
            section.find(".accordion-content").hide();
            section.addClass("accordion-collapsed");
        }
    });
};
