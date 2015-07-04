(function() {

    jQuery(document).ready(function() {

        $(document).on("click", ".filter-helper", function (e) {
           e.preventDefault();
           $("#form-search").val($(this).data("filter"));
           $("form.search").submit();
        });
    });

})();
