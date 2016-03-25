Kanboard.Tooltip = function(app) {
    this.app = app;
};

Kanboard.Tooltip.prototype.onBoardRendered = function() {
    this.execute();
};

Kanboard.Tooltip.prototype.execute = function() {
    $(".tooltip").tooltip({
        track: false,
        show: false,
        hide: false,
        position: {
            my: 'left-20 top',
            at: 'center bottom+9',
            using: function(position, feedback) {

                $(this).css(position);

                var arrow_pos = feedback.target.left + feedback.target.width / 2 - feedback.element.left - 20;

                $("<div>")
                    .addClass("tooltip-arrow")
                    .addClass(feedback.vertical)
                    .addClass(arrow_pos < 1 ? "align-left" : "align-right")
                    .appendTo(this);
            }
        },
        content: function() {
            var _this = this;
            var href = $(this).attr('data-href');

            if (! href) {
                return '<div class="markdown">' + $(this).attr("title") + '</div>';
            }

            $.get(href, function setTooltipContent(data) {
                var tooltip = $('.ui-tooltip:visible');

                $('.ui-tooltip-content:visible').html(data);

                // Clear previous position, it interferes with the updated position computation
                tooltip.css({ top: '', left: '' });

                // Remove arrow, it will be added when repositionning
                tooltip.children('.tooltip-arrow').remove();

                // Reposition the tooltip
                var position = $(_this).tooltip("option", "position");
                position.of = $(_this);
                tooltip.position(position);
            });

            return '<i class="fa fa-spinner fa-spin"></i>';
        }
    })
    .on("mouseenter", function() {
        var _this = this;
        $(this).tooltip("open");

        $(".ui-tooltip").on("mouseleave", function() {
            $(_this).tooltip('close');
        });
    }).on("mouseleave focusout", function(e) {
        e.stopImmediatePropagation();
        var _this = this;

        setTimeout(function() {
            if (! $(".ui-tooltip:hover").length) {
                $(_this).tooltip("close");
            }
        }, 100);
    });
};
