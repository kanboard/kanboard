Kanboard.Accordion = function(app) {
    this.app = app;
};

Kanboard.Accordion.prototype.listen = function() {
    $(document).on("click", ".accordion-toggle", function(e) {
        var section = $(this).parents(".accordion-section");
        e.preventDefault();

        if (section.hasClass("accordion-collapsed")) {
            section.find(".accordion-content").show();
            section.removeClass("accordion-collapsed");
        } else {
            section.find(".accordion-content").hide();
            section.addClass("accordion-collapsed");
        }
    });
};
