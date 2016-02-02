function Project() {
}

Project.prototype.listen = function() {
    $('.project-change-role').on('change', function() {
        $.ajax({
            cache: false,
            url: $(this).data('url'),
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "id": $(this).data('id'),
                "role": $(this).val()
            })
        });
    });

    $('#project-creation-form #form-src_project_id').on('change', function() {
        var srcProjectId = $(this).val();

        if (srcProjectId == 0) {
            $(".project-creation-options").hide();
        } else {
            $(".project-creation-options").show();
        }
    });
};
