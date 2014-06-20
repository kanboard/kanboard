(function () {

    // Show popup
    function popover_show(content)
    {
        $("body").append('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');

        $("#popover-container").click(function() {
            $(this).remove();
        });

        $("#popover-content").click(function(e) {
            e.stopPropagation();
        });
    }

    $(".popover").click(function(e) {

        e.preventDefault();
        e.stopPropagation();

        $.get($(this).attr("href"), function(data) {
            popover_show(data);
        });
    });

    $("#form-date_due").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: 'yy-mm-dd'
    });

}());
