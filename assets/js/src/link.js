Kanboard.Link = (function() {

    function on_change() {
        if ($('.behaviour').prop('checked')) {
            $('.link-inverse-label').hide();
        }
        else {
            $('.link-inverse-label').show();
        }
    }

    jQuery(document).ready(function() {
        if (Kanboard.Exists("link-edit-section")) {
            on_change();
            $(".behaviour").click(on_change);
        }
    });

})();