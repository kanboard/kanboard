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
};
